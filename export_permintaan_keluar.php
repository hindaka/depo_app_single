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
$tipe_depo = $tipes[2];
$conf = json_decode(file_get_contents("config/env_depo.json"),true);
$id_depo = $conf[$tipe_depo]["id_depo"];
$nama_depo = $conf[$tipe_depo]["nama_depo"];
//mysql data obat
$h2=$db->query("SELECT d.*,w.nama_ruang,peg.nama as nama_pemesan,a.nama as nama_petugas,b.asal_depo,b.tanggal_permintaan FROM `barangkeluar_depo` b LEFT JOIN warehouse w ON(b.id_warehouse=w.id_warehouse) LEFT JOIN pegawai peg ON(b.permintaan=peg.id_pegawai) LEFT JOIN anggota a ON(b.mem_id=a.mem_id) INNER JOIN barangkeluar_depo_det d ON(b.id_barangkeluar_depo=d.id_barangkeluar_depo) WHERE MONTH(b.tanggal_permintaan)='".$bulan."' AND YEAR(b.tanggal_permintaan)='".$tahun."' AND (b.id_warehouse='".$id_depo."' OR asal_warehouse='".$id_depo."')");
$data_all = $h2->fetchAll(PDO::FETCH_ASSOC);
//EXCEL
header("Content-type: application/vnd.ms-excel");
header("Content-Disposition: attachment; filename=permintaan_antar_depo(".$bulan."_".$tahun.").xls");
?>
Transaksi Antar Depo (<?php echo $nama_depo; ?>) <?php echo "Bulan : ".$bulan."/ Tahun : ".$tahun; ?>
<table border="1">
	<thead>
		<tr class="bg-blue">
			<th>Tanggal Permintaan</th>
			<th>Pemesan</th>
			<th>Asal Obat</th>
			<th>Tujuan</th>
			<th>Nama Obat</th>
			<th>Volume</th>
			<th>Petugas Input</th>
		</tr>
	</thead>
	<tbody>
	<?php
	foreach ($data_all as $row) {
		echo "<tr>
						<td>".$row['tanggal_permintaan']."</td>
						<td>".$row['nama_pemesan']."</td>
						<td>".$row['asal_depo']."</td>
						<td>".$row['nama_ruang']."</td>
						<td>".$row['namabarang']."</td>
						<td>".$row['volume']."</td>
						<td>".$row['nama_petugas']."</td>
					</tr>";
	}
	 ?>
	</tbody>
</table>
