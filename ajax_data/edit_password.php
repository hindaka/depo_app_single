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
$id_petugas = isset($_POST['mem_id']) ? $_POST['mem_id'] : $r1['mem_id'];
$sandi_lama = isset($_POST['sandi_lama']) ? $_POST['sandi_lama'] : '';
$sandi_baru = isset($_POST['sandi_baru']) ? $_POST['sandi_baru'] : '';
$sandi_baru_konf = isset($_POST['sandi_baru_konf']) ? $_POST['sandi_baru_konf'] : '';
try{
	$db->beginTransaction();
	$check_data = $db->prepare("SELECT COUNT(*) as total FROM anggota WHERE mem_id=:mem_id AND password=:pass");
	$check_data->bindParam(":mem_id",$id_petugas,PDO::PARAM_INT);
	$check_data->bindParam(":pass",$sandi_lama,PDO::PARAM_STR);
	$check_data->execute();
	$get_data = $check_data->fetch(PDO::FETCH_ASSOC);
	if($get_data['total']==1){
		$update_password = $db->prepare("UPDATE anggota SET password=:pass WHERE mem_id=:id");
		$update_password->bindParam(":pass",$sandi_baru,PDO::PARAM_STR);
		$update_password->bindParam(":id",$id_petugas,PDO::PARAM_INT);
		$update_password->execute();
		$feedback = array(
			"status"=>"sukses",
			"title"=>"Berhasil!!",
			"text"=>"Password berhasil diubah!, silakan login kembali",
			"icon"=>"success"
		);
	}else{
		$feedback = array(
			"status"=>"gagal",
			"title"=>"Peringatan!!",
			"text"=>"Gagal Mengubah password, Silakan Ulangi beberapa saat lagi",
			"icon"=>"error"
		);
	}
	$db->commit();
  echo json_encode($feedback);
} catch (PDOException $e) {
  $error = $e->getMessage();
	$db->rollBack();
  $feedback = array(
    "status"=>"error",
    "title"=>"Gagal!!",
    "text"=>$e->getMessage(),
    "icon"=>"error"
  );
  echo json_encode($feedback);
}
