<?php
//conn
session_start();
include("../inc/pdo.conf.php");
include("../inc/version.php");
date_default_timezone_set("Asia/Jakarta");
$namauser = $_SESSION['namauser'];
$password = $_SESSION['password'];
$tipe = $_SESSION['tipe'];
$tipes = explode('-', $tipe);
if ($tipes[0] != 'DepoApp') {
    unset($_SESSION['tipe']);
    unset($_SESSION['namauser']);
    unset($_SESSION['password']);
    header("location:../index.php?status=2");
    exit;
}
include "../inc/anggota_check.php";
//ambil data filter
$tipe_depo = $tipes[2];
$conf = json_decode(file_get_contents("config/env_depo.json"), true);
$id_depo = $conf[$tipe_depo]["id_depo"];
$nama_depo = $conf[$tipe_depo]["nama_depo"];
$sumber = isset($_GET['sumber']) ? $_GET['sumber'] : '';
$today = date('Y-m-d H:i:s');
//mysql data obat
$h2 = $db->query("SELECT ks.id_obat,g.nama,g.satuan,g.jenis,SUM(ks.volume_kartu_akhir) as stok,g.sumber,ks.harga_beli,ks.no_batch,ks.expired FROM kartu_stok_ruangan ks INNER JOIN gobat g ON(ks.id_obat=g.id_obat) WHERE ks.in_out='masuk' AND ks.id_warehouse='" . $id_depo . "' AND g.sumber='".$sumber."' AND ks.volume_kartu_akhir>0 GROUP BY ks.id_obat,ks.no_batch,ks.harga_beli ORDER BY g.nama ASC");
$data_all = $h2->fetchAll(PDO::FETCH_ASSOC);
//EXCEL
header("Content-type: application/vnd.ms-excel");
header("Content-Disposition: attachment; filename=stok_depo_" . $nama_depo . "_" . $sumber . ".xls");
?>
Data Stok <?php echo $nama_depo; ?> per tanggal <?php echo $today; ?>
<table border="1">
    <thead>
        <tr class="bg-blue">
            <th>ID Obat</th>
            <th>Nama</th>
            <th>Satuan</th>
            <th>Jenis</th>
            <th>Stok</th>
            <th>Sumber</th>
            <th>Harga</th>
            <th>No Batch</th>
            <th>Expired</th>
        </tr>
    </thead>
    <tbody>
        <?php
        foreach ($data_all as $row) {
            echo "<tr>
                    <td>" . $row['id_obat'] . "</td>
                    <td>" . $row['nama'] . "</td>
                    <td>" . $row['satuan'] . "</td>
                    <td>" . $row['jenis'] . "</td>
                    <td>" . $row['stok'] . "</td>
                    <td>" . $row['sumber'] . "</td>
                    <td>" . $row['harga_beli'] . "</td>
                    <td>" . $row['no_batch'] . "</td>
                    <td>" . $row['expired'] . "</td>
                </tr>";
        }
        ?>
    </tbody>
</table>