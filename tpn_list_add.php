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
$id_rincian = isset($_POST['rincian']) ? $_POST['rincian'] : '';
$id_register = isset($_POST['reg']) ? $_POST['reg'] : '0';
$tpn_input = isset($_POST['tpn_input']) ? $_POST['tpn_input'] : 'custom';

$id_tpn_apotek = isset($_POST['list_tpn']) ? $_POST['list_tpn'] : 0;
$tipe_tpn="";
$konsentrasi = isset($_POST['konsentrasi']) ? $_POST['konsentrasi'] : '';
$gr = isset($_POST['gr']) ? $_POST['gr'] : '';
$amino_acid = isset($_POST['amino_acid']) ? $_POST['amino_acid'] : '';
$dex40 = isset($_POST['dex40']) ? $_POST['dex40'] : '';
$dex10 = isset($_POST['dex10']) ? $_POST['dex10'] : '';
$kcl = isset($_POST['kcl']) ? $_POST['kcl'] : '';
$ca_glu_10 = isset($_POST['ca_glu_10']) ? $_POST['ca_glu_10'] : '';
$mgso4 = isset($_POST['mgso4']) ? $_POST['mgso4'] : '';
$ns3 = isset($_POST['ns3']) ? $_POST['ns3'] : '';
$heparin = isset($_POST['heparin']) ? $_POST['heparin'] : 0;
$mem_id = $r1['mem_id'];
try {
	if($tpn_input=='list'){
		$get_tpn = $db->query("SELECT * FROM tpn_apotek WHERE id_tpn_apotek='".$id_tpn_apotek."'");
		$tpn = $get_tpn->fetch(PDO::FETCH_ASSOC);
		$stmt = $db->prepare("INSERT INTO `apotek_tpn_list`(
	 	 `id_register`,`type_list`, `tipe_tpn`, `konsentrasi`,
	 	 `gr`, `amino_acid`, `dex40`,
	 	 `dex10`, `kcl`, `ca_glu_10`,
	 	 `mgso4_40`, `ns_3`, `heparin`,`mem_id`) VALUES
	 	 (:id_register,:type_list,:tipe_tpn,:konsentrasi,
	 		 :gr,:amino_acid,:dex40,
	 		 :dex10,:kcl,:ca_glu_10,
	 		 :mgso4,:ns_3,:heparin,:mem_id)");
	  $stmt->bindParam(":id_register",$id_register,PDO::PARAM_INT);
	 	$stmt->bindParam(":type_list",$tpn_input,PDO::PARAM_STR);
		$stmt->bindParam(":tipe_tpn",$tpn['tipe_tpn'],PDO::PARAM_STR);
		$stmt->bindParam(":konsentrasi",$tpn['konsentrasi']);
		$stmt->bindParam(":gr",$tpn['gr']);
		$stmt->bindParam(":amino_acid",$tpn['amino_acid']);
		$stmt->bindParam(":dex40",$tpn['dex40']);
		$stmt->bindParam(":dex10",$tpn['dex10']);
		$stmt->bindParam(":kcl",$tpn['kcl']);
		$stmt->bindParam(":ca_glu_10",$tpn['ca_glu_10']);
		$stmt->bindParam(":mgso4",$tpn['mgso4_40']);
		$stmt->bindParam(":ns_3",$tpn['ns_3']);
		$stmt->bindParam(":heparin",$heparin);
		$stmt->bindParam(":mem_id",$mem_id,PDO::PARAM_INT);
		$stmt->execute();
	}else{
		$stmt = $db->prepare("INSERT INTO `apotek_tpn_list`(
	 	 `id_register`,`type_list`, `tipe_tpn`, `konsentrasi`,
	 	 `gr`, `amino_acid`, `dex40`,
	 	 `dex10`, `kcl`, `ca_glu_10`,
	 	 `mgso4_40`, `ns_3`, `heparin`,`mem_id`) VALUES
	 	 (:id_register,:type_list,:tipe_tpn,:konsentrasi,
	 		 :gr,:amino_acid,:dex40,
	 		 :dex10,:kcl,:ca_glu_10,
	 		 :mgso4,:ns_3,:heparin,:mem_id)");
		$stmt->bindParam(":id_register",$id_register,PDO::PARAM_INT);
	 	$stmt->bindParam(":type_list",$tpn_input,PDO::PARAM_STR);
		$stmt->bindParam(":tipe_tpn",$tipe_tpn,PDO::PARAM_STR);
		$stmt->bindParam(":konsentrasi",$konsentrasi);
		$stmt->bindParam(":gr",$gr);
		$stmt->bindParam(":amino_acid",$amino_acid);
		$stmt->bindParam(":dex40",$dex40);
		$stmt->bindParam(":dex10",$dex10);
		$stmt->bindParam(":kcl",$kcl);
		$stmt->bindParam(":ca_glu_10",$ca_glu_10);
		$stmt->bindParam(":mgso4",$mgso4);
		$stmt->bindParam(":ns_3",$ns3);
		$stmt->bindParam(":heparin",$heparin);
		$stmt->bindParam(":mem_id",$mem_id,PDO::PARAM_INT);
		$stmt->execute();
	}
	header('location: tpn_list.php?rincian='.$id_rincian.'&reg='.$id_register);
} catch (PDOException $e) {
	echo $e->getMessage();
}
