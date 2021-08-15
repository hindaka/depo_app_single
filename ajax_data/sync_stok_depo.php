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
$id_obat = isset($_POST['id_obat']) ? $_POST['id_obat'] : '';
$id_warehouse = isset($_POST['id_warehouse']) ? $_POST['id_warehouse'] : '';
$sumber = isset($_POST['sumber']) ? $_POST['sumber'] : 'APBD';

// check data
$check_warehouse = $db->query("SELECT count(*) as total_data FROM warehouse_stok WHERE id_obat='" . $id_obat . "' AND id_warehouse='" . $id_warehouse . "'");
$check = $check_warehouse->fetch(PDO::FETCH_ASSOC);
if ($check['total_data'] == 1) {
    //data ditemukan
    //sum
    $get_sum = $db->query("SELECT IFNULL(SUM(volume_kartu_akhir),0) as sisa FROM kartu_stok_ruangan WHERE id_warehouse='" . $id_warehouse . "' AND id_obat='" . $id_obat . "' AND in_out='masuk' AND volume_kartu_akhir>0");
    $sum = $get_sum->fetch(PDO::FETCH_ASSOC);
    $sisa_stok = $sum['sisa'];
    $stmt = $db->query("UPDATE warehouse_stok SET stok='" . $sisa_stok . "' WHERE id_warehouse='" . $id_warehouse . "' AND id_obat='" . $id_obat . "'");
    $feedback = [
        "status"=>"sukses",
        "msg"=>"Sinkronisasi Data Berhasil dilakukan",
        "title"=>"Berhasil !!",
        "icon"=>"success"
    ];
} else {
    // data tidak ditemukan
    $feedback = [
        "status"=>"gagal",
        "msg"=>"Sinkronisasi Data gagal dilakukan, data warehouse tidak ditemukan",
        "title"=>"Peringatan !!",
        "icon"=>"warning"
    ];
}
echo json_encode($feedback);