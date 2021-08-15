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
//get var
$id_resep=isset($_GET["id"]) ? base64_decode($_GET['id']) : '';
$jambayar=date("H:i");
$sekarang=date("d-m-Y");
$tanggalbayar=date("d/m/Y");
$status = 'Batal';
$id_petugas = $r1['mem_id'];
$ket_rtt = "Obat dibeli diluar";
//get data
$data = $db->query("SELECT * FROM warehouse_out wo INNER JOIN apotekkeluar ap ON(ap.id_obatkeluar=wo.id_obatkeluar) WHERE ap.id_resep='".$id_resep."'");
$allData = $data->fetchAll(PDO::FETCH_ASSOC);
if(count($allData)>0){
	foreach ($allData as $item) {
		//get data from kartu_stok_ruangan
		$reference = $db->query("SELECT ks.id_obat,g.nama,ks.id_warehouse,ks.ref,ks.volume_out FROM kartu_stok_ruangan ks INNER JOIN gobat g ON(ks.id_obat=g.id_obat) WHERE ks.id_kartu_ruangan='".$item['id_kartu_ruangan']."'");
		$ref = $reference->fetch(PDO::FETCH_ASSOC);
		$ref_id = $ref['ref'];
		$volume_return = $ref['volume_out'];
		$id_obat = $ref['id_obat'];
		$id_warehouse = $ref['id_warehouse'];
		$nama_obat = $ref['nama'];
		$stmt = $db->prepare("INSERT INTO `resep_rtt`(`id_resep`, `nama_obat`, `ket`, `mem_id`) VALUES (:id_resep,:nama_obat,:ket,:mem_id)");
		$stmt->bindParam(':id_resep',$id_resep,PDO::PARAM_INT);
		$stmt->bindParam(':nama_obat',$nama_obat,PDO::PARAM_STR);
		$stmt->bindParam(':ket',$ket_rtt,PDO::PARAM_STR);
		$stmt->bindParam(':mem_id',$id_petugas,PDO::PARAM_INT);
		$stmt->execute();
		//update stok kartu
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
		//delete data di warehouse_out
		$del_ware = $db->query("DELETE FROM warehouse_out WHERE id_warehouse_out='".$id_warehouse_out."'");
		//delete data di apotekkeluar base on nama,volume,resep
		// $del_apotek = $db->query("DELETE FROM apotekkeluar WHERE id_obatkeluar='".$item['id_obatkeluar']."'");
		//update
		$up_invoice = $db->query("SELECT * FROM invoiceapotek WHERE id_resep='".$id_resep."'");
		$inv = $up_invoice->fetch(PDO::FETCH_ASSOC);
		if($inv['id_invoice_apotek']!=''){
				$result = $db->query("UPDATE invoiceapotek SET status='".$status."',jambayar='".$jambayar."',tanggalbayar='".$tanggalbayar."',petugas='".$id_petugas."' WHERE id_invoice_apotek='".$inv['id_invoice_apotek']."'");
		}
		$resultupdate = $db->query("UPDATE resep SET statusbayar='".$status."' WHERE id_resep='".$id_resep."'");
		header('location: transaksi_rajal.php?status=3');
	}
}else{
	$up_invoice = $db->query("SELECT * FROM invoiceapotek WHERE id_resep='".$id_resep."'");
	$inv = $up_invoice->fetch(PDO::FETCH_ASSOC);
	if($inv['id_invoice_apotek']!=''){
			$result = $db->query("UPDATE invoiceapotek SET status='".$status."',jambayar='".$jambayar."',tanggalbayar='".$tanggalbayar."',petugas='".$id_petugas."' WHERE id_invoice_apotek='".$inv['id_invoice_apotek']."'");
	}
	$resultupdate = $db->query("UPDATE resep SET statusbayar='".$status."' WHERE id_resep='".$id_resep."'");
	header('location: transaksi_rajal.php?status=3');
}
?>
