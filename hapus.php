<?php
//conn
session_start();
include("../inc/pdo.conf.php");
include("../inc/version.php");
date_default_timezone_set("Asia/Jakarta");
$namauser = $_SESSION['namauser'];
$password = $_SESSION['password'];
$tipe = $_SESSION['tipe'];
$tipes = explode('-', $tipe);
if ($tipes[0] != 'DepoApp') {
	unset($_SESSION['tipe']);
	unset($_SESSION['namauser']);
	unset($_SESSION['password']);
	header("location:../index.php?status=2");
	exit;
}
include "../inc/anggota_check.php";
//get var
$id_warehouse_out = isset($_GET['id']) ? $_GET['id'] : '';
$id_kartu_ruangan = isset($_GET['kartu']) ? $_GET['kartu'] : '';
$id_resep = isset($_GET['resep']) ? $_GET['resep'] : '';
//get data
$data = $db->query("SELECT * FROM warehouse_out WHERE id_warehouse_out='" . $id_warehouse_out . "'");
$item = $data->fetch(PDO::FETCH_ASSOC);
//get data from kartu_stok_ruangan
$reference = $db->query("SELECT id_obat,ref,volume_out FROM kartu_stok_ruangan WHERE id_kartu_ruangan='" . $item['id_kartu_ruangan'] . "'");
$ref = $reference->fetch(PDO::FETCH_ASSOC);
$ref_id = $ref['ref'];
$volume_return = $ref['volume_out'];
try {
	$db->beginTransaction();
	$up_stok = $db->query("UPDATE kartu_stok_ruangan SET volume_kartu_akhir=volume_kartu_akhir+" . $volume_return . " WHERE id_kartu_ruangan='" . $ref_id . "'");
	//delete data di kartu_stok_ruangan
	$del_kartu = $db->query("DELETE FROM kartu_stok_ruangan WHERE id_kartu_ruangan='" . $item['id_kartu_ruangan'] . "'");
	//delete data di apotekkeluar base on nama,volume,resep
	$del_apotek = $db->query("DELETE FROM apotekkeluar WHERE id_obatkeluar='" . $item['id_obatkeluar'] . "'");
	//delete data di warehouse_out
	$del_ware = $db->query("DELETE FROM warehouse_out WHERE id_warehouse_out='" . $id_warehouse_out . "'");
	echo "<script language=\"JavaScript\">window.location = \"keluar.php?id=$id_resep\"</script>";
	$db->commit();
} catch (PDOException $pd) {
	$db->rollback();
	echo $pd->getMessage();
}
