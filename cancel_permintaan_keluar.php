<?php
session_start();
include("../inc/pdo.conf.php");
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
//get var
$id_parent = isset($_GET['parent']) ? $_GET['parent'] : '';
$del_parent = $db->query("DELETE FROM barangkeluar_depo WHERE id_barangkeluar_depo='".$id_parent."'");
header("location: transaksi_depo.php?status=2");
?>
