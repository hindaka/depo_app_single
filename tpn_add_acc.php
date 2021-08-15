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
$tipe = isset($_POST['tipe']) ? $_POST['tipe'] : '';
$konsentrasi = isset($_POST['konsentrasi']) ? $_POST['konsentrasi'] : '';
$gr = isset($_POST['gr']) ? $_POST['gr'] : '';
$amino_acid = isset($_POST['amino_acid']) ? $_POST['amino_acid'] : '';
$dex40 = isset($_POST['dex40']) ? $_POST['dex40'] : '';
$dex10 = isset($_POST['dex10']) ? $_POST['dex10'] : '';
$kcl = isset($_POST['kcl']) ? $_POST['kcl'] : '';
$ca_glu_10 = isset($_POST['ca_glu_10']) ? $_POST['ca_glu_10'] : '';
$mgso4 = isset($_POST['mgso4']) ? $_POST['mgso4'] : '';
$ns3 = isset($_POST['ns3']) ? $_POST['ns3'] : '';
try {
	$check = $db->prepare("SELECT COUNT(*) as total_data FROM tpn_apotek WHERE tipe_tpn=:tipe AND konsentrasi=:konsentrasi");
	$check->bindParam(":tipe",$tipe,PDO::PARAM_STR);
	$check->bindParam(":konsentrasi",$konsentrasi,PDO::PARAM_STR);
	$check->execute();
	$data_check = $check->fetch(PDO::FETCH_ASSOC);
	if($data_check['total_data']>0){
		echo "<script language=\"JavaScript\">window.location = \"tpn_add.php?status=2\"</script>";
	}else{
		$stmt = $db->prepare("INSERT INTO `tpn_apotek`(`tipe_tpn`, `konsentrasi`, `gr`, `amino_acid`, `dex40`, `dex10`, `kcl`, `ca_glu_10`, `mgso4_40`, `ns_3`) VALUES(:tipe_tpn,:konsentrasi,:gr,:amino_acid,:dex40,:dex10,:kcl,:ca_glu_10,:mgso4,:ns_3)");
		$stmt->bindParam(":tipe_tpn",$tipe,PDO::PARAM_STR);
		$stmt->bindParam(":konsentrasi",$konsentrasi,PDO::PARAM_STR);
		$stmt->bindParam(":gr",$gr,PDO::PARAM_INT);
		$stmt->bindParam(":amino_acid",$amino_acid,PDO::PARAM_STR);
		$stmt->bindParam(":dex40",$dex40,PDO::PARAM_STR);
		$stmt->bindParam(":dex10",$dex10,PDO::PARAM_STR);
		$stmt->bindParam(":kcl",$kcl,PDO::PARAM_STR);
		$stmt->bindParam(":ca_glu_10",$ca_glu_10,PDO::PARAM_STR);
		$stmt->bindParam(":mgso4",$mgso4,PDO::PARAM_STR);
		$stmt->bindParam(":ns_3",$ns3,PDO::PARAM_STR);
		$stmt->execute();
		echo "<script language=\"JavaScript\">window.location = \"tpn.php?status=1\"</script>";
	}
} catch (PDOException $e) {
	echo $e->getMessage();
}
