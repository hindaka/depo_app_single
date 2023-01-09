<?php
session_start();
include("../../inc/pdo.conf.php");
include("../../inc/version.php");
include("../../inc/set_gfarmasi.php");
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
$nama_depo = $conf[$tipe_depo]["nama_depo"];
$keyword = isset($_GET['q']) ? $_GET['q'] : '';
$h4 = $db->query("SELECT a.id_obat,a.nama,SUM(a.volume_kartu_akhir) as stok,a.no_batch,a.expired,a.sumber_dana,a.jenis, a.merk_pabrik FROM (SELECT k.id_obat,g.nama,g.kadar,g.satuan_kadar,g.satuan_jual,g.kemasan,k.volume_kartu_akhir,k.no_batch,k.expired,k.sumber_dana,k.jenis, CASE WHEN(k.jenis='generik') THEN k.pabrikan WHEN(k.jenis='non generik') THEN k.merk ELSE k.merk END AS 'merk_pabrik' FROM kartu_stok_ruangan k INNER JOIN gobat g ON(k.id_obat=g.id_obat) WHERE k.volume_kartu_akhir>0 AND k.in_out='masuk' AND k.id_warehouse='" . $id_depo . "') as a WHERE a.nama LIKE '%" . $keyword . "%' OR a.merk_pabrik LIKE '%" . $keyword . "%' GROUP BY a.id_obat,a.jenis,a.merk_pabrik ORDER BY a.nama ASC");
$data4 = $h4->fetchAll(PDO::FETCH_ASSOC);
$total_data = $h4->rowCount();
$groups = [];
if ($total_data > 0) {
    foreach ($data4 as $row) {
        $id = $row['id_obat'] . "|" . $row['jenis'] . "|" . $row['merk_pabrik'];
        $nama = isset($row['nama']) ? $row['nama'] : '';
        $kadar = isset($row['kadar']) ? $row['kadar'] : '';
        $satuan_kadar = isset($row['satuan_kadar']) ? $row['satuan_kadar'] : '';
        $satuan_jual = isset($row['satuan_jual']) ? $row['satuan_jual'] : '';
        $kemasan = isset($row['kemasan']) ? $row['kemasan'] : '';
        $jenis = isset($row['jenis']) ? $row['jenis'] : '';
        $merk = isset($row['merk_pabrik']) ? $row['merk_pabrik'] : '';
        $pabrikan = isset($row['merk_pabrik']) ? $row['merk_pabrik'] : '';
        $nama_barang = viewNamaBarang($nama, $kadar, $satuan_kadar, $satuan_jual, $kemasan, $jenis, $pabrikan, $merk);
        $item = [
            "id" => $id,
            "id_obat" => $row['id_obat'],
            "nama_obat" => $row['nama'],
            "sumber_dana" => $row['sumber_dana'],
            "jenis" => $row['jenis'],
            "merk_pabrik" => $row['merk_pabrik'],
            "no_batch" => $row['no_batch'],
            "expired" => $row['expired'],
            "volume_kartu_akhir" => $row['stok']
        ];
        array_push($groups, $item);
    }
}
$feedback = [
    "total_count" => $total_data,
    "incomplete_results" => false,
    "items" => $groups
];
echo json_encode($feedback);
