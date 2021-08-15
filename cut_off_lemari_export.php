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
$awal = isset($_GET['awal']) ? $_GET['awal'] : '';
$akhir = isset($_GET['akhir']) ? $_GET['akhir'] : '';
$id_conf_obat = isset($_GET['penyimpanan']) ? $_GET['penyimpanan'] : '';
$tipe_depo = $tipes[2];
$conf = json_decode(file_get_contents("config/env_depo.json"),true);
$id_depo = $conf[$tipe_depo]["id_depo"];
$get_data = $db->query("SELECT * FROM conf_penyimpanan_obat WHERE id_warehouse='".$id_depo."'");
$data_all = $get_data->fetchAll(PDO::FETCH_ASSOC);

$get_data = $db->query("SELECT g.no_urut_depo_igd,ks.id_obat,g.nama,g.jenis,gs.nama_bentuk,SUM(ks.volume_out) as jumlah,ks.expired,ks.no_batch,ks.sumber_dana FROM kartu_stok_ruangan ks INNER JOIN gobat g ON(ks.id_obat=g.id_obat) LEFT JOIN gobat_bentuk_sediaan gs ON(g.id_bentuk=gs.id_bentuk_sediaan) WHERE ks.id_warehouse='".$id_depo."' AND g.lemari_depo_igd='".$id_conf_obat."' AND ks.in_out='keluar' AND ks.created_at BETWEEN '".$awal."' AND '".$akhir."' GROUP BY ks.id_obat,ks.no_batch ORDER BY g.no_urut_depo_igd,g.id_obat ASC");
$data = $get_data->fetchAll(PDO::FETCH_ASSOC);
$db=NULL;

//EXCEL
header("Content-type: application/vnd.ms-excel");
header("Content-Disposition: attachment; filename=cut_off_lemari_".$id_conf_obat.".xls");
?>
CUT OFF LEMARI, Tanggal Export <?php echo date('d-m-Y H:i:s');?>
<table border="1" cellpadding="5">
	<thead>
		<tr class="info">
			<th>No Urut Lemari</th>
			<th>ID obat</th>
			<th>Nama Obat</th>
			<th>Jenis</th>
			<th>Nama Bentuk</th>
			<th>Jumlah</th>
			<th>Expired</th>
			<th>No Batch</th>
			<th>Sumber Dana</th>
		</tr>
	</thead>
	<tbody>
		<?php
			foreach ($data as $row) {
				echo  "<tr>
								<td>".$row['no_urut_depo_igd']."</td>
								<td>".$row['id_obat']."</td>
								<td>".$row['nama']."</td>
								<td>".$row['jenis']."</td>
								<td>".$row['nama_bentuk']."</td>
								<td>".$row['jumlah']."</td>
								<td>".$row['expired']."</td>
								<td>".$row['no_batch']."</td>
								<td>".$row['sumber_dana']."</td>
							</tr>";
			}
		?>
	</tbody>
</table>
