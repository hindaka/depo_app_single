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
echo "Sedang dalam proses...";
$nomedrek = isset($_GET['nomedrek']) ? $_GET['nomedrek'] : '';
$today = date('d/m/Y');
$tgl = date('Y-m-d H:i:s');
$tgl_before = date('d/m/Y', strtotime($tgl . ' -1 days'));
$status = "apotek";
//get id_pasien
$sql_pasien = "SELECT id_pasien,nama FROM registerpasien WHERE nomedrek=" . $nomedrek . " AND (tanggaldaftar='" . $today . "' OR tanggaldaftar='" . $tgl . "') ORDER BY id_pasien DESC LIMIT 1";
$get_pasien = $db->query($sql_pasien);
$pasien = $get_pasien->fetch(PDO::FETCH_ASSOC);

if ($pasien['nama'] != '') {
	//cek udah ada belum id_pasien dengan status masih apotek
	$sql_cek = $db->query("SELECT COUNT(*) as jml,ro.id_pasien FROM rincian_obat_pasien ro INNER JOIN registerpasien rp ON(rp.id_pasien=ro.id_pasien) WHERE ro.status='apotek' AND rp.nama ='" . $pasien['nama'] . "' AND rp.nomedrek='" . $nomedrek . "' GROUP BY ro.id_pasien");
	$cek = $sql_cek->fetch(PDO::FETCH_ASSOC);
	if ($cek['jml'] > 0) {
		//get id_rincian_obat
		$get_rincian = $db->query("SELECT id_rincian_obat FROM rincian_obat_pasien WHERE status='apotek' AND id_pasien='" . $cek['id_pasien'] . "' LIMIT 1");
		$rincian = $get_rincian->fetch(PDO::FETCH_ASSOC);
		echo "<script language=\"JavaScript\">window.location = \"obat_ranap_trans.php?id=" . $rincian['id_rincian_obat'] . "\"</script>";
	} else {
		//redirect to input dpjp
		echo "<script language=\"JavaScript\">window.location = \"obat_dpjp.php?id=" . $nomedrek . "\"</script>";
	}
} else {
	echo "<script language=\"JavaScript\">window.location = \"obat_ranap.php?status=4\"</script>";
}
