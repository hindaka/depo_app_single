<?php
//conn
session_start();
include("../inc/pdo.conf.php");
include("../inc/version.php");
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
include "../inc/anggota_check.php";
//send message to front if loading
echo "Sedang dalam proses...";
//get id_detail_rincian
$id_rincian = isset($_GET['r']) ? $_GET['r'] : '';
$id_transaksi = isset($_GET['t']) ? $_GET['t'] : '';
$id_detail_rincian = isset($_GET['rincian']) ? $_GET['rincian'] : '';
//get
$get_detail = $db->query("SELECT * FROM rincian_detail_obat WHERE id_detail_rincian='".$id_detail_rincian."'");
$detail = $get_detail->fetch(PDO::FETCH_ASSOC);
$volume_return = $detail['volume'];
$id_obat = $detail['id_obat'];
// //get data rincian
$data_trans = $db->query("SELECT ruang FROM rincian_transaksi_obat WHERE id_trans_obat='".$id_transaksi."'");
$ruang = $data_trans->fetch(PDO::FETCH_ASSOC);
$cek_trans = $db->query("SELECT * FROM rincian_transaksi_obat ro INNER JOIN warehouse w ON(ro.id_warehouse=w.id_warehouse) WHERE ro.id_trans_obat='".$id_transaksi."'");
$total_trans = $cek_trans->rowCount();
if($total_trans>0){
	//ada
	$trans = $cek_trans->fetch(PDO::FETCH_ASSOC);
	$id_warehouse = $trans['id_warehouse'];
	$asal_input = $trans['nama_ruang'];
	$get_wo = $db->query("SELECT * FROM warehouse_out WHERE id_detail_rincian='".$id_detail_rincian."'");
	$total_wo = $get_wo->rowCount();
	if($total_wo>0){
		$wo = $get_wo->fetch(PDO::FETCH_ASSOC);
		$id_kartu_ruangan = $wo['id_kartu_ruangan'];
		$id_warehouse_out = $wo['id_warehouse_out'];
		$id_warehouse_stok = $wo['id_warehouse_stok'];
		$data_kartu = $db->query("SELECT * FROM kartu_stok_ruangan WHERE id_kartu_ruangan='".$id_kartu_ruangan."'");
		$kartu = $data_kartu->fetch(PDO::FETCH_ASSOC);
		$ref_id = $kartu['ref'];
		// update stok
		$up_stok_warehouse = $db->query("UPDATE warehouse_stok SET stok=stok+".$volume_return." WHERE id_warehouse_stok='".$id_warehouse_stok."'");
		//normalize kartu_stok
		$up_stok = $db->query("UPDATE kartu_stok_ruangan SET volume_kartu_akhir=volume_kartu_akhir+".$volume_return." WHERE id_kartu_ruangan='".$ref_id."'");
		//delete data di kartu_stok_ruangan
		$del_kartu = $db->query("DELETE FROM kartu_stok_ruangan WHERE id_kartu_ruangan='".$id_kartu_ruangan."'");
		//check ada retur atau tidak
		$del_retur = $db->query("DELETE FROM kartu_stok_ruangan WHERE ref='".$id_kartu_ruangan."' AND in_out='retur'");
		// hapus rincian_detail_obat
		$del_rincian = $db->query("DELETE FROM rincian_detail_obat WHERE id_detail_rincian='".$id_detail_rincian."'");
		//hapus warehouse_out
		$del_ware = $db->query("DELETE FROM warehouse_out WHERE id_warehouse_out='".$id_warehouse_out."'");
		echo "<script language=\"JavaScript\">window.location = \"obat_ranap_detail.php?id=".$id_rincian."&t=".$id_transaksi."&r=".$ruang['ruang']."&status=5\"</script>";
	}else{
		//tidak terdaftar dikartu
		$del_rincian = $db->query("DELETE FROM rincian_detail_obat WHERE id_detail_rincian='".$id_detail_rincian."'");
		echo "<script language=\"JavaScript\">window.location = \"obat_ranap_detail.php?id=".$id_rincian."&t=".$id_transaksi."&r=".$ruang['ruang']."&status=5\"</script>";
	}
}else{
	//kosong / bukan dari farmasi
	// hapus rincian_detail_obat
	$del_rincian = $db->query("DELETE FROM rincian_detail_obat WHERE id_detail_rincian='".$id_detail_rincian."'");
	echo "<script language=\"JavaScript\">window.location = \"obat_ranap_detail.php?id=".$id_rincian."&t=".$id_transaksi."&r=".$ruang['ruang']."&status=5\"</script>";
}
?>
