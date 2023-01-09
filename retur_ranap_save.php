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
$tipe_depo = $tipes[2];
$conf = json_decode(file_get_contents("config/env_depo.json"), true);
$id_depo = $conf[$tipe_depo]["id_depo"];
$id_parent = isset($_POST['p']) ? $_POST['p'] : '';
$id_rincian_obat = isset($_POST['i']) ? $_POST['i'] : '';
$id_transaksi = isset($_POST['t']) ? $_POST['t'] : '';
$id_detail_rincian = isset($_POST['rincian']) ? $_POST['rincian'] : '';
$id_obat = isset($_POST['id_obat']) ? $_POST['id_obat'] : '';
$volume_real = isset($_POST['volume_out']) ? $_POST['volume_out'] : '0';
$retur = isset($_POST['jumlah_retur']) ? $_POST['jumlah_retur'] : '';
$petugas_retur = isset($_POST['petugas_retur']) ? $_POST['petugas_retur'] : '';
// $kartu = isset($_POST['kartu']) ? $_POST['kartu'] : '';
$id_petugas = $r1['mem_id'];
// echo $id_rincian_obat."<br>".$id_detail_rincian."<br>".$retur;
try {
	$db->beginTransaction();
	$get_parent = $db->query("SELECT * FROM parent_retur_obat WHERE id_parent_retur='" . $id_parent . "'");
	$parent = $get_parent->fetch(PDO::FETCH_ASSOC);
	$petugas_retur = $parent['petugas_retur'];
	//get data volume obat n harga
	if ($retur == $volume_real) {
		$volume_sisa = 0;
	} else {
		$volume_sisa = $volume_real - $retur;
	}
	$h3 = $db->query("SELECT ks.*,wo.id_warehouse_stok,g.nama,g.id_obat FROM warehouse_out wo INNER JOIN kartu_stok_ruangan ks ON(wo.id_kartu_ruangan=ks.id_kartu_ruangan) INNER JOIN warehouse_stok ws ON(wo.id_warehouse_stok=ws.id_warehouse_stok) INNER JOIN gobat g ON(g.id_obat=ws.id_obat) WHERE wo.id_detail_rincian='" . $id_detail_rincian . "'");
	$data_wo = $h3->rowCount();
	if ($data_wo > 0) {
		$r3 = $h3->fetch(PDO::FETCH_ASSOC);
		// var_dump($r3);
		$id_obat = $r3['id_obat'];
		$namaobat = $r3['nama'];
		$id_warehouse = $r3['id_warehouse'];
		$id_ref = $r3['ref'];
		$kartu = $r3['id_kartu_ruangan'];
		$jenis = $r3['jenis'];
		$merk = $r3['merk'];
		$pabrikan = $r3['pabrikan'];
		$id_warehouse_stok = $r3['id_warehouse_stok'];
		$volume_in = $retur;
		$in_out = "masuk";
		$tujuan = "-";
		$keterangan = "Retur Obat id_rincian/" . $id_rincian_obat;
		$created_at = date('Y-m-d H:i:s');
		$expired = isset($r3['expired']) ? $r3['expired'] : '';
		$ex_date = substr($expired, 0, 10);
		$volume_kartu_akhir = $retur;
		$volume_out = 0;
		$hargasatuan = $r3['harga_beli'] * 1.2;

		//get id_warehouse_stok
		// $stock_q = $db->query("SELECT * FROM warehouse_stok WHERE id_warehouse_stok='".$id_warehouse_stok."'");
		// $stock = $stock_q->fetch(PDO::FETCH_ASSOC);
		// // var_dump($stock);
		// $id_warehouse_stok = $stock['id_warehouse_stok'];
		//get tuslah
		$tuslah_list = $db->query("SELECT * FROM tuslah WHERE id_tuslah='" . $r3['id_tuslah'] . "'");
		$tus = $tuslah_list->fetch(PDO::FETCH_ASSOC);

		$tuslah_q = $db->query("SELECT tuslah FROM rincian_detail_obat WHERE id_detail_rincian='" . $id_detail_rincian . "'");
		$data_tuslah = $tuslah_q->fetch(PDO::FETCH_ASSOC);

		$tuslah = $data_tuslah['tuslah'];
		if ($volume_sisa > 0) {
			if ($tuslah == 1) {
				$hargatuslah = ($hargasatuan * $volume_sisa) + $tus['rajal'];
			} else if ($tuslah == 2) {
				$hargatuslah = ($hargasatuan * $volume_sisa) + $tus['rajal_racik'];
			} else if ($tuslah == 3) {
				$voltuslah = $volume_sisa * $tus['ranap'];
				$hargatuslah = ($hargasatuan * $volume_sisa) + $voltuslah;
			} else if ($tuslah == 4) {
				$voltuslah = $volume_out * $tus['ranap_racik'];
				$hargatuslah = ($hargasatuan * $volume_sisa) + $voltuslah;
			} else {
				$hargatuslah = $hargasatuan * $volume_sisa;
			}
		} else {
			$hargatuslah = 0;
		}
		//insert kartu retur
		$ins_kartu = $db->prepare("INSERT INTO `kartu_stok_ruangan`(`id_kartu_gobat`, `id_obat`, `id_warehouse`, `sumber_dana`,`jenis`,`merk`,`pabrikan`, `volume_kartu_awal`, `volume_kartu_akhir`, `volume_sisa`, `in_out`, `tujuan`, `volume_in`, `volume_out`, `expired`, `no_batch`, `harga_beli`, `harga_jual`, `id_tuslah`,`ket_tuslah`, `created_at`, `keterangan`,`ref`,`mem_id`) VALUES(:id_kartu_gobat,:id_obat,:id_warehouse,:sumber_dana,:jenis,:merk,:pabrikan,:volume_kartu_awal,:volume_kartu_akhir,:volume_sisa,:in_out,:tujuan,:volume_in,:volume_out,:expired,:no_batch,:harga_beli,:harga_jual,:id_tuslah,:ket_tuslah,:created_at,:keterangan,:ref,:mem_id)");
		$ins_kartu->bindParam(":id_kartu_gobat", $r3['id_kartu_gobat'], PDO::PARAM_INT);
		$ins_kartu->bindParam(":id_obat", $id_obat, PDO::PARAM_INT);
		$ins_kartu->bindParam(":id_warehouse", $id_warehouse, PDO::PARAM_INT);
		$ins_kartu->bindParam(":sumber_dana", $r3['sumber_dana'], PDO::PARAM_STR);
		$ins_kartu->bindParam(":jenis", $r3['jenis'], PDO::PARAM_STR);
		$ins_kartu->bindParam(":merk", $r3['merk'], PDO::PARAM_STR);
		$ins_kartu->bindParam(":pabrikan", $r3['pabrikan'], PDO::PARAM_STR);
		$ins_kartu->bindParam(":volume_kartu_awal", $r3['volume_kartu_awal'], PDO::PARAM_INT);
		$ins_kartu->bindParam(":volume_kartu_akhir", $volume_kartu_akhir, PDO::PARAM_INT);
		$ins_kartu->bindParam(":volume_sisa", $volume_kartu_akhir, PDO::PARAM_INT);
		$ins_kartu->bindParam(":in_out", $in_out, PDO::PARAM_STR);
		$ins_kartu->bindParam(":tujuan", $tujuan, PDO::PARAM_STR);
		$ins_kartu->bindParam(":volume_in", $volume_in, PDO::PARAM_INT);
		$ins_kartu->bindParam(":volume_out", $volume_out, PDO::PARAM_INT);
		$ins_kartu->bindParam(":expired", $ex_date, PDO::PARAM_STR);
		$ins_kartu->bindParam(":no_batch", $r3['no_batch'], PDO::PARAM_STR);
		$ins_kartu->bindParam(":harga_beli", $r3['harga_beli'], PDO::PARAM_INT);
		$ins_kartu->bindParam(":harga_jual", $hargatuslah, PDO::PARAM_INT);
		$ins_kartu->bindParam(":id_tuslah", $r3['id_tuslah'], PDO::PARAM_INT);
		$ins_kartu->bindParam(":ket_tuslah", $r3['ket_tuslah'], PDO::PARAM_INT);
		$ins_kartu->bindParam(":created_at", $created_at, PDO::PARAM_STR);
		$ins_kartu->bindParam(":keterangan", $keterangan, PDO::PARAM_STR);
		$ins_kartu->bindParam(":ref", $kartu, PDO::PARAM_INT);
		$ins_kartu->bindParam(":mem_id", $id_petugas, PDO::PARAM_INT);
		$ins_kartu->execute();
		$harga_retur = $retur * $hargasatuan;
		//insert data retur
		$history_retur = $db->prepare("INSERT INTO rincian_retur_obat(id_detail_rincian,id_parent_retur,nama_obat,jumlah_retur,harga_jual,harga_tuslah,petugas_retur,petugas_input)
	VALUES(:id_detail_rincian,:id_parent_retur,:nama_obat,:retur,:harga_jual,:harga_tuslah,:petugas_retur,:petugas_input)");
		$history_retur->bindParam(":id_detail_rincian", $id_detail_rincian, PDO::PARAM_INT);
		$history_retur->bindParam(":id_parent_retur", $id_parent, PDO::PARAM_INT);
		$history_retur->bindParam(":nama_obat", $namaobat, PDO::PARAM_STR);
		$history_retur->bindParam(":retur", $retur, PDO::PARAM_INT);
		$history_retur->bindParam(":harga_jual", $hargasatuan, PDO::PARAM_INT);
		$history_retur->bindParam(":harga_tuslah", $harga_retur, PDO::PARAM_INT);
		$history_retur->bindParam(":petugas_retur", $petugas_retur, PDO::PARAM_STR);
		$history_retur->bindParam(":petugas_input", $id_petugas, PDO::PARAM_INT);
		$history_retur->execute();
		//update volume kartu_akhir
		$up_awal = $db->query("UPDATE kartu_stok_ruangan SET volume_kartu_akhir=volume_kartu_akhir+" . $retur . " WHERE id_kartu_ruangan='" . $id_ref . "'");
		//update detail_rincian_obat
		$up_detail = $db->query("UPDATE rincian_detail_obat SET volume=" . $volume_sisa . ",harga_satuan='" . $hargasatuan . "',sub_total='" . $hargatuslah . "' WHERE id_detail_rincian='" . $id_detail_rincian . "'");
		// //update warehouse_out
		$up_ware = $db->query("UPDATE warehouse_out SET volume=" . $volume_out . ",harga_beli='" . $r3['harga_beli'] . "',harga_satuan='" . $hargasatuan . "',total_harga='" . $hargatuslah . "' WHERE id_detail_rincian='" . $id_detail_rincian . "'");
		// //update warehouse_stok
		$get_sisa = $db->query("SELECT SUM(volume_kartu_akhir) AS total FROM kartu_stok_ruangan WHERE id_obat='" . $id_obat . "' AND id_warehouse='" . $id_depo . "' AND in_out='masuk' AND volume_kartu_akhir>0");
		$sisa_stok = $get_sisa->fetch(PDO::FETCH_ASSOC);
		$stok_retur = $sisa_stok['total'];
		$up_stok = $db->query("UPDATE warehouse_stok SET stok=" . $stok_retur . " WHERE id_warehouse_stok='" . $id_warehouse_stok . "'");
	} else {
		//no data kartu
		//get data kartu terakhir
		$get_kartu = $db->query("SELECT * FROM kartu_stok_ruangan ks INNER JOIN gobat g ON(ks.id_obat=g.id_obat) WHERE ks.in_out='masuk' AND ks.id_obat='" . $id_obat . "' AND ks.id_warehouse='" . $id_depo . "' ORDER BY ks.created_at DESC LIMIT 1");
		$kartu = $get_kartu->fetch(PDO::FETCH_ASSOC);
		$id_obat = $kartu['id_obat'];
		$namaobat = $kartu['nama'];
		$id_warehouse = $kartu['id_warehouse'];
		$id_ref = $kartu['ref'];
		$kartu_ref = $kartu['id_kartu_ruangan'];
		$volume_in = $retur;
		$in_out = "masuk";
		$tujuan = "-";
		$keterangan = "Retur Obat id_rincian/" . $id_rincian_obat;
		$created_at = date('Y-m-d H:i:s');
		$volume_kartu_akhir = $retur;
		$volume_out = 0;
		$hargasatuan = $kartu['harga_beli'] * 1.2;
		//get tuslah
		$tuslah_list = $db->query("SELECT * FROM tuslah WHERE aktif='y'");
		$tus = $tuslah_list->fetch(PDO::FETCH_ASSOC);

		$get_detail = $db->query("SELECT rd.*,g.nama FROM rincian_detail_obat rd INNER JOIN gobat g ON(rd.id_obat=g.id_obat) WHERE rd.id_detail_rincian='" . $id_detail_rincian . "'");
		$detail = $get_detail->fetch(PDO::FETCH_ASSOC);
		// $volume_out = $detail['volume'] - $retur;
		$tuslah = $detail['tuslah'];
		$hargasatuan = $detail['harga_satuan'];
		// $namaobat = $detail['nama'];

		if ($volume_sisa > 0) {
			if ($tuslah == 1) {
				$hargatuslah = ($hargasatuan * $volume_sisa) + $tus['rajal'];
			} else if ($tuslah == 2) {
				$hargatuslah = ($hargasatuan * $volume_sisa) + $tus['rajal_racik'];
			} else if ($tuslah == 3) {
				$voltuslah = $volume_sisa * $tus['ranap'];
				$hargatuslah = ($hargasatuan * $volume_sisa) + $voltuslah;
			} else if ($tuslah == 4) {
				$voltuslah = $volume_out * $tus['ranap_racik'];
				$hargatuslah = ($hargasatuan * $volume_sisa) + $voltuslah;
			} else {
				$hargatuslah = $hargasatuan * $volume_sisa;
			}
		} else {
			$hargatuslah = 0;
		}
		//insert kartu retur
		$ins_kartu = $db->prepare("INSERT INTO `kartu_stok_ruangan`(`id_obat`, `id_warehouse`, `sumber_dana`, `volume_kartu_awal`, `volume_kartu_akhir`, `volume_sisa`, `in_out`, `tujuan`, `volume_in`, `volume_out`, `expired`, `no_batch`, `harga_beli`, `harga_jual`, `id_tuslah`,`ket_tuslah`, `created_at`, `keterangan`,`ref`,`mem_id`) VALUES(:id_obat,:id_warehouse,:sumber_dana,:volume_kartu_awal,:volume_kartu_akhir,:volume_sisa,:in_out,:tujuan,:volume_in,:volume_out,:expired,:no_batch,:harga_beli,:harga_jual,:id_tuslah,:ket_tuslah,:created_at,:keterangan,:ref,:mem_id)");
		$ins_kartu->bindParam(":id_obat", $id_obat, PDO::PARAM_INT);
		$ins_kartu->bindParam(":id_warehouse", $id_warehouse, PDO::PARAM_INT);
		$ins_kartu->bindParam(":sumber_dana", $kartu['sumber_dana'], PDO::PARAM_STR);
		$ins_kartu->bindParam(":volume_kartu_awal", $kartu['volume_kartu_awal'], PDO::PARAM_INT);
		$ins_kartu->bindParam(":volume_kartu_akhir", $volume_kartu_akhir, PDO::PARAM_INT);
		$ins_kartu->bindParam(":volume_sisa", $volume_kartu_akhir, PDO::PARAM_INT);
		$ins_kartu->bindParam(":in_out", $in_out, PDO::PARAM_STR);
		$ins_kartu->bindParam(":tujuan", $tujuan, PDO::PARAM_STR);
		$ins_kartu->bindParam(":volume_in", $volume_in, PDO::PARAM_INT);
		$ins_kartu->bindParam(":volume_out", $volume_out, PDO::PARAM_INT);
		$ins_kartu->bindParam(":expired", $kartu['expired'], PDO::PARAM_STR);
		$ins_kartu->bindParam(":no_batch", $kartu['no_batch'], PDO::PARAM_STR);
		$ins_kartu->bindParam(":harga_beli", $kartu['harga_beli'], PDO::PARAM_INT);
		$ins_kartu->bindParam(":harga_jual", $hargatuslah, PDO::PARAM_INT);
		$ins_kartu->bindParam(":id_tuslah", $kartu['id_tuslah'], PDO::PARAM_INT);
		$ins_kartu->bindParam(":ket_tuslah", $kartu['ket_tuslah'], PDO::PARAM_INT);
		$ins_kartu->bindParam(":created_at", $created_at, PDO::PARAM_STR);
		$ins_kartu->bindParam(":keterangan", $keterangan, PDO::PARAM_STR);
		$ins_kartu->bindParam(":ref", $kartu_ref, PDO::PARAM_INT);
		$ins_kartu->bindParam(":mem_id", $id_petugas, PDO::PARAM_INT);
		$ins_kartu->execute();
		$harga_retur = $retur * $hargasatuan;
		//insert data retur
		$history_retur = $db->prepare("INSERT INTO rincian_retur_obat(id_detail_rincian,id_parent_retur,nama_obat,jumlah_retur,harga_jual,harga_tuslah,petugas_retur,petugas_input)
	VALUES(:id_detail_rincian,:id_parent_retur,:nama_obat,:retur,:harga_jual,:harga_tuslah,:petugas_retur,:petugas_input)");
		$history_retur->bindParam(":id_detail_rincian", $id_detail_rincian, PDO::PARAM_INT);
		$history_retur->bindParam(":id_parent_retur", $id_parent, PDO::PARAM_INT);
		$history_retur->bindParam(":nama_obat", $namaobat, PDO::PARAM_STR);
		$history_retur->bindParam(":retur", $retur, PDO::PARAM_INT);
		$history_retur->bindParam(":harga_jual", $hargasatuan, PDO::PARAM_INT);
		$history_retur->bindParam(":harga_tuslah", $harga_retur, PDO::PARAM_INT);
		$history_retur->bindParam(":petugas_retur", $petugas_retur, PDO::PARAM_STR);
		$history_retur->bindParam(":petugas_input", $id_petugas, PDO::PARAM_INT);
		$history_retur->execute();
		//update detail_rincian_obat
		$up_detail = $db->query("UPDATE rincian_detail_obat SET volume=" . $volume_sisa . ",harga_satuan='" . $hargasatuan . "',sub_total='" . $hargatuslah . "' WHERE id_detail_rincian='" . $id_detail_rincian . "'");
	}
	$get_sum = $db->query("SELECT SUM(sub_total) as subtotal FROM rincian_detail_obat WHERE id_trans_obat='" . $id_transaksi . "' AND id_rincian='" . $id_rincian_obat . "'");
	$sum = $get_sum->fetch(PDO::FETCH_ASSOC);
	$up = $db->query("UPDATE rincian_transaksi_obat SET biaya_trans=" . $sum['subtotal'] . " WHERE id_trans_obat='" . $id_transaksi . "'");
	$db->commit();
	echo "<script language=\"JavaScript\">window.location = \"obat_ranap_retur_search.php?p=" . $id_parent . "&id=" . $id_rincian_obat . "&task=retur\"</script>";
} catch (PDOException $e) {
	$db->rollBack();
	echo $e->getMessage();
}
