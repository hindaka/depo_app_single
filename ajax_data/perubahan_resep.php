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
$id_resep = isset($_POST['id']) ? $_POST['id'] : '';
$id_perubahan_resep = isset($_POST['id_perubahan_resep']) ? $_POST['id_perubahan_resep'] : '';
$task = isset($_POST['task']) ? $_POST['task'] : '';
$tertulis = isset($_POST['tulis']) ? $_POST['tulis'] : '';
$menjadi = isset($_POST['jadi']) ? $_POST['jadi'] : '';
$petugas_farmasi = isset($_POST['petugas']) ? $_POST['petugas'] : '';
$id_petugas = $r1['mem_id'];
try {
	if($task=='add'){
		$stmt = $db->prepare("INSERT INTO `perubahan_resep`(`id_resep`,`tertulis`, `menjadi`, `mem_id`,`petugas_approve`) VALUES (:id_resep,:tertulis,:menjadi,:mem_id,:petugas_approve)");
		$stmt->bindParam(':id_resep',$id_resep,PDO::PARAM_INT);
		$stmt->bindParam(':tertulis',$tertulis,PDO::PARAM_STR);
		$stmt->bindParam(':menjadi',$menjadi,PDO::PARAM_STR);
		$stmt->bindParam(':mem_id',$id_petugas,PDO::PARAM_INT);
		$stmt->bindParam(':petugas_approve',$petugas_farmasi,PDO::PARAM_INT);
		$stmt->execute();
		echo 'Berhasil ditambahkan';
	}else if($task=='delete'){
		$stmt = $db->prepare("DELETE FROM perubahan_resep WHERE id_perubahan_resep=:id");
		$stmt->bindParam(':id',$id_perubahan_resep,PDO::PARAM_INT);
		$stmt->execute();
		echo 'Berhasil dihapus';
	}else{

	}

} catch (PDOException $e) {
	echo $e->getMessage();
}
