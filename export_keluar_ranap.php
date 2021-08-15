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
$bulan=isset($_GET["bulan"]) ? $_GET['bulan'] : date('m');
$tahun=isset($_GET["tahun"]) ? $_GET['tahun'] : date('Y');
$tipe_depo = $tipes[2];
$conf = json_decode(file_get_contents("config/env_depo.json"),true);
$id_depo = $conf[$tipe_depo]["id_depo"];
//mysql data obat
$result = $db->query("SELECT ag.nama as petugas_input,r.id_detail_rincian,SUBSTRING(r.created_at,1,10) as tgl,g.id_obat,g.nama,g.jenis,g.sumber,r.volume,ri.dpjp,r.ruang,ri.id_pasien,IF(rp.jpasien='Umum','Umum','BPJS') as cara_bayar,rp.nama as namapasien
FROM `rincian_detail_obat` r INNER JOIN gobat g ON(g.id_obat=r.id_obat)
INNER JOIN rincian_transaksi_obat ro ON(r.id_trans_obat=ro.id_trans_obat)
INNER JOIN rincian_obat_pasien ri ON(ri.id_rincian_obat=ro.id_rincian_obat)
INNER JOIN registerpasien rp ON(rp.id_pasien=ri.id_pasien)
INNER JOIN anggota ag ON(ag.mem_id=r.mem_id)
WHERE ro.id_warehouse='".$id_depo."' AND MONTH(r.created_at)='".$bulan."' AND YEAR(r.created_at)='".$tahun."'");
$data = $result->fetchAll(PDO::FETCH_ASSOC);
//EXCEL
header("Content-type: application/vnd.ms-excel");
header("Content-Disposition: attachment; filename=obat_keluar_ranap(".$bulan."_".$tahun.").xls");
?>
<table border=1>
	<thead>
		<tr class="info">
			<th>Tanggal Keluar</th>
			<th>Nama Obat</th>
			<th>Jenis</th>
			<th>Sumber</th>
			<th>Volume</th>
			<th>Dokter</th>
			<th>Ruang</th>
			<th>Cara Bayar</th>
			<th>Pasien</th>
			<th>Petugas Input</th>
		</tr>
	</thead>
	<tbody>
		<?php
		foreach ($data as $row) {
			echo "<tr>
					<td>".$row['tgl']."</td>
					<td>".$row['nama']."</td>
					<td>".$row['jenis']."</td>
					<td>".$row['sumber']."</td>
					<td>".$row['volume']."</td>
					<td>".$row['dpjp']."</td>
					<td>".$row['ruang']."</td>
					<td>".$row['cara_bayar']."</td>
					<td>".$row['namapasien']."</td>
					<td>".$row['petugas_input']."</td>
			</tr>";
		}
		?>
	</tbody>
</table>
