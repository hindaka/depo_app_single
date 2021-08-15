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
$rentang_waktu = isset($_POST['rentang_waktu']) ? $_POST['rentang_waktu'] : '7';
$myfile = fopen("config/waktu_filter.txt", "w") or die("Unable to open file!");
fwrite($myfile, $rentang_waktu);
fclose($myfile);
header('location: pengaturan_filter_transaksi.php?status=1');
