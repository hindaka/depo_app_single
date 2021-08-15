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
$id_paket_ob = isset($_GET['id']) ? $_GET['id'] : '';
$task = isset($_GET['task']) ? $_GET['task'] : '';
$feedback = [];
$i = 1;
$get_data_detail = $db->query("SELECT * FROM paket_obat_bhp_detail WHERE id_paket_ob='" . $id_paket_ob . "' AND jenis_barang='" . $task . "'");
$data_detail = $get_data_detail->fetchAll(PDO::FETCH_ASSOC);
if ($get_data_detail->rowCount() > 0) {
    foreach ($data_detail as $row) {
        $items = [
            $i++,
            $row['namaobat'],
            $row['jumlah_obat'],
            '<button id="'.$task.$row['id_paket_ob_det'].'" onclick="hapusData(this)" data-id="'.$row['id_paket_ob_det'].'" class="btn btn-sm btn-danger"><i class="fa fa-trash"></i></button>'
        ];
        array_push($feedback, $items);
    }
}
echo json_encode($feedback);
