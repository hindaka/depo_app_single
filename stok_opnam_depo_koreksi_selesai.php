<?php
session_start();
include("../inc/pdo.conf.php");
include("../inc/version.php");
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
$id_so = isset($_GET['so']) ? $_GET['so'] : '';
$update =$db->query("UPDATE stok_opname SET koreksi_status='y' WHERE id_so='".$id_so."'");
header('location: stok_opnam_depo_koreksi.php?status=3');
