<?php
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
echo "Sedang dalam proses...";
$id_invoice_all = isset($_POST['id_invoice_all']) ? $_POST['id_invoice_all'] : '';
$id_register = isset($_POST['id_register']) ? $_POST['id_register'] : '';
$tindakan = isset($_POST['tindakan']) ? $_POST['tindakan'] : '';
$drp = isset($_POST['drp']) ? $_POST['drp'] : '-';
$rekomendasi = isset($_POST['rekomendasi']) ? $_POST['rekomendasi'] : '-';
$tanggal_pelayanan = isset($_POST['tanggal_pelayanan']) ? $_POST['tanggal_pelayanan'] : '';
$petugas = $r1['mem_id'];
$asal='farmasi';
$user_tindakan = 0;
$id_tindakan = 0;
$vol = 1;
$inv_in = 'n';
$get_tarif = $db->prepare("SELECT nama,harga FROM tarif WHERE id_tarif=:id");
$get_tarif->bindParam(":id",$tindakan,PDO::PARAM_INT);
$get_tarif->execute();
$tarif = $get_tarif->fetch(PDO::FETCH_ASSOC);
$jperiksa = $tarif['nama'];
$harga = $tarif['harga'];
$check_inv = $db->query("SELECT COUNT(*) as total_rec FROM invoice_all_det WHERE id_invoice_all='".$id_invoice_all."' AND jperiksa LIKE '".$jperiksa."'");
$check = $check_inv->fetch(PDO::FETCH_ASSOC);
if($check['total_rec']>0){
	$ins_log = $db->prepare("INSERT INTO `farmasi_pelayanan`(`id_register`,`tanggal_pelayanan`,`nama_pelayanan`, `petugas`, `inv_in`)VALUES (:id_register,:tanggal,:nama_pelayanan,:petugas,:inv_in)");
	$ins_log->bindParam(":id_register",$id_register,PDO::PARAM_INT);
	$ins_log->bindParam(":tanggal",$tanggal_pelayanan,PDO::PARAM_STR);
	$ins_log->bindParam(":nama_pelayanan",$jperiksa,PDO::PARAM_STR);
	$ins_log->bindParam(":petugas",$petugas,PDO::PARAM_INT);
	$ins_log->bindParam(":inv_in",$inv_in,PDO::PARAM_STR);
	$ins_log->execute();
}else{
	$inv_in='y';
	$stmt = $db->prepare("INSERT INTO `invoice_all_det`(`id_invoice_all`, `jperiksa`, `volume`, `harga`, `id_tindakan`, `asal`, `user_tindakan`)VALUES (:id_invoice_all,:jperiksa,:volume,:harga,:id_tindakan,:asal,:user_tindakan)");
	$stmt->bindParam(":id_invoice_all",$id_invoice_all,PDO::PARAM_INT);
	$stmt->bindParam(":jperiksa",$jperiksa,PDO::PARAM_STR);
	$stmt->bindParam(":volume",$vol,PDO::PARAM_INT);
	$stmt->bindParam(":harga",$harga,PDO::PARAM_INT);
	$stmt->bindParam(":id_tindakan",$id_tindakan,PDO::PARAM_INT);
	$stmt->bindParam(":asal",$asal,PDO::PARAM_STR);
	$stmt->bindParam(":user_tindakan",$user_tindakan,PDO::PARAM_INT);
	$stmt->execute();
	$last_insert_id = $db->lastInsertId();
	$ins_log = $db->prepare("INSERT INTO `farmasi_pelayanan`(`id_register`,`tanggal_pelayanan`,`drp`,`rekomendasi`,`nama_pelayanan`, `petugas`, `inv_in`, `id_invoice_all_det`)VALUES (:id_register,:tanggal,:drp,:rekomendasi,:nama_pelayanan,:petugas,:inv_in,:id_invoice_all_det)");
	$ins_log->bindParam(":id_register",$id_register,PDO::PARAM_INT);
	$ins_log->bindParam(":tanggal",$tanggal_pelayanan,PDO::PARAM_STR);
	$ins_log->bindParam(":drp",$drp,PDO::PARAM_STR);
	$ins_log->bindParam(":rekomendasi",$rekomendasi,PDO::PARAM_STR);
	$ins_log->bindParam(":nama_pelayanan",$jperiksa,PDO::PARAM_STR);
	$ins_log->bindParam(":petugas",$petugas,PDO::PARAM_INT);
	$ins_log->bindParam(":inv_in",$inv_in,PDO::PARAM_STR);
	$ins_log->bindParam(":id_invoice_all_det",$last_insert_id,PDO::PARAM_INT);
	$ins_log->execute();
	// update total tagihan
	$get_total = $db->query("SELECT SUM(harga) as total FROM invoice_all_det WHERE id_invoice_all='".$id_invoice_all."' GROUP BY id_invoice_all");
	$total =$get_total->fetch(PDO::FETCH_ASSOC);
	$up_inv = $db->query("UPDATE invoice_all SET total_tagihan=".$total['total']." WHERE id_invoice_all='".$id_invoice_all."'");
}
echo "<script language=\"JavaScript\">window.location = \"tindakan_pasien_log.php?inv=".$id_invoice_all."&reg=".$id_register."&status=1\"</script>";
