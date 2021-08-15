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
echo "Sedang dalam proses...<br>";
$id_rincian = isset($_GET['id']) ? $_GET['id'] : '';
// delete master data rincian
try {
	$sql_master = $db->query("DELETE FROM rincian_obat_pasien WHERE id_rincian_obat=".$id_rincian);
	if($sql_master){
	  echo "<br>Penghapusan Berhasil...";
	  echo "<script language=\"JavaScript\">window.location = \"obat_ranap.php?status=5\"</script>";
	}
} catch (PDOException $e) {
	echo $e->getMessage();
}
