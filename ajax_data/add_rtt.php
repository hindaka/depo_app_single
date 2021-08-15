<?php
session_start();
include("../../inc/pdo.conf.php");
include("../../inc/version.php");
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
	header("location:../../index.php?status=2");
	exit;
}
include "../../inc/anggota_check.php";
$id_resep = isset($_POST['id']) ? $_POST['id'] : '';
$id_rtt = isset($_POST['id_rtt']) ? $_POST['id_rtt'] : '';
$fr = isset($_POST['fr']) ? $_POST['fr'] : '';
$task = isset($_POST['task']) ? $_POST['task'] : '';
$id_obat = isset($_POST['obatrtt']) ? $_POST['obatrtt'] : '';
$jenis_obat = isset($_POST['jenis_obat']) ? $_POST['jenis_obat'] : '';
$satuan = isset($_POST['satuan']) ? $_POST['satuan'] : '';
$jumlah_item = isset($_POST['jumlah']) ? $_POST['jumlah'] : 0;
$harga = isset($_POST['harga']) ? $_POST['harga'] : 0;
$keterangan = isset($_POST['ket']) ? $_POST['ket'] : '';
$id_petugas = $r1['mem_id'];
try {
	if($task=='add'){
		if($fr=='ya'){
			$get_nama = $db->query("SELECT nama FROM gobat WHERE id_obat='".$id_obat."'");
			$obat = $get_nama->fetch(PDO::FETCH_ASSOC);
			$nama_obat=$obat['nama'];
		}else{
			$nama_obat = $id_obat;
		}
		$stmt = $db->prepare("INSERT INTO `resep_rtt`(`id_resep`, `nama_obat`,`jenis_obat`,`satuan`,`jumlah_item`,`harga`, `ket`, `mem_id`) VALUES (:id_resep,:nama_obat,:jenis_obat,:satuan,:jumlah_item,:harga,:ket,:mem_id)");
		$stmt->bindParam(':id_resep',$id_resep,PDO::PARAM_INT);
		$stmt->bindParam(':nama_obat',$nama_obat,PDO::PARAM_STR);
		$stmt->bindParam(':jenis_obat',$jenis_obat,PDO::PARAM_STR);
		$stmt->bindParam(':satuan',$satuan,PDO::PARAM_STR);
		$stmt->bindParam(':jumlah_item',$jumlah_item,PDO::PARAM_INT);
		$stmt->bindParam(':harga',$harga,PDO::PARAM_INT);
		$stmt->bindParam(':ket',$keterangan,PDO::PARAM_STR);
		$stmt->bindParam(':mem_id',$id_petugas,PDO::PARAM_INT);
		$stmt->execute();
		echo 'Berhasil ditambahkan';
	}else if($task=='delete'){
		$stmt = $db->prepare("DELETE FROM resep_rtt WHERE id_rtt=:id");
		$stmt->bindParam(':id',$id_rtt,PDO::PARAM_INT);
		$stmt->execute();
		echo 'Berhasil dihapus';
	}else if($task=='change'){
		$id_warehouse='7';
		$stmt = $db->query("SELECT ks.*,g.satuan,g.jenis FROM `kartu_stok_ruangan` ks INNER JOIN gobat g ON(ks.id_obat=g.id_obat) WHERE ks.id_warehouse='".$id_warehouse."' AND ks.volume_kartu_akhir>0 AND ks.in_out='masuk' AND ks.id_obat='".$id_obat."' ORDER BY ks.created_at ASC LIMIT 1");
		$stmt->execute();
		$data = $stmt->fetch(PDO::FETCH_ASSOC);
		$feedback = array(
			"id_obat"=>$data['id_obat'],
			"satuan"=>$data['satuan'],
			"jenis"=>$data['jenis'],
			"harga_beli"=>$data['harga_beli'],
			"harga_jual"=>$data['harga_jual'],
		);
		echo json_encode($feedback);
	}else{

	}

} catch (PDOException $e) {
	echo $e->getMessage();
}
