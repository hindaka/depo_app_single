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
$id_etiket = $_GET['e'];
$id_obatkeluar=$_GET["i"];
$id_resep=$_GET["resep"];

$hariini=date("d/m/Y");
//data etiket_ranap
$etiket_q = $db->prepare("SELECT * FROM etiket_apotek_rajal WHERE id_etiket_rajal=:id");
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
$label_khusus = isset($etiket['label_khusus']) ? $etiket['label_khusus'] : '';
//ambil value pasien lama
$h2=$db->query("SELECT * FROM resep WHERE id_resep='$id_resep'");
$r2=$h2->fetch(PDO::FETCH_ASSOC);
$nomedrek=$r2["nomedrek"];
$nama=$r2["nama"];
$h3=$db->query("SELECT * FROM pasien WHERE nomedrek='$nomedrek'");
$r3=$h3->fetch(PDO::FETCH_ASSOC);
$tanggallahir=$r3["tanggallahir"];
$kelamin=$r3["kelamin"];
$h4=$db->query("SELECT * FROM apotekkeluar WHERE id_obatkeluar='$id_obatkeluar'");
$r4=$h4->fetch(PDO::FETCH_ASSOC);
$namaobat=$r4["namaobat"];
$volume=$r4["volume"];
//action
echo "<html>
<head>
<link rel=\"stylesheet\" type=\"text/css\" href=\"cetak.css\" /></head>
<body onload=\"loadPrint()\" ><center>INSTALASI FARMASI<br />RSKIA KOTA BANDUNG<hr></center>
No. RM: $nomedrek &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp; TGL: $hariini<br><center>$nama ($tanggallahir)</center><br>
<center><font size='2'>Sehari $sehari $takaran $minum</font><br><br></center>
Nama Obat: $namaobat<br>Jumlah: $volume<br>Petunjuk Khusus: $petunjuk<br>ED: $edate
<br />
<table border='1' width='100%'>
	<tr><td align='center'>$label_khusus</td></tr>
</table>

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
