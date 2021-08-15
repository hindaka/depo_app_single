<?php
session_start();
include("../../inc/pdo.conf.php");
include("../../inc/version.php");
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
	header("location:../../index.php?status=2");
	exit;
}
include "../../inc/anggota_check.php";
$id_tpn_list = isset($_POST['id']) ? $_POST['id'] : '';
try {
	// check data
	$check_data = $db->query("SELECT COUNT(*) total_rec FROM apotek_tpn_list WHERE id_tpn_list='".$id_tpn_list."'");
	$check = $check_data->fetch(PDO::FETCH_ASSOC);
	if($check['total_rec']==0){
		$feed_back= array(
			"title"=>"Peringatan!!",
			"text"=>'Data TPN tidak terdaftar, silakan cek tabel data TPN',
			"status"=>'warning',
			"icon"=>'warning'
		);
	}else{
		$del_tpn = $db->query("DELETE FROM apotek_tpn_list WHERE id_tpn_list='".$id_tpn_list."'");
		$feed_back= array(
			"title"=>'Berhasil!!',
			"text"=>'Data TPN Berhasil dihapus',
			"status"=>'success',
			"icon"=>'success'
		);
	}
	echo json_encode($feed_back);
} catch (PDOException $e) {
	$feed_back= array(
		"title"=>'Error!!',
		"text"=>$e->getMessage(),
		"status"=>'error',
		"icon"=>'error'
	);
	echo json_encode($feed_back);
}
