<?php
//conn
session_start();
include("../inc/pdo.conf.php");
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
//get var
$id_barangkeluar_depo_det=isset($_GET["id"]) ? $_GET['id'] : '';
$id_kartu = isset($_GET['kartu']) ? $_GET['kartu'] : '';
$id_barangkeluar_depo = isset($_GET['parent']) ? $_GET['parent'] : '';
try {
	//get reference
	$reference = $db->query("SELECT id_obat,ref,volume_out FROM kartu_stok_ruangan WHERE id_kartu_ruangan='".$id_kartu."'");
	$ref = $reference->fetch(PDO::FETCH_ASSOC);
	$ref_id = $ref['ref'];
	$volume_return = $ref['volume_out'];
	$up_stok = $db->query("UPDATE kartu_stok_ruangan SET volume_kartu_akhir=volume_kartu_akhir+".$volume_return." WHERE id_kartu_ruangan='".$ref_id."'");
	//delete data di kartu
	$del_kartu = $db->prepare("DELETE FROM kartu_stok_ruangan WHERE id_kartu_ruangan=:id_kartu");
	$del_kartu->bindParam(":id_kartu",$id_kartu,PDO::PARAM_INT);
	$del_kartu->execute();
	//delete data obat keluar
	$del_keluar = $db->prepare("DELETE FROM barangkeluar_depo_det WHERE id_barangkeluar_depo_det=:id_keluar");
	$del_keluar->bindParam(":id_keluar",$id_barangkeluar_depo_det,PDO::PARAM_INT);
	$del_keluar->execute();
	header("location: input_permintaan_depo.php?parent=".$id_barangkeluar_depo."&status=2");
} catch (PDOException $e) {
	echo "Fail to delete data : Fail ON(".$e->getMessage().")";
}
?>
