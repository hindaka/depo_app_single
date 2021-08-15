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
$id_rincian = isset($_GET['id']) ? $_GET['id'] : '';
$task = isset($_GET['task']) ? $_GET['task'] : '';
$ruang = isset($_GET['ruang']) ? $_GET['ruang'] : '';
$mem_id = $r1['mem_id'];
$f_today=date("Y-m-d");
$submit = isset($_GET['tambah']) ? $_GET['tambah'] : '';
$tipe_depo = $tipes[2];
$conf = json_decode(file_get_contents("config/env_depo.json"),true);
$id_depo = $conf[$tipe_depo]["id_depo"];
if($task=='new'){
	$sql_new = $db->query("INSERT INTO `rincian_transaksi_obat` (`id_rincian_obat`,`ruang`,`id_warehouse`,`user`) VALUES ('".$id_rincian."','".$ruang."','".$id_depo."','".$mem_id."')");
	if($sql_new){
		$id_transaksi = $db->lastInsertId();
    echo "<script language=\"JavaScript\">window.location = \"obat_ranap_detail.php?id=".$id_rincian."&t=".$id_transaksi."&r=".$ruang."&task=new\"</script>";
	}else{
		//fail
		echo "Fail to create Transaction : <br>";
		exit();
	}
}else if($task=='edit'){

}else{

}
