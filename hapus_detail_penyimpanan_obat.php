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
$id_conf_detail = isset($_GET['id']) ? $_GET['id'] : '';
$id_conf_obat = isset($_GET['master']) ? $_GET['master'] : '';
try {
  $del_data = $db->prepare("DELETE FROM conf_detail_penyimpanan WHERE id_conf_detail=:id");
  $del_data->bindParam(":id",$id_conf_detail,PDO::PARAM_INT);
  $del_data->execute();
  header("location: penyimpanan_obat.php?id=".$id_conf_obat."&status=4");
} catch (PDOException $e) {
  echo $e->getMessage();
}
