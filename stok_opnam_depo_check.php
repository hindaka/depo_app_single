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
$tipe_depo = $tipes[2];
$conf = json_decode(file_get_contents("config/env_depo.json"),true);
$id_depo = $conf[$tipe_depo]["id_depo"];
$penyimpanan = isset($_GET['penyimpanan']) ? $_GET['penyimpanan'] : '';
$tanggal_so = isset($_GET['tanggal_so']) ? $_GET['tanggal_so'] : '';
$split = explode("/",$tanggal_so);
$tahun = $split[2];
$bulan = $split[1];

$get_penyimpanan = $db->query("SELECT * FROM conf_penyimpanan_obat WHERE id_conf_obat='".$penyimpanan."'");
$data = $get_penyimpanan->fetch(PDO::FETCH_ASSOC);
$nama_penyimpanan = $data['nama_penyimpanan'];
// check
$get_check = $db->query("SELECT COUNT(*) as total FROM stok_opname WHERE id_warehouse='".$id_depo."' AND YEAR(tanggal_so)='".$tahun."' AND MONTH(tanggal_so)='".$bulan."' AND penyimpanan LIKE '".$nama_penyimpanan."'");
$check = $get_check->fetch(PDO::FETCH_ASSOC);
if($check['total']>0){
	header("location: stok_opnam_depo.php?status=2");
}else{
	header("location: stok_opnam_depo_view.php?tanggal_so=".$tanggal_so."&penyimpanan=".$penyimpanan);
}
