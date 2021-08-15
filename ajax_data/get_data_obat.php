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
	header("location:../index.php?status=2");
	exit;
}
include "../../inc/anggota_check.php";
$q = isset($_GET['q']) ? $_GET['q'] : '';
$tipe_depo = $tipes[2];
$conf = json_decode(file_get_contents("../config/env_depo.json"),true);
$id_depo = $conf[$tipe_depo]["id_depo"];
$data_obat = $db->query("SELECT a.* FROM (SELECT ks.id_obat,g.nama,g.sumber FROM kartu_stok_ruangan ks INNER JOIN gobat g ON(ks.id_obat=g.id_obat) WHERE ks.id_warehouse='".$id_depo."' AND g.nama LIKE '%".$q."%' GROUP BY id_obat) a WHERE a.id_obat NOT IN (SELECT id_obat FROM conf_detail_penyimpanan cf INNER JOIN conf_penyimpanan_obat cp ON(cf.id_conf_obat=cp.id_conf_obat) WHERE cp.id_warehouse='".$id_depo."')");
$all_obat = $data_obat->fetchAll(PDO::FETCH_ASSOC);
$total_data = $data_obat->rowCount();
$data_feedback = [
  "incomplete_results"=>false,
  "items"=>[],
  "total_count"=>$total_data,
];
$j=0;
foreach ($all_obat as $row) {
  $text = $row['nama']." - ".$row['sumber'];
  $data_feedback["items"][$j] = [
    "id"=>$row['id_obat'],
    "text"=>$text
  ];
  $j++;
}
echo json_encode($data_feedback);
