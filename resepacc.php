<?php
//conn
session_start();
include("../inc/pdo.conf.php");
include("../inc/version.php");
ini_set('display_errors',1);
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
$tanggalr=isset($_POST["tanggalr"]) ? $_POST['tanggalr'] : '';
$dokter=isset($_POST["dokter"]) ? $_POST['dokter'] : '';
$ruang=isset($_POST["ruang"]) ? $_POST['ruang'] : '';
$nomedrek=isset($_POST["nomedrek"]) ? $_POST['nomedrek'] : '';
$namaValue=isset($_POST["namaValue"]) ? $_POST['namaValue'] : '';
$bayar=isset($_POST["bayar"]) ? $_POST['bayar'] : '';
$jenis_transaksi = isset($_POST['jenis_transaksi']) ? $_POST['jenis_transaksi'] : '';
$id_petugas = $r1['mem_id'];
$status = "Belum dibayar";
$today= date('d/m/Y');
$data_arr = array(
	"tanggalr"=>$tanggalr,
	"dokter"=>$dokter,
	"ruang"=>$ruang,
	"namaValue"=>$namaValue,
	"bayar"=>$bayar,
	"jenis_transaksi"=>$jenis_transaksi
);
$_SESSION['data_resep'] = $data_arr;
try {
	if ($tanggalr==''||$nomedrek==''||$bayar=='')
	{
		header("location:resep.php?status=3");
		exit();
	}
	$db->beginTransaction();
	//logika nama
	if ($nomedrek=='-') {
	$nama=$namaValue;
	$id_register=0;
	} else {
	//cek resep
	$stmt = $db->query("SELECT COUNT(*) as total_data FROM registerpasien rp WHERE rp.nomedrek='".$nomedrek."' AND rp.tanggaldaftar='".$today."'");
	$result = $stmt->fetch(PDO::FETCH_ASSOC);
	if($result['total_data']==0){
		header("location:resep.php?status=4");
		exit("No.Rekam Medis tersebut tidak terdaftar pada hari ini");
	}
	//ambil data nama
	$h3=$db->query("SELECT * FROM registerpasien WHERE nomedrek='$nomedrek' ORDER BY id_pasien DESC LIMIT 1");
	$data3=$h3->fetch(PDO::FETCH_ASSOC);
	$nama=$data3["nama"];
	$id_register = $data3['id_pasien'];
	}
	//insert
	$result = $db->query("INSERT INTO resep(tgl_resep,statusbayar,dokter,ruang,nomedrek,id_register,bayar,nama,jenis_transaksi,mem_id) VALUES ('$tanggalr','$status','$dokter','$ruang','$nomedrek','$id_register','$bayar','$nama','$jenis_transaksi','$id_petugas')");
	$id_resep=$db->lastInsertId();

	//action
	if ($result) {
		if($jenis_transaksi=="Bon"){
			echo "<script language=\"JavaScript\">window.location = \"keluar.php?id=$id_resep&trans=$jenis_transaksi\"</script>";
		}else if($jenis_transaksi=="Resep"){
			echo "<script language=\"JavaScript\">window.location = \"resep_check.php?id=$id_resep&trans=$jenis_transaksi\"</script>";
		}else{
			exit("Jenis Transaksi Belum dipilih");
		}
	} else {
		exit("Transaksi Resep Gagal, Silakan Ulangi Kembali");
	}
	$db->commit();
} catch (PDOException $e) {
	$db->rollBack();
	header("location:resep.php?status=3");
}
?>
