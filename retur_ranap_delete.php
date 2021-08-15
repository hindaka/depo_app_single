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
$id_rincian_obat = isset($_GET['id']) ? $_GET['id'] : '';
$id_parent = isset($_GET['p']) ? $_GET['p'] : '';
$del_parent_retur = $db->prepare("DELETE FROM parent_retur_obat WHERE id_parent_retur=:id");
$del_parent_retur->bindParam(":id",$id_parent,PDO::PARAM_INT);
$del_parent_retur->execute();
echo "<script language=\"JavaScript\">window.location = \"obat_ranap_trans.php?id=".$id_rincian_obat."&status=7\"</script>";
 ?>
