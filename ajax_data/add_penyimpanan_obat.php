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
$id_obat = isset($_POST['id']) ? $_POST['id'] : '';
$id_conf_obat = isset($_POST['id_conf_obat']) ? $_POST['id_conf_obat'] : '';
$tipe_depo = $tipes[2];
$conf = json_decode(file_get_contents("../config/env_depo.json"),true);
$id_depo = $conf[$tipe_depo]["id_depo"];

try {
  $db->beginTransaction();
  $config = $db->query("SELECT * FROM conf_penyimpanan_obat WHERE id_conf_obat='".$id_conf_obat."'");
  $data_conf = $config->fetch(PDO::FETCH_ASSOC);
  $nama_penyimpanan = $data_conf['nama_penyimpanan'];
  $stmt = $db->prepare("INSERT INTO `conf_detail_penyimpanan`(`id_conf_obat`, `id_obat`) VALUES (:id_conf,:id_obat)");
  $stmt->bindParam(":id_conf",$id_conf_obat,PDO::PARAM_INT);
  $stmt->bindParam(":id_obat",$id_obat,PDO::PARAM_INT);
  $stmt->execute();
  $feedback = array(
    "status"=>"sukses",
    "title"=>"Berhasil!!",
    "text"=>"Data Obat Berhasil ditambahkan di ".$nama_penyimpanan,
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
