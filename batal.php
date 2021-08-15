<?php
//conn
session_start();
include("../inc/pdo.conf.php");
include("../inc/version.php");
ini_set('display_errors',1);
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
//var
$id_resep=isset($_GET["resep"]) ? $_GET['resep'] : '';
if($id_resep==''){
	header('location:rekap.php');
}else{
	$get_resep = $db->query("SELECT COUNT(*) as total_data FROM resep WHERE id_resep='".$id_resep."'");
	$resep = $get_resep->fetch(PDO::FETCH_ASSOC);
	if($resep['total_data']>0){
		// check telaah_resep
		$check_telaah = $db->query("SELECT COUNT(*) as rec FROM telaah_resep");
		$ct = $check_telaah->fetch(PDO::FETCH_ASSOC);
		if($ct['rec']>0){
			$del_telaah = $db->query("DELETE FROM telaah_resep WHERE id_resep='".$id_resep."'");
		}
		// check perubahan resep
		$check_perubahan = $db->query("SELECT COUNT(*) as rec FROM perubahan_resep WHERE id_resep='".$id_resep."'");
		$cp = $check_perubahan->fetch(PDO::FETCH_ASSOC);
		if($cp['rec']>0){
			$del_perubahan = $db->query("DELETE FROM perubahan_resep WHERE id_resep='".$id_resep."'");
		}
		//cek rtt
		$check_rtt = $db->query("SELECT COUNT(*) as rec FROM resep_rtt WHERE id_resep='".$id_resep."'");
		$rtt = $check_rtt->fetch(PDO::FETCH_ASSOC);
		if($rtt['rec']>0){
			$del_rtt = $db->query("DELETE FROM resep_rtt WHERE id_resep='".$id_resep."'");
		}
		//delete
		$result = $db->query("DELETE FROM resep WHERE id_resep='".$id_resep."'");
		echo "<script language=\"JavaScript\">window.location = \"rekap.php?status=1\"</script>";
	}else{
		echo "Resep tidak ditemukan";
	}
}
?>
