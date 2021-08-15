<?php
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
$id_paket_ob = isset($_GET['jenis_paket']) ? $_GET['jenis_paket'] : '';
$id_master_pasien = isset($_GET['nomedrek']) ? $_GET['nomedrek'] : '';
$dokter_bedah = isset($_GET['dokter_bedah']) ? $_GET['dokter_bedah'] : '.....';
$dokter_anestesi = isset($_GET['dokter_anestesi']) ? $_GET['dokter_anestesi'] : '.....';
$perawat_anestesi = isset($_GET['perawat_anestesi']) ? $_GET['perawat_anestesi'] : '.....';
$dokter_anak = isset($_GET['dokter_anak']) ? $_GET['dokter_anak'] : '.....';
$diagnosa = isset($_GET['diagnosa']) ? $_GET['diagnosa'] : '.....';
$jenis_anestesi = isset($_GET['jenis_anestesi']) ? $_GET['jenis_anestesi'] : '.....';
$asisten = isset($_GET['asisten']) ? $_GET['asisten'] : '.....';
//get jenis paket 
$get_paket = $db->query("SELECT * FROM paket_obat_bhp WHERE id_paket_ob='" . $id_paket_ob . "'");
$head_paket = $get_paket->fetch(PDO::FETCH_ASSOC);
$get_pasien = $db->query("SELECT * FROM pasien WHERE id_master_pasien='" . $id_master_pasien . "'");
$pas = $get_pasien->fetch(PDO::FETCH_ASSOC);
//get paket 
$get_umum = $db->query("SELECT * FROM paket_obat_bhp_detail WHERE id_paket_ob='" . $id_paket_ob . "' AND jenis_barang='umum'");
$all_umum = $get_umum->fetchAll(PDO::FETCH_ASSOC);
$get_bedah = $db->query("SELECT * FROM paket_obat_bhp_detail WHERE id_paket_ob='" . $id_paket_ob . "' AND jenis_barang='bedah'");
$all_bedah = $get_bedah->fetchAll(PDO::FETCH_ASSOC);
$get_anestesi = $db->query("SELECT * FROM paket_obat_bhp_detail WHERE id_paket_ob='" . $id_paket_ob . "' AND jenis_barang='anestesi'");
$all_anestesi = $get_anestesi->fetchAll(PDO::FETCH_ASSOC);
$total_row = 20;
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>CETAK PAKET OBAT BHP</title>
    <style>
        body {
            font-size: arial;
        }

        #head_kop {
            font-weight: bold;
        }

        table {
            border-collapse: collapse;
        }

        table.anak_table tr td {
            font-size: 12px;
        }

        .garis-kiri {
            border-left: 1px solid black;
        }

        .garis-kanan {
            border-right: 1px solid black;
        }

        .garis-atas {
            border-top: 1px solid black;
        }

        .garis-bawah {
            border-bottom: 1px solid black;
        }

        .huruf_normal {
            font-size: 12px;
        }
    </style>
</head>

<body onload="loadPrint()">
    <table border="0" width="100%" cellspacing="0" cellpadding="5">
        <tr>
            <td class="garis-kiri garis-atas garis-bawah garis-kanan" rowspan="4" style="width:50%;text-align:center;font-weigth:bold;font-size:18px;">
                RUMAH SAKIT KHUSUS IBU DAN ANAK <br>
                KOTA BANDUNG<br>
                Jl. Kopo No.311 <br>
                Phone (022)86037777
            </td>
            <td class="garis-atas" width="15%">Nama</td>
            <td class="garis-atas garis-kanan">: <?php echo $pas['nama']; ?></td>
        </tr>
        <tr>
            <td>Tanggal Lahir</td>
            <td class="garis-kanan">: <?php echo $pas['tanggallahir']; ?></td>
        </tr>
        <tr>
            <td>No.RM</td>
            <td class="garis-kanan">: <?php echo $pas['nomedrek']; ?></td>
        </tr>
        <tr>
            <td class="garis-bawah">Tanggal</td>
            <td class="garis-kanan garis-bawah">: <?php echo date('d F Y'); ?></td>
        </tr>
    </table>
    <table border="0" width="100%" cellspacing="0" cellpadding="5">
        <tr>
            <td class="garis-atas garis-kiri garis-kanan garis-bawah" colspan="5" style="text-align:center;font-weigth:bold;font-size:18px;">FORMULIR PERMINTAAN OBAT DAN BMHP<br> <?php echo $head_paket['nama_paket']; ?></td>
        </tr>
        <tr>
            <td class="garis-kiri huruf_normal" style="width:15%;">Dokter Bedah</td>
            <td class="huruf_normal" style="width:40%;">: <?php echo $dokter_bedah; ?></td>
            <td></td>
            <td class="huruf_normal" style="width:10%;">Diagnosa</td>
            <td class="garis-kanan huruf_normal">: <?php echo $diagnosa; ?></td>
        </tr>
        <tr>
            <td class="garis-kiri huruf_normal" style="width:15%;font-size:12px;">Dokter Anestesi</td>
            <td class="huruf_normal">: <?php echo $dokter_anestesi; ?></td>
            <td></td>
            <td class="huruf_normal" style="width:10%">Jenis Operasi</td>
            <td class="garis-kanan huruf_normal">: <?php echo $head_paket['nama_paket']; ?></td>
        </tr>
        <tr>
            <td class="garis-kiri huruf_normal" style="width:15%">Penata Anestesi</td>
            <td class="huruf_normal">: <?php echo $perawat_anestesi; ?></td>
            <td></td>
            <td class="huruf_normal" style="width:10%">Jenis Anestesi</td>
            <td class="garis-kanan huruf_normal">: <?php echo $jenis_anestesi; ?></td>
        </tr>
        <tr>
            <td class="garis-kiri garis-bawah huruf_normal" style="width:15%">Dokter Anak</td>
            <td class="garis-bawah huruf_normal">: <?php echo $dokter_anak; ?></td>
            <td class="garis-bawah"></td>
            <td class="garis-bawah huruf_normal" style="width:10%">Asisten/Instrumentator</td>
            <td class="garis-kanan garis-bawah huruf_normal">: <?php echo $asisten; ?></td>
        </tr>
    </table>
    <table width="100%" border="0" class="anak_table" cellspacing="0" cellpadding="5">
        <tr>
            <td width="33.33333333%" style="vertical-align:top" class="garis-kanan garis-bawah">
                <table border="1" width="100%">
                    <tr>
                        <td colspan="4" style="text-align:center">BHP DAN OBAT ANESTESI</td>
                    </tr>
                    <tr>
                        <td rowspan="2">No</td>
                        <td rowspan="2">Nama Barang</td>
                        <td colspan="2" style="text-align:center">Jumlah</td>
                    </tr>
                    <tr>
                        <td>Pengambilan</td>
                        <td>Terpakai</td>
                    </tr>
                    <?php
                    $num = 1;
                    for ($i = 0; $i < $total_row; $i++) {
                        $nama_obat = isset($all_anestesi[$i]['namaobat']) ? $all_anestesi[$i]['namaobat'] : '&nbsp;';
                        $jumlah_obat = isset($all_anestesi[$i]['jumlah_obat']) ? $all_anestesi[$i]['jumlah_obat'] : '&nbsp;';
                        echo '<tr>
                                <td>' . $num++ . '</td>
                                <td>' . $nama_obat . '</td>
                                <td>' . $jumlah_obat . '</td>
                                <td>&nbsp;</td>
                            </tr>';
                    }
                    ?>
                </table>
            </td>
            <td width="33.33333333%" style="vertical-align:top" class="garis-bawah">
                <table border="1" width="100%">
                    <tr>
                        <td colspan="4" style="text-align:center">BHP BEDAH</td>
                    </tr>
                    <tr>
                        <td rowspan="2">No</td>
                        <td rowspan="2">Nama Barang</td>
                        <td colspan="2" style="text-align:center">Jumlah</td>
                    </tr>
                    <tr>
                        <td>Pengambilan</td>
                        <td>Terpakai</td>
                    </tr>
                    <?php
                    $num = 1;
                    for ($i = 0; $i < $total_row; $i++) {
                        $nama_obat = isset($all_bedah[$i]['namaobat']) ? $all_bedah[$i]['namaobat'] : '&nbsp;';
                        $jumlah_obat = isset($all_bedah[$i]['jumlah_obat']) ? $all_bedah[$i]['jumlah_obat'] : '&nbsp;';
                        echo '<tr>
                                <td>' . $num++ . '</td>
                                <td>' . $nama_obat . '</td>
                                <td>' . $jumlah_obat . '</td>
                                <td>&nbsp;</td>
                            </tr>';
                    }
                    ?>
                </table>
            </td>
            <td width="33.33333333%" style="vertical-align:top" class="garis-kiri garis-bawah">
                <table border="1" width="100%">
                    <tr>
                        <td colspan="4" style="text-align:center">BHP UMUM</td>
                    </tr>
                    <tr>
                        <td rowspan="2">No</td>
                        <td rowspan="2">Nama Barang</td>
                        <td colspan="2" style="text-align:center">Jumlah</td>
                    </tr>
                    <tr>
                        <td>Pengambilan</td>
                        <td>Terpakai</td>
                    </tr>
                    <?php
                    $num = 1;
                    for ($i = 0; $i < $total_row; $i++) {
                        $nama_obat = isset($all_umum[$i]['namaobat']) ? $all_umum[$i]['namaobat'] : '&nbsp;';
                        $jumlah_obat = isset($all_umum[$i]['jumlah_obat']) ? $all_umum[$i]['jumlah_obat'] : '&nbsp;';
                        echo '<tr>
                                <td>' . $num++ . '</td>
                                <td>' . $nama_obat . '</td>
                                <td>' . $jumlah_obat . '</td>
                                <td>&nbsp;</td>
                            </tr>';
                    }
                    ?>
                </table>
            </td>
        </tr>
    </table>
    <table border="0" width="100%" style="text-align:center" cellspacing="0" cellpadding="5">
        <tr>
            <td class="garis-kiri garis-atas garis-kanan garis-bawah" colspan="4">Serah Terima Obat & BHP</td>
        </tr>
        <tr>
            <td class="garis-kiri garis-kanan garis-bawah" colspan="2">Penyerahan</td>
            <td class="garis-kiri garis-kanan garis-bawah" colspan="2">Pengembalian</td>
        </tr>
        <tr>
            <td class="garis-kiri garis-kanan">Petugas Depo</td>
            <td class="garis-kiri">Petugas</td>
            <td class="garis-kiri garis-kanan">Petugas Depo</td>
            <td class="garis-kanan">Petugas</td>
        </tr>
        <tr>
            <td class="garis-kiri garis-kanan" height="20px">&nbsp;</td>
            <td class="garis-kiri garis-kanan" height="20px">&nbsp;</td>
            <td class="garis-kiri garis-kanan" height="20px">&nbsp;</td>
            <td class="garis-kiri garis-kanan" height="20px">&nbsp;</td>
        </tr>
        <tr>
            <td class="garis-kiri garis-bawah">(.................................)</td>
            <td class="garis-kiri garis-bawah">(.................................)</td>
            <td class="garis-kiri garis-bawah">(.................................)</td>
            <td class="garis-kiri garis-kanan garis-bawah">(.................................)</td>
        </tr>
    </table>
    <script type="text/javascript">
        function loadPrint() {
            window.print();
            setTimeout(function() {
                window.location="form_cetak_paket.php?status=1";
            }, 3000);
        }
    </script>
</body>

</html>