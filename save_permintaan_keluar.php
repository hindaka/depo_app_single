<?php
//conn
session_start();
include("../inc/pdo.conf.php");
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
$id_parent = isset($_GET['parent']) ? $_GET['parent'] : '';
$mode = isset($_GET['mode']) ? $_GET['mode'] : 'draft';
$aktif = 'ya';
$mem_id = $r1['mem_id'];
if ($mode == 'draft') {
	header("location: transaksi_depo.php");
} else {
	// get list barang
	$get_list_barang = $db->query("SELECT bd.id_warehouse as id_depo,b.*,k.*,peg.nama as nama_peminta,w.nama_ruang FROM barangkeluar_depo_det b INNER JOIN barangkeluar_depo bd ON(b.id_barangkeluar_depo=bd.id_barangkeluar_depo) LEFT JOIN pegawai peg ON(bd.permintaan=peg.id_pegawai) LEFT JOIN warehouse w ON(bd.id_warehouse=w.id_warehouse) INNER JOIN kartu_stok_ruangan k ON(b.id_kartu=k.id_kartu_ruangan) WHERE b.id_barangkeluar_depo='" . $id_parent . "'");
	$list_barang = $get_list_barang->fetchAll(PDO::FETCH_ASSOC);
	// print_r($list_barang);
	foreach ($list_barang as $kartu) {
		$tujuan = isset($kartu['nama_ruang']) ? $kartu['nama_ruang'] : '';
		$id_obat = isset($kartu['id_obat']) ? $kartu['id_obat'] : '';
		$id_depo = isset($kartu['id_depo']) ? $kartu['id_depo'] : '';
		$sumber = isset($kartu['sumber_dana']) ? $kartu['sumber_dana'] : '-';
		$jenis = isset($kartu['jenis']) ? $kartu['jenis'] : '-';
		$merk = isset($kartu['merk']) ? $kartu['merk'] : '-';
		$pabrikan = isset($kartu['pabrikan']) ? $kartu['pabrikan'] : '-';
		$nama_peminta = isset($kartu['nama_peminta']) ? $kartu['nama_peminta'] : '-';
		$keterangan = "Permintaan dari " . $tujuan . " oleh " . $kartu['nama_peminta'];
		$volume_in = isset($kartu['volume']) ? $kartu['volume'] : '0';
		$volume_out = 0;
		$harga_beli = isset($kartu['harga_beli']) ? $kartu['harga_beli'] : '0';
		$created_at = date('Y-m-d H:i:s');
		$i = 0;
		$in_out = "masuk";
		//check warehouse_stok
		$get_check = $db->query("SELECT COUNT(*) as total FROM warehouse_stok WHERE id_warehouse='" . $id_depo . "' AND id_obat='" . $id_obat . "'");
		$check = $get_check->fetch(PDO::FETCH_ASSOC);
		if ($check['total'] == 1) {
			//sum
			$get_sum = $db->query("SELECT SUM(volume_kartu_akhir) as sisa FROM kartu_stok_ruangan WHERE id_warehouse='" . $id_depo . "' AND id_obat='" . $id_obat . "' AND in_out='masuk' AND volume_kartu_akhir>0");
			$sum = $get_sum->fetch(PDO::FETCH_ASSOC);
			$sisa_stok = isset($sum['sisa']) ? $sum['sisa'] : 0;
			$stmt = $db->query("UPDATE warehouse_stok SET stok=" . $sisa_stok . " WHERE id_warehouse='" . $id_depo . "' AND id_obat='" . $id_obat . "'");
		} else {
			$stmt = $db->prepare("INSERT INTO `warehouse_stok`(`id_warehouse`, `id_obat`, `stok`, `expired`, `no_batch`, `created_at`)VALUES (:id_warehouse,:id_obat,:stok,:expired,:no_batch,:created_at)");
			$stmt->bindParam(":id_warehouse", $id_depo, PDO::PARAM_INT);
			$stmt->bindParam(":id_obat", $id_obat, PDO::PARAM_INT);
			$stmt->bindParam(":stok", $volume_in, PDO::PARAM_INT);
			$stmt->bindParam(":expired", $kartu['expired'], PDO::PARAM_STR);
			$stmt->bindParam(":no_batch", $kartu['no_batch'], PDO::PARAM_STR);
			$stmt->bindParam(":created_at", $created_at, PDO::PARAM_STR);
			$stmt->execute();
		}

		$up_kartu = $db->query("UPDATE kartu_stok_ruangan SET in_out='keluar' WHERE id_kartu_ruangan='" . $kartu['id_kartu_ruangan'] . "'");
		//insert into warehouse yang dituju
		$ins_kartu = $db->prepare("INSERT INTO `kartu_stok_ruangan`(`id_kartu_gobat`,`id_obat`,`id_warehouse`,`sumber_dana`,`jenis`,`merk`,`pabrikan`,`volume_kartu_awal`, `volume_kartu_akhir`, `in_out`, `tujuan`, `volume_in`, `volume_out`, `harga_beli`,`harga_jual`,`id_tuslah`,`ket_tuslah`,`expired`,`no_batch`,`created_at`, `keterangan`,`ref`,`mem_id`) VALUES(:id_kartu_gobat,:id_barang,:id_warehouse,:sumber_dana,:jenis,:merk,:pabrikan,:volume_kartu_awal,:volume_kartu_akhir,:in_out,:tujuan,:volume_in,:volume_out,:harga_beli,:harga_jual,:id_tuslah,:ket_tuslah,:expired,:no_batch,:created_at,:keterangan,:ref,:mem_id)");
		$ins_kartu->bindParam(":id_kartu_gobat", $kartu['id_kartu_gobat'], PDO::PARAM_INT);
		$ins_kartu->bindParam(":id_barang", $id_obat, PDO::PARAM_INT);
		$ins_kartu->bindParam(":id_warehouse", $id_depo, PDO::PARAM_INT);
		$ins_kartu->bindParam(":sumber_dana", $sumber, PDO::PARAM_STR);
		$ins_kartu->bindParam(":jenis", $jenis, PDO::PARAM_STR);
		$ins_kartu->bindParam(":merk", $merk, PDO::PARAM_STR);
		$ins_kartu->bindParam(":pabrikan", $pabrikan, PDO::PARAM_STR);
		$ins_kartu->bindParam(":volume_kartu_awal", $volume_in, PDO::PARAM_INT);
		$ins_kartu->bindParam(":volume_kartu_akhir", $volume_in, PDO::PARAM_INT);
		$ins_kartu->bindParam(":in_out", $in_out, PDO::PARAM_STR);
		$ins_kartu->bindParam(":tujuan", $tujuan, PDO::PARAM_STR);
		$ins_kartu->bindParam(":volume_in", $volume_in, PDO::PARAM_INT);
		$ins_kartu->bindParam(":volume_out", $volume_out, PDO::PARAM_INT);
		$ins_kartu->bindParam(":harga_beli", $harga_beli, PDO::PARAM_INT);
		$ins_kartu->bindParam(":harga_jual", $kartu['harga_jual'], PDO::PARAM_INT);
		$ins_kartu->bindParam(":id_tuslah", $kartu['id_tuslah'], PDO::PARAM_INT);
		$ins_kartu->bindParam(":ket_tuslah", $kartu['ket_tuslah'], PDO::PARAM_INT);
		$ins_kartu->bindParam(":expired", $kartu['expired'], PDO::PARAM_STR);
		$ins_kartu->bindParam(":no_batch", $kartu['no_batch'], PDO::PARAM_STR);
		$ins_kartu->bindParam(":created_at", $created_at, PDO::PARAM_STR);
		$ins_kartu->bindParam(":keterangan", $keterangan, PDO::PARAM_STR);
		$ins_kartu->bindParam(":ref", $kartu['id_kartu'], PDO::PARAM_INT);
		$ins_kartu->bindParam(":mem_id", $mem_id, PDO::PARAM_INT);
		$ins_kartu->execute();
	}
	// //update parent status
	$up = $db->query("UPDATE barangkeluar_depo SET status_keluar='posting' WHERE id_barangkeluar_depo='" . $id_parent . "'");
	header("location: transaksi_depo.php?status=1");
}
