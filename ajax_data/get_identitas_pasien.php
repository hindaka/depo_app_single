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
$q = isset($_GET['q']) ? $_GET['q'] : '';
$data_pasien = $db->query("SELECT id_master_pasien,nomedrek,nama,kelamin,tanggallahir FROM pasien WHERE nomedrek LIKE '%".$q."%' OR nama LIKE '%".$q."%'");
$all_pasien = $data_pasien->fetchAll(PDO::FETCH_ASSOC);
$total_data = $data_pasien->rowCount();
$data_feedback = [
    "incomplete_results" => false,
    "items" => [],
    "total_count" => $total_data,
];
$j = 0;
foreach ($all_pasien as $row) {
    $id = $row['id_master_pasien'];
    $text = $row['nama'] . " (" . $row['nomedrek'] . ")";
    $data_feedback["items"][$j] = [
        "id" => $id,
        "text" => $text
    ];
    $j++;
}
echo json_encode($data_feedback);
