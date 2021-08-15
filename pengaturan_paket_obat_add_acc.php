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
$id_paket_ob = isset($_POST['id']) ? $_POST['id'] : '';
$task = isset($_POST['task']) ? $_POST['task'] : '';
$nama_paket = isset($_POST['nama_paket']) ? strtoupper(trim($_POST['nama_paket'])) : '';
$tipe_depo = $tipes[2];
$conf = json_decode(file_get_contents("config/env_depo.json"),true);
$id_depo = $conf[$tipe_depo]["id_depo"];
$today = date("Y-m-d H:i:s");
try {
	if($task=='add'){
		$check_nama = $db->query("SELECT COUNT(*) AS rec FROM paket_obat_bhp WHERE nama_paket LIKE '".$nama_paket."'");
		$check = $check_nama->fetch(PDO::FETCH_ASSOC);
		if($check['rec']>0){
			header("location: pengaturan_paket_obat.php?status=3");
		}else{
			$ins_data = $db->prepare("INSERT INTO `paket_obat_bhp`(`id_warehouse`, `nama_paket`) VALUES (:id_warehouse,:nama)");
			$ins_data->bindParam(":id_warehouse",$id_depo,PDO::PARAM_INT);
			$ins_data->bindParam(":nama",$nama_paket,PDO::PARAM_STR);
			$ins_data->execute();
			header("location: pengaturan_paket_obat.php?status=1");
		}

	}else if($task=='edit'){
		//check nama
		$check_nama = $db->query("SELECT COUNT(*) AS rec FROM paket_obat_bhp WHERE nama_paket LIKE '".$nama_paket."' AND id_paket_ob<>'".$id_paket_ob."'");
		$check = $check_nama->fetch(PDO::FETCH_ASSOC);
		if($check['rec']>0){
			header("location: pengaturan_paket_obat.php?status=3");
		}else{
			$up_data = $db->prepare("UPDATE `paket_obat_bhp` SET nama_paket=:nama WHERE id_paket_ob=:id");
			$up_data->bindParam(":nama",$nama_paket,PDO::PARAM_STR);
			$up_data->bindParam(":id",$id_paket_ob,PDO::PARAM_INT);
			$up_data->execute();
			header("location: pengaturan_paket_obat.php?status=2");
		}
	}else{

	}
} catch (PDOException $e) {
	echo $e->getMessage();
}
