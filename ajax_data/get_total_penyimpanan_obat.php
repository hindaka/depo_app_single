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
$tipe_depo = $tipes[2];
$conf = json_decode(file_get_contents("../config/env_depo.json"),true);
$id_depo = $conf[$tipe_depo]["id_depo"];
$exclude_query = $db->query("SELECT COUNT(*) as total FROM (SELECT ks.id_obat,g.nama,g.sumber FROM kartu_stok_ruangan ks INNER JOIN gobat g ON(ks.id_obat=g.id_obat) WHERE ks.id_warehouse='".$id_depo."' GROUP BY id_obat) a WHERE a.id_obat NOT IN (SELECT id_obat FROM conf_detail_penyimpanan cf INNER JOIN conf_penyimpanan_obat cp ON(cf.id_conf_obat=cp.id_conf_obat) WHERE cp.id_warehouse='".$id_depo."')");
$ex = $exclude_query->fetch(PDO::FETCH_ASSOC);
$ex_total = isset($ex['total']) ? $ex['total'] : 0;
$include_query = $db->query("SELECT COUNT(*) as total FROM conf_detail_penyimpanan cf INNER JOIN conf_penyimpanan_obat cp ON(cf.id_conf_obat=cp.id_conf_obat) WHERE cp.id_warehouse='".$id_depo."'");
$inc = $include_query->fetch(PDO::FETCH_ASSOC);
$inc_total = isset($inc['total']) ? $inc['total'] : 0;
$feed_back = [
  "ex"=>$ex_total,
  "inc"=>$inc_total,
];
echo json_encode($feed_back);
