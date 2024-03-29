<?php
session_start();
include("../inc/pdo.conf.php");
include("../inc/version.php");
ini_set('display_errors', '1');
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
$id_resep = isset($_GET['id']) ? $_GET["id"] : '';
$tanggal = isset($_POST['tgglkeluar']) ? $_POST["tgglkeluar"] : '';
$selectedObat = isset($_POST['namaobat']) ? $_POST['namaobat'] : '';
$sp = explode("|", $selectedObat);
$id_kartu_ruangan = isset($sp[0]) ? $sp[0] : 0;
$id_obat = isset($sp[1]) ? $sp[1] : 0;
$volume_kartu_akhir = isset($sp[2]) ? $sp[2] : 0;
$id_warehouse = isset($sp[3]) ? $sp[3] : 0;
$volume_input = isset($_POST['volume']) ? $_POST["volume"] : '';
$tuslah = isset($_POST['tuslah']) ? $_POST["tuslah"] : '';
$jenis_rawat = "rajal";
$new_date = explode('/', $tanggal);
$tanggal_out = $new_date[2] . "-" . $new_date[1] . "-" . $new_date[0];
$id_petugas = $r1['mem_id'];
//ambil value obat
$h3 = $db->query("SELECT ws.stok,g.nama,ws.id_warehouse_stok,ws.id_warehouse FROM warehouse_stok ws INNER JOIN gobat g ON(g.id_obat=ws.id_obat) WHERE ws.id_obat='" . $id_obat . "' AND ws.id_warehouse='" . $id_warehouse . "'");
$r3 = $h3->fetch(PDO::FETCH_ASSOC);
$stok_ruangan = $r3["stok"];
$namaobat = $r3['nama'];
$id_warehouse = $r3['id_warehouse'];
$id_warehouse_stok = $r3['id_warehouse_stok'];

if ($volume_input > $stok_ruangan || $stok_ruangan <= 0) {
	header("location:keluar.php?id=$id_resep&status=3");
	exit;
}
//getlist kartu_stok_ruangan
$kartu_list = $db->query("SELECT * FROM kartu_stok_ruangan WHERE id_kartu_ruangan='" . $id_kartu_ruangan . "'");
$kartu = $kartu_list->fetch(PDO::FETCH_ASSOC);
$total_data = $kartu_list->rowCount();
$sisa_keluar = 0;
$tujuan = "Pasien";
$keterangan = "Resep/" . $id_resep;
$volume_in = 0;
$created_at = date('Y-m-d H:i:s');
$i = 0;
$in_out = "keluar";
// var_dump($kartu[$i]);
//get tuslah yang aktif
$tuslah_list = $db->query("SELECT * FROM tuslah WHERE id_tuslah='" . $kartu['id_tuslah'] . "'");
$tus = $tuslah_list->fetch(PDO::FETCH_ASSOC);
$harga_beli = isset($kartu['harga_beli']) ? $kartu['harga_beli'] : 0;
$hargasatuan = $harga_beli * 1.2;
$volume_kartu = isset($kartu['volume_kartu_akhir']) ? $kartu['volume_kartu_akhir'] : 0;
$volume_kartu_akhir = $volume_kartu - $volume_input;
$volume_out = $volume_input;
$stok_ruangan = $stok_ruangan - $volume_input;
$volume_akhir = 0;
$id_kartu_awal = $kartu['id_kartu_ruangan'];
//logika tuslah
if ($tuslah == 1) {
	$hargatuslah = ($hargasatuan * $volume_out) + $tus['rajal'];
} else if ($tuslah == 2) {
	$hargatuslah = ($hargasatuan * $volume_out) + $tus['rajal_racik'];
} else if ($tuslah == 3) {
	$voltuslah = $volume_out * $tus['ranap'];
	$hargatuslah = ($hargasatuan * $volume_out) + $voltuslah;
} else if ($tuslah == 4) {
	$voltuslah = $volume_out * $tus['ranap_racik'];
	$hargatuslah = ($hargasatuan * $volume_out) + $voltuslah;
} else {
	$hargatuslah = $hargasatuan * $volume_out;
}
$id_kartu_gobat = isset($kartu['id_kartu_gobat']) ? $kartu['id_kartu_gobat'] : 0;
$sumber_dana = isset($kartu['sumber_dana']) ? $kartu['sumber_dana'] : 0;
$volume_kartu_awal = isset($kartu['volume_kartu_awal']) ? $kartu['volume_kartu_awal'] : 0;
$merk = isset($kartu['merk']) ? $kartu['merk'] : 0;
$jenis = isset($kartu['jenis']) ? $kartu['jenis'] : 0;
$pabrikan = isset($kartu['pabrikan']) ? $kartu['pabrikan'] : 0;
$expired = isset($kartu['expired']) ? $kartu['expired'] : 0;
$no_batch = isset($kartu['id_kartu_gobat']) ? $kartu['id_kartu_gobat'] : 0;
$id_tuslah = isset($kartu['id_tuslah']) ? $kartu['id_tuslah'] : 0;
$ket_tuslah = isset($kartu['ket_tuslah']) ? $kartu['ket_tuslah'] : 0;

//update volume_kartu_akhir berdasarkan data on point
$update_vol = $db->query("UPDATE kartu_stok_ruangan SET volume_kartu_akhir='" . $volume_kartu_akhir . "' WHERE id_kartu_ruangan='" . $id_kartu_awal . "'");
//insert into kartu_stok_ruangan in_out=keluar
$ins_kartu = $db->prepare("INSERT INTO `kartu_stok_ruangan`(`id_kartu_gobat`, `id_obat`, `id_warehouse`, `sumber_dana`,`jenis`,`merk`,`pabrikan`, `volume_kartu_awal`, `volume_kartu_akhir`, `volume_sisa`, `in_out`, `tujuan`, `volume_in`, `volume_out`, `expired`, `no_batch`, `harga_beli`, `harga_jual`, `id_tuslah`,`ket_tuslah`, `created_at`, `keterangan`,`ref`,`mem_id`) VALUES(:id_kartu_gobat,:id_obat,:id_warehouse,:sumber_dana,:jenis,:merk,:pabrikan,:volume_kartu_awal,:volume_kartu_akhir,:volume_sisa,:in_out,:tujuan,:volume_in,:volume_out,:expired,:no_batch,:harga_beli,:harga_jual,:id_tuslah,:ket_tuslah,:created_at,:keterangan,:ref,:mem_id)");
$ins_kartu->bindParam(":id_kartu_gobat", $id_kartu_gobat, PDO::PARAM_INT);
$ins_kartu->bindParam(":id_obat", $id_obat, PDO::PARAM_INT);
$ins_kartu->bindParam(":id_warehouse", $id_warehouse, PDO::PARAM_INT);
$ins_kartu->bindParam(":sumber_dana", $sumber_dana, PDO::PARAM_STR);
$ins_kartu->bindParam(":merk", $merk, PDO::PARAM_STR);
$ins_kartu->bindParam(":jenis", $jenis, PDO::PARAM_STR);
$ins_kartu->bindParam(":pabrikan", $pabrikan, PDO::PARAM_STR);
$ins_kartu->bindParam(":volume_kartu_awal", $volume_kartu_awal, PDO::PARAM_INT);
$ins_kartu->bindParam(":volume_kartu_akhir", $volume_akhir, PDO::PARAM_INT);
$ins_kartu->bindParam(":volume_sisa", $volume_akhir, PDO::PARAM_INT);
$ins_kartu->bindParam(":in_out", $in_out, PDO::PARAM_STR);
$ins_kartu->bindParam(":tujuan", $tujuan, PDO::PARAM_STR);
$ins_kartu->bindParam(":volume_in", $volume_in, PDO::PARAM_INT);
$ins_kartu->bindParam(":volume_out", $volume_out, PDO::PARAM_INT);
$ins_kartu->bindParam(":expired", $expired, PDO::PARAM_STR);
$ins_kartu->bindParam(":no_batch", $no_batch, PDO::PARAM_STR);
$ins_kartu->bindParam(":harga_beli", $harga_beli);
$ins_kartu->bindParam(":harga_jual", $hargatuslah);
$ins_kartu->bindParam(":id_tuslah", $id_tuslah, PDO::PARAM_INT);
$ins_kartu->bindParam(":ket_tuslah", $ket_tuslah, PDO::PARAM_INT);
$ins_kartu->bindParam(":created_at", $created_at, PDO::PARAM_STR);
$ins_kartu->bindParam(":keterangan", $keterangan, PDO::PARAM_STR);
$ins_kartu->bindParam(":ref", $id_kartu_awal, PDO::PARAM_INT);
$ins_kartu->bindParam(":mem_id", $id_petugas, PDO::PARAM_INT);
$ins_kartu->execute();
$id_kartu_ruangan = $db->lastInsertId();
// insert into apotekkeluar
$result2 = $db->query("INSERT INTO apotekkeluar(id_resep,tanggal,id_obat,namaobat,merk,jenis,pabrikan,sumber,volume,harga,total,tuslah,no_batch,expired_date) VALUES ('" . $id_resep . "','" . $tanggal . "','" . $id_obat . "','" . $namaobat . "','$merk','$jenis','$pabrikan','" . $sumber_dana . "','" . $volume_out . "','" . $hargasatuan . "','" . $hargatuslah . "','" . $tuslah . "','" . $no_batch . "','" . $expired . "')");
$id_obatkeluar = $db->lastInsertId();
// warehouse keluar
$ins_out = $db->prepare("INSERT INTO `warehouse_out`(`tanggal_keluar`,`id_kartu_ruangan`,`id_obatkeluar`,`id_warehouse_stok`, `volume`, `harga_beli`, `harga_satuan`, `total_harga`,`jenis_rawat`,`id_resep`) VALUES
(:tanggal_keluar,:id_kartu_ruangan,:id_obatkeluar,:id_warehouse_stok,:volume,:harga_beli,:harga_satuan,:total_harga,:jenis_rawat,:id_resep)");
$ins_out->bindParam(":tanggal_keluar", $tanggal_out, PDO::PARAM_STR);
$ins_out->bindParam(":id_kartu_ruangan", $id_kartu_ruangan, PDO::PARAM_INT);
$ins_out->bindParam(":id_obatkeluar", $id_obatkeluar, PDO::PARAM_INT);
$ins_out->bindParam(":id_warehouse_stok", $id_warehouse_stok, PDO::PARAM_INT);
$ins_out->bindParam(":volume", $volume_out, PDO::PARAM_INT);
$ins_out->bindParam(":harga_beli", $harga_beli);
$ins_out->bindParam(":harga_satuan", $hargasatuan);
$ins_out->bindParam(":total_harga", $hargatuslah);
$ins_out->bindParam(":jenis_rawat", $jenis_rawat, PDO::PARAM_STR);
$ins_out->bindParam(":id_resep", $id_resep, PDO::PARAM_INT);
$ins_out->execute();
echo "<script language=\"JavaScript\">window.location = \"keluar.php?id=$id_resep\"</script>";
