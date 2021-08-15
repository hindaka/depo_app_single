<?php
session_start();
include("../../inc/pdo.conf.php");
include("../../inc/version.php");
date_default_timezone_set("Asia/Jakarta");
$namauser = $_SESSION['namauser'];
$password = $_SESSION['password'];
$tipe = $_SESSION['tipe'];
$tipes = explode('-', $tipe);
if ($tipes[0] != 'DepoApp') {
    unset($_SESSION['tipe']);
    unset($_SESSION['namauser']);
    unset($_SESSION['password']);
    header("location:../../index.php?status=2");
    exit;
}
include "../../inc/anggota_check.php";
$tipe_depo = $tipes[2];
$conf = json_decode(file_get_contents("../config/env_depo.json"), true);
$id_depo = $conf[$tipe_depo]["id_depo"];
$tahun = date('Y');
$today = date("Y-m-d H:i:s");
$id_paket_obat=isset($_POST['id_paket_obat']) ? $_POST['id_paket_obat'] : '';
$id_obat = isset($_POST['id_obat']) ? $_POST['id_obat'] : '';
$jenis = isset($_POST['jenis']) ? $_POST['jenis'] : '';
$jumlah = isset($_POST['jumlah']) ? $_POST['jumlah'] : '';
$get_data = $db->query("SELECT nama FROM gobat WHERE id_obat='".$id_obat."'");
$data = $get_data->fetch(PDO::FETCH_ASSOC);
$namaobat = $data['nama'];
$ins_data = $db->prepare("INSERT INTO `paket_obat_bhp_detail`(`id_paket_ob`,`jenis_barang`, `id_obat`, `namaobat`, `jumlah_obat`, `created_at`) VALUES (:id_paket_ob,:jenis_barang,:id_obat,:namaobat,:jumlah_obat,:created_at)");
$ins_data->bindParam(":id_paket_ob",$id_paket_obat,PDO::PARAM_INT);
$ins_data->bindParam(":jenis_barang",$jenis,PDO::PARAM_STR);
$ins_data->bindParam(":id_obat",$id_obat,PDO::PARAM_INT);
$ins_data->bindParam(":namaobat",$namaobat,PDO::PARAM_STR);
$ins_data->bindParam(":jumlah_obat",$jumlah,PDO::PARAM_INT);
$ins_data->bindParam(":created_at",$today,PDO::PARAM_STR);
$ins_data->execute();

$feedback = [
    "status"=>"sukses",
    "title"=>"Berhasil!",
    "msg"=>"Data Berhasil Ditambahkan",
    "icon"=>"success",
];

echo json_encode($feedback);