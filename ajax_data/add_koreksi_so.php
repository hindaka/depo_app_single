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
$id_so = isset($_POST['id_so']) ? $_POST['id_so'] : '';
$id_kartu = isset($_POST['id_kartu']) ? $_POST['id_kartu'] : '';

try {
  $db->beginTransaction();
	// check SO
	$check_detail = $db->query("SELECT COUNT(*) as total FROM stok_opname_det WHERE id_parent_so='".$id_so."' AND id_kartu='".$id_kartu."'");
	$check = $check_detail->fetch(PDO::FETCH_ASSOC);
	if($check['total']>0){
		// header("location: stok_opnam_depo_koreksi_edit.php?koreksi=".$id_so."&status=1");
		$feedback = array(
			"status"=>"sukses",
			"title"=>"Peringatan!!",
			"text"=>"Data Obat sudah masuk ke dalam daftar SO!",
			"icon"=>"warning"
		);
	}else{
		//get kartu
		$get_kartu = $db->query("SELECT * FROM kartu_stok_ruangan WHERE id_kartu_ruangan='".$id_kartu."'");
		$kartu = $get_kartu->fetch(PDO::FETCH_ASSOC);
		$ins_detail = $db->prepare("INSERT INTO `stok_opname_det`(`id_parent_so`,`id_kartu`,`id_obat`, `no_batch`, `expired`,`harga_beli`,`harga_jual`, `stok_sistem`) VALUES (:id_parent,:id_kartu,:id_obat,:no_batch,:expired,:harga_beli,:harga_jual,:stok_sistem)");
		$ins_detail->bindParam(":id_parent",$id_so,PDO::PARAM_INT);
	  $ins_detail->bindParam(":id_kartu",$id_kartu,PDO::PARAM_INT);
	  $ins_detail->bindParam(":id_obat",$kartu['id_obat'],PDO::PARAM_INT);
	  $ins_detail->bindParam(":no_batch",$kartu['no_batch'],PDO::PARAM_INT);
	  $ins_detail->bindParam(":expired",$kartu['expired'],PDO::PARAM_INT);
	  $ins_detail->bindParam(":harga_beli",$kartu['harga_beli'],PDO::PARAM_INT);
	  $ins_detail->bindParam(":harga_jual",$kartu['harga_jual'],PDO::PARAM_INT);
	  $ins_detail->bindParam(":stok_sistem",$kartu['volume_kartu_akhir'],PDO::PARAM_INT);
	  $ins_detail->execute();
		$feedback = array(
			"status"=>"sukses",
			"title"=>"Berhasil!!",
			"text"=>"Data Detail SO Berhasil ditambahkan!",
			"icon"=>"success"
		);
	}
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
