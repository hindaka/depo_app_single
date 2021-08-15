<?php
session_start();
include("../inc/pdo.conf.php");
include("../inc/version.php");
date_default_timezone_set("Asia/Jakarta");
$namauser = $_SESSION['namauser'];
$password = $_SESSION['password'];
$tipe = $_SESSION['tipe'];
$tipes = explode('-',$tipe);
if ($tipes[0]!='DepoApp')
{
	unset($_SESSION['tipe']);
	unset($_SESSION['namauser']);
	unset($_SESSION['password']);
	header("location:../index.php?status=2");
	exit;
}
include "../inc/anggota_check.php";
$tipe_depo = $tipes[2];
$conf = json_decode(file_get_contents("config/env_depo.json"),true);
$id_depo = $conf[$tipe_depo]["id_depo"];
$id_conf_obat = isset($_GET['penyimpanan']) ? $_GET['penyimpanan'] : '';
$tanggal_so = isset($_GET['tanggal_so']) ? $_GET['tanggal_so'] : date("Y-m-d");
$split = explode("/",$tanggal_so);
$new_date= $split[2]."-".$split[1]."-".$split[0];
$set_stok="on";
$mem_id = $r1['mem_id'];
$data_penyimpanan = $db->query("SELECT * FROM conf_penyimpanan_obat WHERE id_conf_obat='".$id_conf_obat."'");
$dp= $data_penyimpanan->fetch(PDO::FETCH_ASSOC);
$pj= $dp['pj'];
$penyimpanan = $dp['nama_penyimpanan'];
$search_pegawai = $db->query("SELECT id_pegawai FROM pegawai WHERE nama LIKE '".$pj."'");
$peg = $search_pegawai->fetch(PDO::FETCH_ASSOC);
//insert parent SO
$parent = $db->prepare("INSERT INTO `stok_opname`(`id_warehouse`, `tanggal_so`, `set_stok`,`petugas_so`,`penyimpanan`, `mem_id`) VALUES (:id_warehouse,:tanggal_so,:set_stok,:petugas_so,:penyimpanan,:mem_id)");
$parent->bindParam(":id_warehouse",$id_depo,PDO::PARAM_INT);
$parent->bindParam(":tanggal_so",$new_date,PDO::PARAM_STR);
$parent->bindParam(":set_stok",$set_stok,PDO::PARAM_STR);
$parent->bindParam(":petugas_so",$peg['id_pegawai'],PDO::PARAM_STR);
$parent->bindParam(":penyimpanan",$penyimpanan,PDO::PARAM_STR);
$parent->bindParam(":mem_id",$mem_id,PDO::PARAM_INT);
$parent->execute();
$id_parent = $db->lastInsertId();
$ins_detail = $db->prepare("INSERT INTO `stok_opname_det`(`id_parent_so`,`id_kartu`,`id_obat`, `no_batch`, `expired`,`harga_beli`,`harga_jual`, `stok_sistem`) VALUES (:id_parent,:id_kartu,:id_obat,:no_batch,:expired,:beli,:jual,:stok_sistem)");
//get data SO
$get_all_data = $db->query("SELECT g.no_urut_depo_igd,ks.id_kartu_ruangan,g.id_obat,ks.no_batch,ks.expired,SUM(ks.volume_kartu_akhir) as sisa_stok,ks.harga_beli,ks.harga_jual FROM kartu_stok_ruangan ks INNER JOIN gobat g ON(ks.id_obat=g.id_obat) WHERE ks.in_out='masuk' AND ks.id_warehouse='".$id_depo."' AND ks.volume_kartu_akhir>0 AND YEAR(ks.created_at)='".$split[2]."' AND g.lemari_depo_igd='".$id_conf_obat."' GROUP BY ks.id_obat,ks.no_batch,ks.harga_beli ORDER BY g.no_urut_depo_igd,g.id_obat ASC");
$all_data = $get_all_data->fetchAll(PDO::FETCH_ASSOC);
foreach ($all_data as $ad) {
  $id_kartu = $ad['id_kartu_ruangan'];
  $id_obat = $ad['id_obat'];
  $no_batch = $ad['no_batch'];
  $expired = $ad['expired'];
  $stok_sistem = $ad['sisa_stok'];
	$harga_beli = $ad['harga_beli'];
	$harga_jual = $ad['harga_jual'];
  $ins_detail->bindParam(":id_parent",$id_parent,PDO::PARAM_INT);
  $ins_detail->bindParam(":id_kartu",$id_kartu,PDO::PARAM_INT);
  $ins_detail->bindParam(":id_obat",$id_obat,PDO::PARAM_INT);
  $ins_detail->bindParam(":no_batch",$no_batch,PDO::PARAM_STR);
  $ins_detail->bindParam(":expired",$expired,PDO::PARAM_STR);
	$ins_detail->bindParam(":beli",$harga_beli,PDO::PARAM_INT);
	$ins_detail->bindParam(":jual",$harga_jual,PDO::PARAM_INT);
  $ins_detail->bindParam(":stok_sistem",$stok_sistem,PDO::PARAM_INT);
  $ins_detail->execute();
}

// redirect
header("location: stok_opnam_depo_cetak.php?ctx=".$id_parent);
