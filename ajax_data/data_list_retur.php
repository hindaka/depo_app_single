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
$id_parent_retur = isset($_GET['p']) ? $_GET['p'] : '';
$stmt = $db->prepare("SELECT rb.*,a.nama,ro.jenis,ro.merk,ro.pabrikan FROM rincian_retur_obat rb INNER JOIN rincian_detail_obat ro ON(rb.id_detail_rincian=ro.id_detail_rincian) INNER JOIN anggota a ON(rb.petugas_input=a.mem_id) WHERE rb.id_parent_retur=:id ORDER BY rb.created_at ASC");
$stmt->bindParam(":id", $id_parent_retur, PDO::PARAM_INT);
$stmt->execute();
$result = $stmt->fetchAll(PDO::FETCH_ASSOC);
$all_data['data'] = array();
foreach ($result as $d) {
	$jenis = isset($d['jenis']) ? $d['jenis'] : '';
	$merk = isset($d['merk']) ? $d['merk'] : '';
	$pabrikan = isset($d['pabrikan']) ? $d['pabrikan'] : '';
	if ($jenis == 'generik') {
		$merk_pabrikan = $pabrikan;
	} else if ($jenis == 'non generik') {
		$merk_pabrikan = $merk;
	} else if ($jenis == 'bmhp') {
		if ($merk != '') {
			$merk_pabrikan = $merk;
		} else {
			$merk_pabrikan = $pabrikan;
		}
	} else {
		$merk_pabrikan = '-';
	}
	$item = array(
		"id_parent_retur" => $d['id_parent_retur'],
		"nama_obat" => $d['nama_obat'],
		"jenis" => $d['jenis'],
		"merk_pabrikan" => $merk_pabrikan,
		"jumlah_retur" => $d['jumlah_retur'],
		"harga_jual" => $d['harga_jual'],
		"nama_petugas" => $d['nama'],
		"created_at" => $d['created_at']
	);
	array_push($all_data['data'], $item);
}
echo json_encode($all_data);
