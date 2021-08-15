<?php
//conn
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
$id_rincian = isset($_GET['id']) ? trim($_GET['id']) : '';
$id_transaksi = isset($_GET['t']) ? trim($_GET['t']) : '';
$keterangan = "id_trans/".$id_transaksi;
try {
  //get all data rincian
  // $get_data = $db->query("SELECT * FROM rincian_detail_obat rd INNER JOIN warehouse_out wo ON(wo.id_detail_rincian=rd.id_detail_rincian) WHERE rd.id_rincian='".$id_rincian."' AND rd.id_trans_obat='".$id_transaksi."'");
  // $rincian = $get_data->fetchAll(PDO::FETCH_ASSOC);
  // foreach ($rincian as $data) {
  //   $id_warehouse_stok = $data['id_warehouse_stok'];
  //   $volume = $data['volume'];
  //   //update stok
  //   $up_stok = $db->query("UPDATE warehouse_stok SET stok=stok-".$volume." WHERE id_warehouse_stok='".$id_warehouse_stok."'");
  // }
	//update aktif
	$up_aktif = $db->query("UPDATE kartu_stok_ruangan SET aktif='ya' WHERE keterangan LIKE '".$keterangan."'");
	//get Total
	$get_total = $db->query("SELECT SUM(sub_total) as total FROM rincian_detail_obat WHERE id_trans_obat='".$id_transaksi."'");
	$total = $get_total->fetch(PDO::FETCH_ASSOC);
	$total_trans = ceil($total['total']);
	$ratusan = substr($total_trans, -3);
	$total_harga=0;
	if($ratusan<450){
		$total_harga = $total_trans - $ratusan;
	}else if(($ratusan>=450)&&($ratusan<950)){
		$total_harga = ($total_trans - $ratusan)+500;
	}else{
		$total_harga = $total_trans + (1000-$ratusan);
	}
	$selisih = $total_harga - $total_trans;
	$up_selisih = $db->query("UPDATE rincian_transaksi_obat SET biaya_trans=".$total_trans.",biaya_bulat=".$total_harga.",selisih_pembulatan=".$selisih." WHERE id_trans_obat='".$id_transaksi."'");
  //redirect to front
  echo "<script language=\"JavaScript\">window.location = \"obat_ranap_trans.php?id=$id_rincian\"</script>";
} catch (PDOException $e) {
  echo "Fail To Execute : ".$e->getMessage();
}
