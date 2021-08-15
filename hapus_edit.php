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
//get var
$id_warehouse_out = isset($_GET['id']) ? $_GET['id'] : '';
$id_kartu_ruangan = isset($_GET['kartu']) ? $_GET['kartu'] : '';
$id_resep = isset($_GET['resep']) ? $_GET['resep'] : '';
try {
	//get data
	$data = $db->query("SELECT * FROM warehouse_out WHERE id_warehouse_out='".$id_warehouse_out."'");
	$item = $data->fetch(PDO::FETCH_ASSOC);
	//get data from kartu_stok_ruangan
	$reference = $db->query("SELECT id_obat,id_warehouse,ref,volume_out FROM kartu_stok_ruangan WHERE id_kartu_ruangan='".$item['id_kartu_ruangan']."'");
	$ref = $reference->fetch(PDO::FETCH_ASSOC);
	$ref_id = $ref['ref'];
	$volume_return = $ref['volume_out'];
	$id_obat = $ref['id_obat'];
	$id_warehouse = $ref['id_warehouse'];
	$up_stok = $db->query("UPDATE kartu_stok_ruangan SET volume_kartu_akhir=volume_kartu_akhir+".$volume_return." WHERE id_kartu_ruangan='".$ref_id."'");
	//update stok di warehouse_stok
	$get_stok = $db->prepare("SELECT SUM(volume_kartu_akhir) as sisa_stok FROM kartu_stok_ruangan WHERE id_obat=:obat AND id_warehouse=:ware AND in_out='masuk'");
	$get_stok->bindParam(":obat",$id_obat,PDO::PARAM_INT);
	$get_stok->bindParam(":ware",$id_warehouse,PDO::PARAM_INT);
	$get_stok->execute();
	$stok = $get_stok->fetch(PDO::FETCH_ASSOC);
	$up_ware_stok = $db->prepare("UPDATE warehouse_stok SET stok=:stok WHERE id_obat=:obat AND id_warehouse=:ware");
	$up_ware_stok->bindParam(":stok",$stok['sisa_stok'],PDO::PARAM_INT);
	$up_ware_stok->bindParam(":obat",$id_obat,PDO::PARAM_INT);
	$up_ware_stok->bindParam(":ware",$id_warehouse,PDO::PARAM_INT);
	$up_ware_stok->execute();
	//delete data di kartu_stok_ruangan
	$del_kartu = $db->query("DELETE FROM kartu_stok_ruangan WHERE id_kartu_ruangan='".$item['id_kartu_ruangan']."'");
	//delete data di apotekkeluar base on nama,volume,resep
	$del_apotek = $db->query("DELETE FROM apotekkeluar WHERE id_obatkeluar='".$item['id_obatkeluar']."'");
	//delete data di warehouse_out
	$del_ware = $db->query("DELETE FROM warehouse_out WHERE id_warehouse_out='".$id_warehouse_out."'");
	//optimize 3 tables kartu_stok_ruangan,apotekkeluar,warehouse_out

	echo "<script language=\"JavaScript\">window.location = \"keluar_edit.php?id=$id_resep\"</script>";

} catch (PDOException $e) {
	echo $e->getMessage();
}
?>
