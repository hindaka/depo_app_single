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
$nomedrek = isset($_GET['nomedrek']) ? $_GET['nomedrek'] : '';
$dpjp = isset($_GET['dpjp']) ? $_GET['dpjp'] : '';
$status = "apotek";
//get id_pasien
$sql_pasien = "SELECT id_pasien,nama FROM registerpasien WHERE nomedrek=".$nomedrek." ORDER BY id_pasien DESC LIMIT 1";
$get_pasien = $db->query($sql_pasien);
$pasien = $get_pasien->fetch(PDO::FETCH_ASSOC);

if($pasien['nama']!=''){
		//insert new data
		//insert into table rincian_obat_pasien
	  $sql_insert = $db->query("INSERT INTO rincian_obat_pasien(id_pasien,dpjp,status) VALUES(".$pasien['id_pasien'].",'".$dpjp."','".$status."')");
	  if($sql_insert){
	    $id_rincian = $db->lastInsertId();
	    echo "<script language=\"JavaScript\">window.location = \"obat_ranap_trans.php?id=".$id_rincian."\"</script>";
	  }else{
	    echo 'Failed<br>';
	  }
}else{
  echo "<script language=\"JavaScript\">window.location = \"obat_ranap.php?status=4\"</script>";
}
