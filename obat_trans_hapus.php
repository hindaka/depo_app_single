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
echo "Sedang dalam proses...";
$id_rincian = isset($_GET['id']) ? $_GET['id'] : '';
$id_transaksi = isset($_GET['t']) ? $_GET['t'] : '';
  // no data just delete
  $sql_delete = $db->query("DELETE FROM rincian_transaksi_obat WHERE id_trans_obat=".$id_transaksi);
  echo "<script language=\"JavaScript\">window.location = \"obat_ranap_trans.php?id=".$id_rincian."&status=5\"</script>";
