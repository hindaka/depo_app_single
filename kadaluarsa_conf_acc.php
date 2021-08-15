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
$waktu_kadaluarsa_red = isset($_POST['waktu_kadaluarsa_red']) ? $_POST['waktu_kadaluarsa_red'] : '1';
$waktu_kadaluarsa_yellow = isset($_POST['waktu_kadaluarsa_yellow']) ? $_POST['waktu_kadaluarsa_yellow'] : '2';
$waktu_kadaluarsa_green = isset($_POST['waktu_kadaluarsa_green']) ? $_POST['waktu_kadaluarsa_green'] : '3';
$myfile = fopen("config/kadaluarsa.txt", "w") or die("Unable to open file!");
$join = $waktu_kadaluarsa_red.";".$waktu_kadaluarsa_yellow.";".$waktu_kadaluarsa_green;
fwrite($myfile, $join);
fclose($myfile);
header('location: kadaluarsa_conf.php?status=1');
