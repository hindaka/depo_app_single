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
$id_det_so = isset($_POST['id_det_so']) ? $_POST['id_det_so'] : '';
$tipe_depo = $tipes[2];
$conf = json_decode(file_get_contents("../config/env_depo.json"),true);
$id_depo = $conf[$tipe_depo]["id_depo"];
$get_data = $db->query("SELECT sd.*,so.created_at as tanggal_awal FROM stok_opname_det sd INNER JOIN stok_opname so ON(sd.id_parent_so=so.id_so) WHERE id_det_so='".$id_det_so."'");
$data_so = $get_data->fetch(PDO::FETCH_ASSOC);
$tanggal_awal = $data_so['tanggal_awal'];
$id_kartu_ruangan = $data_so['id_kartu'];
$id_obat= $data_so['id_obat'];
$no_batch = $data_so['no_batch'];
$sum_data = $db->query("SELECT IFNULL(SUM(volume_out),0) as total_keluar FROM `kartu_stok_ruangan` WHERE id_obat='".$id_obat."' AND no_batch LIKE '".$no_batch."' AND created_at>'".$tanggal_awal."' AND in_out='keluar' AND id_warehouse='".$id_depo."'");
$data_pengeluaran = $sum_data->fetch(PDO::FETCH_ASSOC);
$total_keluar = isset($data_pengeluaran['total_keluar']) ? $data_pengeluaran['total_keluar'] : 0;
$sum_masuk = $db->query("SELECT IFNULL(SUM(volume_in),0) as total_masuk FROM `kartu_stok_ruangan` WHERE id_obat='".$id_obat."' AND no_batch LIKE '".$no_batch."' AND created_at>'".$tanggal_awal."' AND in_out='masuk' AND id_warehouse='".$id_depo."'");
$data_masuk = $sum_masuk->fetch(PDO::FETCH_ASSOC);
$total_masuk = isset($data_masuk['total_masuk']) ? $data_masuk['total_masuk'] : 0;
$feedback = [
	"keluar"=>$total_keluar,
	"masuk"=>$total_masuk
];
echo json_encode($feedback);
