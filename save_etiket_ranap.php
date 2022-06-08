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
$id_petugas = $r1['mem_id'];
$id_rincian = isset($_GET['i']) ? $_GET['i'] : '';
$id_detail_rincian = isset($_GET['d']) ? $_GET['d'] : '';
$sehari = isset($_POST['sehari']) ? $_POST['sehari'] : '';
$takaran = isset($_POST['takaran']) ? $_POST['takaran'] : '';
$minum = isset($_POST['minum']) ? $_POST['minum'] : '';
$petunjuk = isset($_POST['petunjuk']) ? $_POST['petunjuk'] : '';
$edate = isset($_POST['edate']) ? $_POST['edate'] : '';
$no_batch = isset($_POST['no_batch']) ? $_POST['no_batch'] : '';

// check in etiket_apotek is id_warehouse_out already been there
$check_in = $db->query("SELECT * FROM etiket_apotek WHERE id_detail_rincian='".$id_detail_rincian."' ORDER BY id_etiket DESC LIMIT 1");
$total_check = $check_in->rowCount();
if($total_check==1){
  $check = $check_in->fetch(PDO::FETCH_ASSOC);
  $update_etiket = $db->prepare("UPDATE etiket_apotek SET sehari_x='".$sehari."',takaran='".$takaran."',diminum='".$minum."',petunjuk_khusus='".$petunjuk."' WHERE id_etiket='".$check['id_etiket']."'");
  $update_etiket->execute();
	$id_etiket = $check['id_etiket'];
}else{
  // insert etiket_ranap
  $save_etiket = $db->prepare("INSERT INTO `etiket_apotek`(`id_detail_rincian`, `sehari_x`, `takaran`, `diminum`, `petunjuk_khusus`, `expired_date`,`no_batch`,`petugas`) VALUES (:id_detail_rincian,:sehari,:takaran,:diminum,:petunjuk,:expired,:no_batch,:petugas)");
  $save_etiket->bindParam(":id_detail_rincian",$id_detail_rincian,PDO::PARAM_INT);
  $save_etiket->bindParam(":sehari",$sehari,PDO::PARAM_STR);
  $save_etiket->bindParam(":takaran",$takaran,PDO::PARAM_STR);
  $save_etiket->bindParam(":diminum",$minum,PDO::PARAM_STR);
  $save_etiket->bindParam(":petunjuk",$petunjuk,PDO::PARAM_STR);
  $save_etiket->bindParam(":expired",$edate,PDO::PARAM_STR);
  $save_etiket->bindParam(":no_batch",$no_batch,PDO::PARAM_STR);
	$save_etiket->bindParam(":petugas",$id_petugas,PDO::PARAM_INT);
  $save_etiket->execute();
	$id_etiket = $db->lastInsertId();
}

// redirect to cetak_etiket_ranap
echo "<script language=\"JavaScript\">window.location = \"cetak_etiket_ranap.php?e=".$id_etiket."&i=".$id_rincian."&d=".$id_detail_rincian."\"</script>";
