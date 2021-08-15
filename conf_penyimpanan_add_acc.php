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
$id_conf_obat = isset($_POST['id']) ? $_POST['id'] : '';
$task = isset($_POST['task']) ? $_POST['task'] : '';
$nama_penyimpanan = isset($_POST['nama_penyimpanan']) ? strtoupper(trim($_POST['nama_penyimpanan'])) : '';
$nama_petugas = isset($_POST['nama_petugas']) ? $_POST['nama_petugas'] : '';
$tipe_depo = $tipes[2];
$conf = json_decode(file_get_contents("config/env_depo.json"),true);
$id_depo = $conf[$tipe_depo]["id_depo"];
$today = date("Y-m-d H:i:s");
try {
	if($task=='add'){
		$check_nama = $db->query("SELECT COUNT(*) AS rec FROM conf_penyimpanan_obat WHERE nama_penyimpanan LIKE '".$nama_penyimpanan."'");
		$check = $check_nama->fetch(PDO::FETCH_ASSOC);
		if($check['rec']>0){
			header("location: conf_penyimpanan.php?status=3");
		}else{
			$ins_data = $db->prepare("INSERT INTO `conf_penyimpanan_obat`(`id_warehouse`, `nama_penyimpanan`,`pj`,`created_at`) VALUES (:id_warehouse,:nama,:pj,:created_at)");
			$ins_data->bindParam(":id_warehouse",$id_depo,PDO::PARAM_INT);
			$ins_data->bindParam(":nama",$nama_penyimpanan,PDO::PARAM_STR);
			$ins_data->bindParam(":pj",$nama_petugas,PDO::PARAM_STR);
			$ins_data->bindParam(":created_at",$today,PDO::PARAM_STR);
			$ins_data->execute();
			header("location: conf_penyimpanan.php?status=1");
		}

	}else if($task=='edit'){
		//check nama
		$check_nama = $db->query("SELECT COUNT(*) AS rec FROM conf_penyimpanan_obat WHERE nama_penyimpanan LIKE '".$nama_penyimpanan."' AND id_conf_obat<>'".$id_conf_obat."'");
		$check = $check_nama->fetch(PDO::FETCH_ASSOC);
		if($check['rec']>0){
			header("location: conf_penyimpanan.php?status=3");
		}else{
			$up_data = $db->prepare("UPDATE `conf_penyimpanan_obat` SET nama_penyimpanan=:nama,pj=:pj WHERE id_conf_obat=:id");
			$up_data->bindParam(":nama",$nama_penyimpanan,PDO::PARAM_STR);
			$up_data->bindParam(":pj",$nama_petugas,PDO::PARAM_STR);
			$up_data->bindParam(":id",$id_conf_obat,PDO::PARAM_INT);
			$up_data->execute();
			header("location: conf_penyimpanan.php?status=2");
		}
	}else{

	}
} catch (PDOException $e) {
	echo $e->getMessage();
}
