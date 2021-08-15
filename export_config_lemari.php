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
$id_conf_obat = isset($_GET['conf']) ? $_GET['conf'] : '';

$get_conf = $db->query("SELECT nama_penyimpanan FROM conf_penyimpanan_obat WHERE id_conf_obat='".$id_conf_obat."'");
$header = $get_conf->fetch(PDO::FETCH_ASSOC);

$content_data = $db->query("SELECT cp.*,g.nama,g.sumber FROM conf_detail_penyimpanan cp INNER JOIN conf_penyimpanan_obat cf ON(cp.id_conf_obat=cf.id_conf_obat) INNER JOIN gobat g ON(cp.id_obat=g.id_obat) WHERE cf.id_conf_obat='".$id_conf_obat."' ORDER BY cp.id_conf_detail ASC");
$content_all = $content_data->fetchAll(PDO::FETCH_ASSOC);

//EXCEL
header("Content-type: application/vnd.ms-excel");
header("Content-Disposition: attachment; filename=export_conf_lemari_".$id_conf_obat.".xls");
?>
Data Pengaturan Penyimpanan Obat di <?php echo $header['nama_penyimpanan']; ?> <br>
Tanggal Export <?php echo date('d-m-Y H:i:s');?>
<table border="1" cellspacing="5" cellpadding="5">
	<thead>
		<tr>
			<th>Id Obat</th>
			<th>Nama Obat</th>
			<th>Sumber</th>
		</tr>
	</thead>
	<tbody>
		<?php
			foreach ($content_all as $c) {
				echo '<tr>
								<td>'.$c['id_obat'].'</td>
								<td>'.$c['nama'].'</td>
								<td>'.$c['sumber'].'</td>
							</tr>';
			}
		?>
	</tbody>
</table>
