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
$id_resep=isset($_GET["id"]) ? $_GET['id'] : '';
$totalbayar=isset($_GET["total"]) ? $_GET['total'] : '';
$tanggalresep=date("d/m/Y");
//get data resep
$h2=$db->query("SELECT * FROM resep WHERE id_resep='".$id_resep."'");
$data2=$h2->fetch(PDO::FETCH_ASSOC);
$tgl_resep=$data2["tgl_resep"];
$nomedrek=$data2["nomedrek"];
$dokter=$data2["dokter"];
$ruang=$data2["ruang"];
$nama=$data2["nama"];
$bayar=$data2["bayar"];
//pembulatan
$totalharga=ceil($totalbayar);
$ratusan = substr($totalharga, -3);
if($ratusan<450){
	$total_harga = $totalharga - $ratusan;
}else if(($ratusan>=450)&&($ratusan<950)){
	$total_harga = ($totalharga - $ratusan)+500;
}else{
	$total_harga = $totalharga + (1000-$ratusan);
}
$selisih = $total_harga - $totalharga;
// potong stok
$get_data = $db->query("SELECT * FROM warehouse_out WHERE id_resep='".$id_resep."'");
$data = $get_data->fetchAll(PDO::FETCH_ASSOC);
foreach ($data as $row) {
	$up = $db->query("UPDATE warehouse_stok SET stok=stok-".$row['volume']." WHERE id_warehouse_stok='".$row['id_warehouse_stok']."'");
}
//insert
$result = $db->query("INSERT INTO invoiceapotek(nomedrek,status,jpasien,asal,dokter,totalbayar,pembayaran_pasien,selisih_pembulatan,tanggalresep,id_resep) VALUES ('".$nomedrek."','Belum dibayar','".$bayar."','".$ruang."','".$dokter."','".$totalharga."','".$total_harga."',".$selisih.",'".$tanggalresep."','".$id_resep."')");

//action
if ($result) {
	require '../plugins/pusher/vendor/autoload.php';

  $options = array(
    'cluster' => 'ap1',
    'useTLS' => true
  );
  $pusher = new Pusher\Pusher(
    'acd9a19d6de134d60feb',
    '41379e074ec97d25f3ba',
    '963052',
    $options
  );

  $data['message'] = 'Hi, Ada Transaksi Apotek Baru belum diproses!!';
	$data = json_encode($data);
  $pusher->trigger('transaksi', 'trans_apotek', $data);
	echo "<script language=\"JavaScript\">window.location = \"resep.php?status=1\"</script>";
} else {
	echo "gagal";
}
?>
