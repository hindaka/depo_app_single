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
$request = isset($_POST['medrek']) ? $_POST['medrek'] : '';
$today = date('d/m/Y');
$stmt = $db->query("SELECT jpasien,slug_apotek FROM registerpasien rp INNER JOIN cabar c ON(rp.jpasien=c.cabar) WHERE rp.nomedrek='".$request."' AND rp.tanggaldaftar='".$today."' ORDER BY id_pasien DESC LIMIT 1");
$result = $stmt->fetch(PDO::FETCH_ASSOC);
if($result){
    sleep(1);
    echo json_encode($result);
}else{
  return false;
}
?>
