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
$id_rincian = isset($_GET['id']) ? trim($_GET['id']) : '';
$id_parent_retur = isset($_GET['p']) ? $_GET['p'] : '';
$id_transaksi = isset($_GET['t']) ? trim($_GET['t']) : '';
// update nominal retur di parent_retur_obat
$get_total_retur = $db->prepare("SELECT SUM(harga_tuslah) as total_harga FROM rincian_retur_obat WHERE id_parent_retur=:id");
$get_total_retur->bindParam(":id",$id_parent_retur,PDO::PARAM_INT);
$get_total_retur->execute();
$total_retur = $get_total_retur->fetch(PDO::FETCH_ASSOC);
$up_total = $db->prepare("UPDATE parent_retur_obat SET total_retur=:total WHERE id_parent_retur=:id");
$up_total->bindParam(":total",$total_retur['total_harga'],PDO::PARAM_INT);
$up_total->bindParam(":id",$id_parent_retur,PDO::PARAM_INT);
$up_total->execute();
echo "<script language=\"JavaScript\">window.location = \"obat_ranap_trans.php?id=$id_rincian\"</script>";
