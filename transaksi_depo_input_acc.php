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
$tanggal_permintaan = isset($_POST['tanggal_permintaan']) ? $_POST['tanggal_permintaan'] : '';
$permintaan = isset($_POST['permintaan']) ? $_POST['permintaan'] : '';
$id_warehouse = isset($_POST['id_warehouse']) ? $_POST['id_warehouse'] : '';
$tipe_depo = $tipes[2];
$conf = json_decode(file_get_contents("config/env_depo.json"),true);
$id_depo = $conf[$tipe_depo]["id_depo"];
$nama_depo = $conf[$tipe_depo]["nama_depo"];
$status = 'draft';
$mem_id = $r1['mem_id'];
$tgl = substr($tanggal_permintaan,0,10);
$jam = substr($tanggal_permintaan,11,8);
$split = explode("/",$tgl);
$new_date = $split[2]."-".$split[1]."-".$split[0]." ".$jam;
try {
	$stmt = $db->prepare("INSERT INTO `barangkeluar_depo`(`asal_warehouse`,`id_warehouse`,`asal_depo`,`tanggal_permintaan`, `permintaan`, `status_keluar`, `mem_id`)
	VALUES (:asal,:id_warehouse,:asal_depo,:tanggal_permintaan,:permintaan,:status_keluar,:mem_id)");
	$stmt->bindParam(":asal",$id_depo,PDO::PARAM_INT);
	$stmt->bindParam(":id_warehouse",$id_warehouse,PDO::PARAM_INT);
	$stmt->bindParam(":asal_depo",$nama_depo,PDO::PARAM_STR);
	$stmt->bindParam(":tanggal_permintaan",$new_date,PDO::PARAM_STR);
	$stmt->bindParam(":permintaan",$permintaan,PDO::PARAM_STR);
	$stmt->bindParam(":status_keluar",$status,PDO::PARAM_STR);
	$stmt->bindParam(":mem_id",$mem_id,PDO::PARAM_INT);
	$stmt->execute();
	$last_id = $db->lastInsertId();
	header('location: input_permintaan_depo.php?parent='.$last_id);
} catch (PDOException $e) {
	echo $e->getMessage();
}
