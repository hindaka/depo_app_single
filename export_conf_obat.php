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
$today = date('YmdHis');
$tipe_depo = $tipes[2];
$conf = json_decode(file_get_contents("config/env_depo.json"), true);
$id_depo = $conf[$tipe_depo]["id_depo"];
$data_obat = $db->query("SELECT a.* FROM (SELECT ks.id_obat,g.nama,g.sumber FROM kartu_stok_ruangan ks INNER JOIN gobat g ON(ks.id_obat=g.id_obat) WHERE ks.id_warehouse='".$id_depo."' GROUP BY id_obat) a WHERE a.id_obat NOT IN (SELECT id_obat FROM conf_detail_penyimpanan cf INNER JOIN conf_penyimpanan_obat cp ON(cf.id_conf_obat=cp.id_conf_obat) WHERE cp.id_warehouse='".$id_depo."')");
$data_all = $data_obat->fetchAll(PDO::FETCH_ASSOC);
//EXCEL
header("Content-type: application/vnd.ms-excel");
header("Content-Disposition: attachment; filename=export_depo".$id_depo."_lemari_" . $today . ".xls");
?>
Export Data Lemari Obat, Waktu Generate Data : <?php echo date('Y-m-d H:i:s'); ?>
<table border="1" width="100%">
    <thead>
        <tr class="bg-purple">
            <th>ID obat</th>
            <th>Nama Obat</th>
            <th>Sumber</th>
        </tr>
    </thead>
    <tbody>
        <?php
        foreach ($data_all as $da) {
            echo '<tr>
                    <td>' . $da['id_obat'] . '</td>
                    <td>' . $da['nama'] . '</td>
                    <td>' . $da['sumber'] . '</td>
                </tr>';
        }
        ?>
    </tbody>
</table>