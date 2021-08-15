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
$id_far_pelayanan = isset($_GET['f']) ? $_GET['f'] : '';
$id_invoice_all = isset($_GET['inv']) ? $_GET['inv'] : '';
$id_register = isset($_GET['reg']) ? $_GET['reg'] : '';
//get
$get_pelayanan = $db->query("SELECT * FROM farmasi_pelayanan WHERE id_far_pelayanan='".$id_far_pelayanan."'");
$pel = $get_pelayanan->fetch(PDO::FETCH_ASSOC);
$nama_pelayanan = $pel['nama_pelayanan'];
if($pel['inv_in']=='y'){
	$check_inv = $db->query("SELECT COUNT(*) as total_rec FROM invoice_all_det WHERE id_invoice_all='".$id_invoice_all."' AND jperiksa LIKE '".$nama_pelayanan."'");
	$check = $check_inv->fetch(PDO::FETCH_ASSOC);
	if($check['total_rec']==1){
		$del_data = $db->prepare("DELETE FROM invoice_all_det WHERE id_invoice_all=:id AND jperiksa LIKE :nama");
		$del_data->bindParam(":id",$id_invoice_all,PDO::PARAM_INT);
		$del_data->bindParam(":nama",$nama_pelayanan,PDO::PARAM_STR);
		$del_data->execute();
	}
}
$del_data = $db->prepare("DELETE FROM farmasi_pelayanan WHERE id_far_pelayanan=:id");
$del_data->bindParam(":id",$id_far_pelayanan,PDO::PARAM_INT);
$del_data->execute();
echo "<script language=\"JavaScript\">window.location = \"tindakan_pasien_log.php?inv=".$id_invoice_all."&reg=".$id_register."&status=2\"</script>";
