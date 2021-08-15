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
$id_etiket = isset($_GET['e']) ? $_GET['e'] : '';
$id_rincian = isset($_GET['i']) ? $_GET['i'] : '';
$id_detail_rincian = isset($_GET['d']) ? $_GET['d'] : '';

//data etiket_ranap
$etiket_q = $db->prepare("SELECT * FROM etiket_apotek WHERE id_etiket=:id");
$etiket_q->bindParam(":id",$id_etiket,PDO::PARAM_INT);
$etiket_q->execute();
$etiket = $etiket_q->fetch(PDO::FETCH_ASSOC);

$sehari=isset($etiket['sehari_x']) ? $etiket['sehari_x'] : '';
$edate=isset($etiket['expired_date']) ? $etiket['expired_date'] : '';
$edate = substr($edate,0,10);
$format = explode("-",$edate);
$new_date = $format[2]."-".$format[1]."-".$format[0];
$takaran=isset($etiket['takaran']) ? $etiket['takaran'] : '';
$minum=isset($etiket['diminum']) ? $etiket['diminum'] : '';
$petunjuk=isset($etiket['petunjuk_khusus']) ? $etiket['petunjuk_khusus'] : '';

$hariini=date("d/m/Y H:i:s");
// //ambil value pasien lama
$get_cetak ="SELECT rp.nomedrek,rp.nama as nama_pasien,rp.tanggallahir,rp.kelamin,g.nama as nama_obat,rd.volume FROM rincian_obat_pasien ro INNER JOIN rincian_transaksi_obat rto ON(rto.id_rincian_obat=ro.id_rincian_obat) INNER JOIN rincian_detail_obat rd ON(rto.id_trans_obat=rd.id_trans_obat) INNER JOIN registerpasien rp ON(ro.id_pasien=rp.id_pasien) INNER JOIN gobat g ON(rd.id_obat=g.id_obat) WHERE rd.id_detail_rincian=".$id_detail_rincian;
$cetak = $db->query($get_cetak);
$items = $cetak->fetch(PDO::FETCH_ASSOC);
$hariini2 = date('d/m/Y');

//action
echo "<html>
<head>
<link rel=\"stylesheet\" type=\"text/css\" href=\"cetak.css\" />
<body onload=\"loadPrint()\"><center>INSTALASI FARMASI<br />RSKIA KOTA BANDUNG<hr></center>
No. RM: ".$items['nomedrek']." &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp; TGL: ".$hariini."<br><center>".$items['nama_pasien']." (".$items['tanggallahir'].")</center><br>
<center><font size='2'>Sehari ".$sehari." ".$takaran." ".$minum."</font><br><br></center>
Nama Obat: ".$items['nama_obat']."<br>Jumlah: ".$items['volume']."<br>Petunjuk Khusus: ".$petunjuk."<br>ED: ".$edate."
<script type='text/javascript'>
	function loadPrint(){
		window.print();
		setTimeout(function(){
			window.close();
		},100);
	}
</script>
</body>
</html>";
?>
