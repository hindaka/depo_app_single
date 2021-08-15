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
$id_paket_ob = isset($_POST['id_detail']) ? $_POST['id_detail'] : '';
$del_data = $db->query("DELETE FROM paket_obat_bhp_detail WHERE id_paket_ob_det='".$id_paket_ob."'");
$feedback = [
    "status"=>"sukses",
    "title"=>"Berhasil!",
    "msg"=>"Data Berhasil dihapus",
    "icon"=>"success",
];

echo json_encode($feedback);