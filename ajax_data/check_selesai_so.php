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
$id_so = isset($_POST['id_so']) ? $_POST['id_so'] : '';
try {
	$db->beginTransaction();
	//check data yang belum koreksi
	$stmt = $db->query("SELECT COUNT(*) AS total FROM stok_opname_det WHERE id_parent_so='" . $id_so . "' AND koreksi='n'");
	$data = $stmt->fetch(PDO::FETCH_ASSOC);
	$total_data = isset($data['total']) ? $data['total'] : 0;
	if ($data['total'] > 0) {
		$title = "Peringatan!";
		$txt = "Ada " . $data['total'] . " yang belum dikoreksi!\nSilakan selesaikan dulu semua!";
		$icon = "warning";
	} else {
		$title = "Selamat!";
		$txt = "Seluruh data sudah dikoreksi!\nSilakan lanjutkan.";
		$icon = "success";
	}
	$feedback = array(
		"status" => "sukses",
		"title" => $title,
		"text" => $txt,
		"icon" => $icon,
		"total_data" => $total_data
	);
	$db->commit();
} catch (PDOException $e) {
	$db->rollBack();
	$feedback = array(
		"status" => "error",
		"title" => "Error!!",
		"text" => $e->getMessage(),
		"icon" => "error",
		"total_data" => 0
	);
}
echo json_encode($feedback);
