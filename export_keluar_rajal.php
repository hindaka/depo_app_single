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
//ambil data filter
$bulan=$_GET["bulan"] ? $_GET['bulan'] : date('m');
$tahun=$_GET["tahun"] ? $_GET['tahun'] : date('y');
$gabung = "%".$bulan."/".$tahun;
//mysql data obat
$h2=$db->query("SELECT a.tanggal,a.namaobat,a.sumber,a.volume,r.dokter,r.ruang,IF(r.bayar='Tunai','Umum','BPJS') as cara_bayar,r.nama FROM `apotekkeluar` a INNER JOIN resep r ON(r.id_resep=a.id_resep) WHERE a.tanggal LIKE '%$gabung%'");
//EXCEL
header("Content-type: application/vnd.ms-excel");
header("Content-Disposition: attachment; filename=obat_keluar_rajal(".$bulan."_".$tahun.").xls");
?>
Transaksi Rajal IGD <?php echo "Bulan : ".$bulan."/ Tahun : ".$tahun; ?>
<table border=1>
	<thead>
		<tr>
			<th>Tanggal Keluar</th>
			<th>Nama Obat</th>
			<th>Sumber</th>
			<th>Volume</th>
			<th>Dokter</th>
			<th>Ruang</th>
			<th>Cara Bayar</th>
			<th>Pasien</th>
		</tr>
	</thead>
	<tbody>
	<?php
	foreach ($h2 as $row) {
		echo "<tr>
						<td>".$row['tanggal']."</td>
						<td>".$row['namaobat']."</td>
						<td>".$row['sumber']."</td>
						<td>".$row['volume']."</td>
						<td>".$row['dokter']."</td>
						<td>".$row['ruang']."</td>
						<td>".$row['cara_bayar']."</td>
						<td>".$row['nama']."</td>
					</tr>";
	}
	 ?>
	</tbody>
</table>
