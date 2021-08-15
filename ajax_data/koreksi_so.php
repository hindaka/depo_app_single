<?php
session_start();
include("../../inc/pdo.conf.php");
include("../../inc/version.php");
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
	header("location:../../index.php?status=2");
	exit;
}
include "../../inc/anggota_check.php";
$id_det_so = isset($_POST['id_det_so']) ? $_POST['id_det_so'] : '';
$retur_masuk = isset($_POST['retur_masuk']) ? $_POST['retur_masuk'] : '';
$pengeluaran = isset($_POST['pengurangan']) ? $_POST['pengurangan'] : '';
$sisa_real = isset($_POST['sisa_real']) ? $_POST['sisa_real'] : '';
$fisik = isset($_POST['fisik']) ? $_POST['fisik'] : '';
$alasan = isset($_POST['alasan']) ? $_POST['alasan'] : '';
$selisih = $sisa_real - $fisik;
$koreksi = "y";
try {
  $db->beginTransaction();
	//update
	$update_detail_so = $db->prepare("UPDATE stok_opname_det SET mutasi_masuk=:mutasi_masuk,pengurangan=:pengeluaran,sisa_real=:sisa_real,fisik=:fisik,alasan=:alasan,selisih=:selisih,koreksi=:koreksi WHERE id_det_so=:id");
  $update_detail_so->bindParam(":mutasi_masuk",$retur_masuk,PDO::PARAM_INT);
	$update_detail_so->bindParam(":pengeluaran",$pengeluaran,PDO::PARAM_INT);
	$update_detail_so->bindParam(":sisa_real",$sisa_real,PDO::PARAM_INT);
	$update_detail_so->bindParam(":fisik",$fisik,PDO::PARAM_INT);
	$update_detail_so->bindParam(":alasan",$alasan,PDO::PARAM_STR);
	$update_detail_so->bindParam(":selisih",$selisih,PDO::PARAM_INT);
	$update_detail_so->bindParam(":koreksi",$koreksi,PDO::PARAM_STR);
	$update_detail_so->bindParam(":id",$id_det_so,PDO::PARAM_STR);
	$update_detail_so->execute();
  $feedback = array(
    "status"=>"sukses",
    "title"=>"Berhasil!!",
    "text"=>"Data SO Berhasil dikoreksi!",
    "icon"=>"success"
  );
  $db->commit();
} catch (PDOException $e) {
  $db->rollBack();
  $feedback = array(
    "status"=>"error",
    "title"=>"Error!!",
    "text"=>$e->getMessage(),
    "icon"=>"error"
  );
}
echo json_encode($feedback);
