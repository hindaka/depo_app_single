<?php
session_start();
include("../inc/pdo.conf.php");
include("../inc/version.php");
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
$id_so = isset($_GET['sync']) ? $_GET['sync'] : '';
$tipe_depo = $tipes[2];
$conf = json_decode(file_get_contents("config/env_depo.json"),true);
$id_depo = $conf[$tipe_depo]["id_depo"];
$get_parent = $db->query("SELECT * FROM stok_opname WHERE id_so='".$id_so."'");
$parent = $get_parent->fetch(PDO::FETCH_ASSOC);
$tanggal_so = $parent['tanggal_so'];
$split = explode("-",$tanggal_so);
$tahun = $split[0];
$bulan = $split[1];
$mem_id = $r1['mem_id'];
//get data
$get_so = $db->query("SELECT * FROM stok_opname_det WHERE id_parent_so='".$id_so."'");
$all_data = $get_so->fetchAll(PDO::FETCH_ASSOC);
$i=1;
foreach ($all_data as $item) {
		$volume_sisa=0;
		$volume_out = 0;
		$in_out = 'masuk';
		$keterangan ='Stok Awal Setelah Stok Opnam';
		$aktif='ya';
		$tujuan ='stock_opname-v3-'.$id_so;
		$get_data = $db->query("SELECT * FROM kartu_stok_ruangan WHERE id_kartu_ruangan='".$item['id_kartu']."'");
		$data = $get_data->fetch(PDO::FETCH_ASSOC);
		$new_stok = $db->prepare("INSERT INTO `kartu_stok_ruangan`(`id_obat`, `id_warehouse`, `sumber_dana`, `volume_kartu_awal`, `volume_kartu_akhir`, `volume_sisa`, `in_out`, `tujuan`, `volume_in`, `volume_out`, `expired`, `no_batch`, `harga_beli`, `harga_jual`, `id_tuslah`, `ket_tuslah`, `aktif`, `keterangan`, `mem_id`)VALUES(:id_obat,:id_warehouse,:sumber_dana,:volume_kartu_awal,:volume_kartu_akhir,:volume_sisa,:in_out,:tujuan,:volume_in,:volume_out,:expired,:no_batch,:harga_beli,:harga_jual,:id_tuslah,:ket_tuslah,:aktif,:keterangan,:mem_id)");
		$new_stok->bindParam(":id_obat",$item['id_obat'],PDO::PARAM_INT);
		$new_stok->bindParam(":id_warehouse",$id_depo,PDO::PARAM_INT);
		$new_stok->bindParam(":sumber_dana",$data['sumber_dana'],PDO::PARAM_STR);
		$new_stok->bindParam(":volume_kartu_awal",$item['fisik'],PDO::PARAM_INT);
		$new_stok->bindParam(":volume_kartu_akhir",$item['fisik'],PDO::PARAM_INT);
		$new_stok->bindParam(":volume_sisa",$volume_sisa,PDO::PARAM_INT);
		$new_stok->bindParam(":in_out",$in_out,PDO::PARAM_STR);
		$new_stok->bindParam(":tujuan",$tujuan,PDO::PARAM_STR);
		$new_stok->bindParam(":volume_in",$item['fisik'],PDO::PARAM_INT);
		$new_stok->bindParam(":volume_out",$volume_out,PDO::PARAM_INT);
		$new_stok->bindParam(":expired",$item['expired'],PDO::PARAM_STR);
		$new_stok->bindParam(":no_batch",$item['no_batch'],PDO::PARAM_STR);
		$new_stok->bindParam(":harga_beli",$item['harga_beli'],PDO::PARAM_INT);
		$new_stok->bindParam(":harga_jual",$item['harga_jual'],PDO::PARAM_INT);
		$new_stok->bindParam(":id_tuslah",$data['id_tuslah'],PDO::PARAM_INT);
		$new_stok->bindParam(":ket_tuslah",$data['ket_tuslah'],PDO::PARAM_INT);
		$new_stok->bindParam(":aktif",$aktif,PDO::PARAM_STR);
		$new_stok->bindParam(":keterangan",$keterangan,PDO::PARAM_STR);
		$new_stok->bindParam(":mem_id",$mem_id,PDO::PARAM_INT);
		$new_stok->execute();
		echo $i++.'<br>'.$item['id_det_so'];
}
$db->query("UPDATE stok_opname SET sync='y' WHERE id_so='".$id_so."'");
header("location: stok_opnam_depo_sync_list.php?bulan=".$bulan."&tahun=".$tahun."&status=1");
