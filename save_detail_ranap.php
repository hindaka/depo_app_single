<?php
session_start();
include("../inc/pdo.conf.php");
include("../inc/version.php");
ini_set('display_errors', 1);
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
//get passing data form
$id_rincian = isset($_GET['id']) ? $_GET['id'] : '';
$id_transaksi = isset($_GET['t']) ? $_GET['t'] : '';
$dpjp = isset($_POST['dpjp']) ? $_POST['dpjp'] : '';
$volume_input = isset($_POST['volume']) ? $_POST['volume'] : '';
$selectedItem = isset($_POST['namaobat']) ? $_POST['namaobat'] : '';
$split = explode("|", $selectedItem);
$id_kartu_ruangan = isset($split[0]) ? $split[0] : 0;
$id_obat = isset($split[1]) ? $split[1] : 0;
$stok_sisa = isset($split[2]) ? $split[2] : 0;
$id_warehouse = isset($split[3]) ? $split[3] : 0;
$sumber = isset($_POST['sumber']) ? $_POST['sumber'] : '';
$tuslah = isset($_POST['tuslah']) ? $_POST['tuslah'] : '5';
$ruang = isset($_POST['ruang']) ? $_POST['ruang'] : '';
$today = isset($_POST['today']) ? $_POST['today'] : date('d-m-Y');
$id_petugas = $r1['mem_id'];
$jenis_rawat = "ranap";
$new_date = explode('/', $today);
$hours = date('H:i:s');
$tanggal_out = $new_date[2] . "-" . $new_date[1] . "-" . $new_date[0] . " " . $hours;
//get nama pasien
$data_pasien = $db->query("SELECT rp.nama,rp.nomedrek FROM rincian_obat_pasien ro INNER JOIN registerpasien rp ON(ro.id_pasien=rp.id_pasien) WHERE ro.id_rincian_obat='" . $id_rincian . "'");
$pasien = $data_pasien->fetch(PDO::FETCH_ASSOC);
$nama_pasien = isset($pasien['nama']) ? $pasien['nama'] : '';
$nomedrek = isset($pasien['nomedrek']) ? $pasien['nomedrek'] : '';
//get data obat
$h3 = $db->query("SELECT * FROM warehouse_stok ws INNER JOIN gobat g ON(g.id_obat=ws.id_obat) WHERE ws.id_warehouse='" . $id_warehouse . "' AND ws.id_obat='" . $id_obat . "'");
$r3 = $h3->fetch(PDO::FETCH_ASSOC);
$stok_ruangan = $r3["stok"];
$namaobat = $r3['nama'];
$id_warehouse_stok = $r3['id_warehouse_stok'];
// obat tidak cukup
if ($volume_input > $stok_ruangan || $stok_ruangan <= 0) {
	echo "<script language=\"JavaScript\">window.location = \"obat_ranap_detail.php?id=" . $id_rincian . "&t=" . $id_transaksi . "&r=" . $ruang . "&status=3\"</script>";
}
//getlist kartu_stok_ruangan
$kartu_list = $db->query("SELECT * FROM kartu_stok_ruangan WHERE id_kartu_ruangan='" . $id_kartu_ruangan . "'");
$kartu = $kartu_list->fetch(PDO::FETCH_ASSOC);
$total_data = $kartu_list->rowCount();
$sisa_keluar = 0;
$tujuan = "Pasien " . $nama_pasien . " (" . $nomedrek . ")";
$keterangan = "id_trans/" . $id_transaksi;
$volume_in = 0;
$created_at = date('Y-m-d H:i:s');
$i = 0;
$in_out = "keluar";
$id_obatkeluar = 0;
$id_resep = 0;
$id_tuslah = isset($kartu['id_tuslah']) ? $kartu['id_tuslah'] : '';
$harga_beli = isset($kartu['harga_beli']) ? $kartu['harga_beli'] : 0;
$hargasatuan = $harga_beli * 1.2;
$volume_kartu = isset($kartu['volume_kartu_akhir']) ? $kartu['volume_kartu_akhir'] : 0;
$volume_kartu_akhir = $volume_kartu - $volume_input;
$merk = isset($kartu['merk']) ? $kartu['merk'] : '';
$sumber = isset($kartu['sumber_dana']) ? $kartu['sumber_dana'] : '';
$jenis = isset($kartu['jenis']) ? $kartu['jenis'] : '';
$pabrikan = isset($kartu['pabrikan']) ? $kartu['pabrikan'] : '';
$volume_out = $volume_input;
$volume_akhir = 0;
$stok_ruangan = $stok_ruangan - $volume_input;
//get tuslah yang aktif
$tuslah_list = $db->query("SELECT * FROM tuslah WHERE id_tuslah='" . $id_tuslah . "'");
$tus = $tuslah_list->fetch(PDO::FETCH_ASSOC);
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
$volume_kartu_awal = isset($kartu['volume_kartu_awal']) ? $kartu['volume_kartu_awal'] : 0;
$expired = isset($kartu['expired']) ? $kartu['expired'] : '';
$no_batch = isset($kartu['no_batch']) ? $kartu['no_batch'] : '';
//update volume_kartu_akhir berdasarkan data on point
$update_vol = $db->query("UPDATE kartu_stok_ruangan SET volume_kartu_akhir='" . $volume_kartu_akhir . "' WHERE id_kartu_ruangan='" . $id_kartu_ruangan . "'");
//insert into kartu_stok_ruangan in_out=keluar
$ins_kartu = $db->prepare("INSERT INTO `kartu_stok_ruangan`(`id_kartu_gobat`, `id_obat`, `id_warehouse`, `sumber_dana`,`merk`,`jenis`,`pabrikan`, `volume_kartu_awal`, `volume_kartu_akhir`, `volume_sisa`, `in_out`, `tujuan`, `volume_in`, `volume_out`, `expired`, `no_batch`, `harga_beli`, `harga_jual`, `id_tuslah`,`ket_tuslah`, `created_at`, `keterangan`,`ref`,`mem_id`) VALUES(:id_kartu_gobat,:id_obat,:id_warehouse,:sumber_dana,:merk,:jenis,:pabrikan,:volume_kartu_awal,:volume_kartu_akhir,:volume_sisa,:in_out,:tujuan,:volume_in,:volume_out,:expired,:no_batch,:harga_beli,:harga_jual,:id_tuslah,:ket_tuslah,:created_at,:keterangan,:ref,:mem_id)");
$ins_kartu->bindParam(":id_kartu_gobat", $id_kartu_gobat, PDO::PARAM_INT);
$ins_kartu->bindParam(":id_obat", $id_obat, PDO::PARAM_INT);
$ins_kartu->bindParam(":id_warehouse", $id_warehouse, PDO::PARAM_INT);
$ins_kartu->bindParam(":sumber_dana", $sumber, PDO::PARAM_STR);
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
$ins_kartu->bindParam(":ket_tuslah", $tuslah, PDO::PARAM_INT);
$ins_kartu->bindParam(":created_at", $created_at, PDO::PARAM_STR);
$ins_kartu->bindParam(":keterangan", $keterangan, PDO::PARAM_STR);
$ins_kartu->bindParam(":ref", $id_kartu_ruangan, PDO::PARAM_INT);
$ins_kartu->bindParam(":mem_id", $id_petugas, PDO::PARAM_INT);
$ins_kartu->execute();
$id_kartu_ruangan = $db->lastInsertId();
//insert detail rincian
$sql = "";
$ins_rincian = $db->prepare("INSERT INTO rincian_detail_obat (ruang,id_obat,nama_obat,merk,sumber_dana,jenis,pabrikan,expired,no_batch,volume,harga_satuan,sub_total,id_rincian,id_trans_obat,tuslah,mem_id)VALUES(:ruang,:id_obat,:nama_obat,:merk,:sumber_dana,:jenis,:pabrikan,:expired,:no_batch,:volume,:harga_satuan,:sub_total,:id_rincian,:id_transaksi,:tuslah,:mem_id)");
$ins_rincian->bindParam(":ruang", $ruang, PDO::PARAM_INT);
$ins_rincian->bindParam(":id_obat", $id_obat, PDO::PARAM_INT);
$ins_rincian->bindParam(":nama_obat", $namaobat, PDO::PARAM_STR);
$ins_rincian->bindParam(":merk", $merk, PDO::PARAM_STR);
$ins_rincian->bindParam(":sumber_dana", $sumber, PDO::PARAM_STR);
$ins_rincian->bindParam(":jenis", $jenis, PDO::PARAM_STR);
$ins_rincian->bindParam(":pabrikan", $pabrikan, PDO::PARAM_STR);
$ins_rincian->bindParam(":no_batch", $no_batch, PDO::PARAM_STR);
$ins_rincian->bindParam(":expired", $expired, PDO::PARAM_STR);
$ins_rincian->bindParam(":volume", $volume_out, PDO::PARAM_INT);
$ins_rincian->bindParam(":harga_satuan", $hargasatuan);
$ins_rincian->bindParam(":sub_total", $hargatuslah);
$ins_rincian->bindParam(":id_rincian", $id_rincian, PDO::PARAM_INT);
$ins_rincian->bindParam(":id_transaksi", $id_transaksi, PDO::PARAM_INT);
$ins_rincian->bindParam(":tuslah", $tuslah, PDO::PARAM_INT);
$ins_rincian->bindParam(":mem_id", $id_petugas, PDO::PARAM_INT);
$ins_rincian->execute();
$id_detail_rincian = $db->lastInsertId();
// warehouse keluar
$ins_out = $db->prepare("INSERT INTO `warehouse_out`(`tanggal_keluar`,`id_kartu_ruangan`,`id_obatkeluar`,`id_warehouse_stok`, `volume`, `harga_beli`, `harga_satuan`, `total_harga`,`jenis_rawat`,`id_resep`,`id_detail_rincian`) VALUES
(:tanggal_keluar,:id_kartu_ruangan,:id_obatkeluar,:id_warehouse_stok,:volume,:harga_beli,:harga_satuan,:total_harga,:jenis_rawat,:id_resep,:id_detail_rincian)");
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
$ins_out->bindParam(":id_detail_rincian", $id_detail_rincian, PDO::PARAM_INT);
$ins_out->execute();
//UPDATE
$up = $db->query("SELECT SUM(volume_kartu_akhir) as total FROM kartu_stok_ruangan WHERE id_warehouse='" . $id_warehouse . "' AND id_obat='" . $id_obat . "' AND in_out='masuk' AND volume_kartu_akhir>0");
$stok = $up->fetch(PDO::FETCH_ASSOC);
$stok_total = isset($stok['total']) ? $stok['total'] : 0;
$up_ware = $db->query("UPDATE warehouse_stok SET stok='" . $stok_total . "' WHERE id_warehouse='" . $id_warehouse . "' AND id_obat='" . $id_obat . "'");
echo "<script language=\"JavaScript\">window.location = \"obat_ranap_detail.php?id=" . $id_rincian . "&t=" . $id_transaksi . "&r=" . $ruang . "&status=1\"</script>";
