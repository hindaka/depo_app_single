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
$id_rincian = isset($_POST['id_rincian']) ? $_POST['id_rincian'] : '';
$petugas_retur = isset($_POST['petugas_retur']) ? $_POST['petugas_retur'] : '';
$ruang_retur = isset($_POST['ruang_retur']) ? $_POST['ruang_retur'] : '';
$today = date('Y-m-d H:i:s');
$id_petugas = $r1['mem_id'];
$total_retur = 0;
try {
  $stmt = $db->prepare("INSERT INTO `parent_retur_obat`(`id_rincian_obat`,`tanggal_retur`, `ruangan`, `total_retur`,`petugas_retur`, `petugas_input`)VALUES (:id_rincian_obat,:tanggal_retur,:ruangan,:total_retur,:petugas_retur,:petugas_input)");
	$stmt->bindParam(":id_rincian_obat",$id_rincian,PDO::PARAM_INT);
  $stmt->bindParam(":tanggal_retur",$today,PDO::PARAM_STR);
  $stmt->bindParam(":ruangan",$ruang_retur,PDO::PARAM_STR);
  $stmt->bindParam(":total_retur",$total_retur,PDO::PARAM_INT);
  $stmt->bindParam(":petugas_retur",$petugas_retur,PDO::PARAM_STR);
  $stmt->bindParam(":petugas_input",$id_petugas,PDO::PARAM_INT);
  $stmt->execute();
  $id_parent = $db->lastInsertId();
  $feedback = array(
    "status"=>"sukses",
    "pesan"=>"Retur Parent Berhasil disimpan",
    "id_rincian"=>$id_rincian,
    "id_parent"=>$id_parent,
  );
  echo json_encode($feedback);
} catch (PDOException $e) {
  $pesan = $e->getMessage();
  $feedback = array(
    "status"=>"gagal",
    "pesan"=>$pesan,
    "id_rincian"=>$id_rincian,
    "id_parent"=>"NULL",
  );
  echo json_encode($feedback);
}
