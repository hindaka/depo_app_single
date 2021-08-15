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
$conf = json_decode(file_get_contents("config/env_depo.json"), true);
$id_depo = $conf[$tipe_depo]["id_depo"];
//get list obat
$get_data_obat = $db->query("SELECT g.nama FROM kartu_stok_ruangan kr INNER JOIN gobat g ON(kr.id_obat=g.id_obat) WHERE kr.id_warehouse='".$id_depo."' GROUP BY g.nama ORDER BY g.nama ASC");