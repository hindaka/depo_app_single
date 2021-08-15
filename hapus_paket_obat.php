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
$id_paket_ob = isset($_GET['id']) ? $_GET['id'] : '';
$task = isset($_GET['task']) ? $_GET['task'] : '';
try {
  $check_data = $db->prepare("SELECT COUNT(*) as total_data FROM paket_obat_bhp_detail WHERE id_paket_ob=:id");
  $check_data->bindParam(":id",$id_paket_ob,PDO::PARAM_INT);
  $check_data->execute();
	$check = $check_data->fetch(PDO::FETCH_ASSOC);
	if($check['total_data']>0){
		//tida boleh dihapus karena ada detail
		header("location: pengaturan_paket_obat.php?status=5");
	}else{
		$del_data = $db->prepare("DELETE FROM paket_obat_bhp WHERE id_paket_ob=:id");
	  $del_data->bindParam(":id",$id_paket_ob,PDO::PARAM_INT);
	  $del_data->execute();
		header("location: pengaturan_paket_obat.php?status=4");
	}
} catch (PDOException $e) {
  echo $e->getMessage();
}
