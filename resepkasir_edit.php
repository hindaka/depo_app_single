<?php
//conn
session_start();
include("../inc/pdo.conf.php");
include("../inc/version.php");
date_default_timezone_set("Asia/Jakarta");
ini_set('display_errors',1);
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
$id_resep=isset($_GET["id"]) ? $_GET['id'] : 'xxxxx';
$tanggalresep=date("d/m/Y");
try {

	$get_total = $db->prepare("SELECT SUM(total) as total FROM apotekkeluar WHERE id_resep=:id");
	$get_total->bindParam(":id",$id_resep,PDO::PARAM_INT);
	$get_total->execute();
	$all = $get_total->fetch(PDO::FETCH_ASSOC);

	//pembulatan
	$total_bayar=ceil($all['total']);
	$total_harga=ceil($all['total']);
	$ratusan = substr($total_harga, -3);
	if($ratusan<450){
		$total_harga = $total_harga - $ratusan;
	}else if(($ratusan>=450)&&($ratusan<950)){
		$total_harga = ($total_harga - $ratusan)+500;
	}else{
		$total_harga = $total_harga + (1000-$ratusan);
	}
	$selisih = $total_harga - $total_bayar;
	$update_inv = $db->prepare("UPDATE invoiceapotek SET totalbayar=:total,pembayaran_pasien=:bayar,selisih_pembulatan=:selisih WHERE id_resep=:id");
	$update_inv->bindParam(":total",$total_bayar,PDO::PARAM_INT);
	$update_inv->bindParam(":bayar",$total_harga,PDO::PARAM_INT);
	$update_inv->bindParam(":selisih",$selisih,PDO::PARAM_INT);
	$update_inv->bindParam(":id",$id_resep,PDO::PARAM_INT);
	$update_inv->execute();
	echo "<script language=\"JavaScript\">window.location = \"resep.php?status=1\"</script>";
} catch (PDOException $e) {
	echo "FAIL TO UPDATE RESEP: <br>";
	echo "Definition : ".$e->getMessage();
	echo "<br>Foto atau Capture ERROR halaman ini, segera lapor ke UNIT IT untuk segera diperbaiki!!";
}
?>
