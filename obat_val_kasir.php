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
echo "Sedang dalam proses...";
$id_rincian = isset($_GET['rincian']) ? $_GET['rincian'] : '';
$total = isset($_GET['total']) ? $_GET['total'] : '';

//update status
$sql_update = $db->query("UPDATE rincian_obat_pasien SET status='kasir',approval='y' WHERE id_rincian_obat=".$id_rincian);
if($sql_update){
  echo "<script language=\"JavaScript\">window.location = \"obat_ranap.php?&status=6\"</script>";
  // echo "<br>update rincian obat berhasil...";
  // //copy data rincian_obat_pasien to invoice_obat
  // $sql_insert ="INSERT INTO invoice_obat(id_pasien,status,total_bayar,mem_id) SELECT id_pasien,'Belum dibayar','".$total."','".$mem_id."' FROM rincian_obat_pasien WHERE id_rincian_obat=".$id_rincian;
  // $execute_insert = $db->query($sql_insert);
  // if($execute_insert){
  //   echo "<br>Pembuatan invoice..";
  //   //copy data into invoice_obat_d
  //   $last_id = mysql_insert_id();
  //   $sql_detail = $db->query("INSERT INTO invoice_obat_d(dpjp,id_obat_apotek,volume,harga_satuan,sub_total,id_invoice_obat) SELECT dpjp,id_obat,volume,harga_satuan,sub_total,'".$last_id."' FROM rincian_detail_obat WHERE id_rincian=".$id_rincian);
  //   if($sql_detail){
  //     echo 'Sukses';
  //     echo "<script language=\"JavaScript\">window.location = \"obat_ranap.php?&status=6\"</script>";
  //   }else{
  //     echo "Gagal<br>Error : ".mysql_error();
  //   }
  // }else{
  //   echo "<br>Pembuatan Invoice gagal : ".mysql_error();
  // }
}else{
  echo "Gagal, Error : ".mysql_error();
}
