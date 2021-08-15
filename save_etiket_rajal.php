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
$id_petugas = $r1['mem_id'];
$id_obatkeluar = isset($_GET['i']) ? $_GET['i'] : '';
$id_resep = isset($_GET['resep']) ? $_GET['resep'] : '';
$sehari = isset($_POST['sehari']) ? $_POST['sehari'] : '';
$takaran = isset($_POST['takaran']) ? $_POST['takaran'] : '';
$minum = isset($_POST['minum']) ? $_POST['minum'] : '';
$petunjuk = isset($_POST['petunjuk']) ? $_POST['petunjuk'] : '';
$edate = isset($_POST['edate']) ? $_POST['edate'] : '';
$label_khusus = isset($_POST['label_khusus']) ? $_POST['label_khusus'] : '';

// check in etiket_apotek is id_warehouse_out already been there
$check_in = $db->query("SELECT * FROM etiket_apotek_rajal WHERE id_obatkeluar='".$id_obatkeluar."' ORDER BY id_etiket_rajal DESC LIMIT 1");
$total_check = $check_in->rowCount();
echo $total_check;
if($total_check==1){
  $check = $check_in->fetch(PDO::FETCH_ASSOC);
  $update_etiket = $db->prepare("UPDATE etiket_apotek_rajal SET sehari_x='".$sehari."',takaran='".$takaran."',diminum='".$minum."',petunjuk_khusus='".$petunjuk."',label_khusus='".$label_khusus."' WHERE id_etiket_rajal='".$check['id_etiket_rajal']."'");
  $update_etiket->execute();
	$id_etiket = $check['id_etiket_rajal'];
}else{
  // insert etiket_ranap
  $save_etiket = $db->prepare("INSERT INTO `etiket_apotek_rajal`(`id_obatkeluar`, `sehari_x`, `takaran`, `diminum`, `petunjuk_khusus`, `expired_date`,`label_khusus`,`petugas`) VALUES (:id_obatkeluar,:sehari,:takaran,:diminum,:petunjuk,:expired,:label_khusus,:petugas)");
  $save_etiket->bindParam(":id_obatkeluar",$id_obatkeluar,PDO::PARAM_INT);
  $save_etiket->bindParam(":sehari",$sehari,PDO::PARAM_STR);
  $save_etiket->bindParam(":takaran",$takaran,PDO::PARAM_STR);
  $save_etiket->bindParam(":diminum",$minum,PDO::PARAM_STR);
  $save_etiket->bindParam(":petunjuk",$petunjuk,PDO::PARAM_STR);
  $save_etiket->bindParam(":expired",$edate,PDO::PARAM_STR);
	$save_etiket->bindParam(":label_khusus",$label_khusus,PDO::PARAM_STR);
	$save_etiket->bindParam(":petugas",$id_petugas,PDO::PARAM_INT);
  $save_etiket->execute();
	$id_etiket = $db->lastInsertId();
}

// redirect to cetak_etiket_ranap
echo "<script language=\"JavaScript\">window.location = \"cetaketiket.php?e=".$id_etiket."&i=".$id_obatkeluar."&resep=".$id_resep."\"</script>";
