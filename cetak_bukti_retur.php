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
$id_parent_retur = $_GET['id'];
$limit_awal = isset($_GET['la']) ? $_GET['la'] : 0;
$limit_akhir = isset($_GET['lk']) ? $_GET['lk'] : 23;
$hal = isset($_GET['hal']) ? $_GET['hal'] : 1;
$get_head = $db->query("SELECT pr.*,r.nama,r.nomedrek,r.jpasien FROM `parent_retur_obat` pr INNER JOIN rincian_obat_pasien rp ON(pr.id_rincian_obat=rp.id_rincian_obat) INNER JOIN registerpasien r ON(r.id_pasien=rp.id_pasien) WHERE pr.id_parent_retur='" . $id_parent_retur . "'");
$head = $get_head->fetch(PDO::FETCH_ASSOC);
$get_total_item = $db->query("SELECT ro.nama_obat FROM `parent_retur_obat` pr INNER JOIN rincian_retur_obat ro ON(pr.id_parent_retur=ro.id_parent_retur) WHERE pr.id_parent_retur='" . $id_parent_retur . "' GROUP BY ro.nama_obat");
$data_total = $get_total_item->fetch(PDO::FETCH_ASSOC);
$total_item = $get_total_item->rowCount();
$next_hal = $hal + 1;
$next_la = $limit_awal + 22;
$next_lk = $limit_akhir + 22;
$total_page = ceil($total_item / 23);
$total_hal = $total_item / $limit_akhir;
if (floor($total_hal) > 0) {
  $link = '<meta http-equiv="refresh" content="2;url=cetak_bukti_transaksi.php?id=' . $id_parent_retur . '&la=' . $next_la . '&lk=' . $next_lk . '&hal=' . $next_hal . '">';
  $body = '<body onload="window.print();">';
} else {
  // $link ='<meta http-equiv="refresh" content="2;url=cetak_bukti_transaksi.php?id='.$id_parent_retur.'">';
  // $body = '<body onload="window.print();window.close();">';
  $link = "";
  $body = '<body onload="loadPrint();">';
  $body .= '<script type="text/javascript">
		function loadPrint(){
			window.print();
			setTimeout(function(){
				window.close();
			},100);
		}
	</script>';
}
$get_transaksi = $db->query("SELECT ro.created_at,g.nama,SUM(ro.jumlah_retur) as volume,g.satuan_jual,g.bentuk_sediaan,g.kadar,g.satuan_kadar,g.kemasan,rd.jenis,rd.merk,rd.pabrikan FROM `parent_retur_obat` pr INNER JOIN rincian_retur_obat ro ON(pr.id_parent_retur=ro.id_parent_retur) INNER JOIN rincian_detail_obat rd ON(ro.id_detail_rincian=rd.id_detail_rincian) INNER JOIN gobat g ON(g.id_obat=rd.id_obat) WHERE pr.id_parent_retur='" . $id_parent_retur . "' GROUP BY ro.nama_obat ORDER BY ro.created_at LIMIT $limit_awal,$limit_akhir");
$trans = $get_transaksi->fetchAll(PDO::FETCH_ASSOC);
$cetak = date('d F Y H:i:s');
?>
<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="utf-8">
  <meta http-equiv="X-UA-Compatible" content="IE=edge">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <?php echo $link; ?>
  <title>FROM PENGEMBALIAN BMHP/OBAT PASIEN</title>
  <style>
    body {
      font-size: arial;
      font-size: 10px;
    }

    #head_kop {
      font-weight: bold;
    }

    table {
      border-collapse: collapse;
    }

    #biaya {
      font-size: 12px;
      font-weight: bold;
    }
  </style>
</head>
<?php echo $body; ?>
<div style="font-size:12px;text-align:center;"><b>INSTALASI FARMASI RSKIA KOTA BANDUNG</b></div>
<table id="head_kop" border="0" width="100%">
  <tr>
    <td width="15%">NO.RM / NAMA</td>
    <td width="30%">: <?php echo $head['nomedrek'] . " / " . $head['nama']; ?></td>
    <td width="20%">JENIS TRANSAKSI</td>
    <td width="35%">: RETUR</td>
  </tr>
  <tr>
    <td width="15%">TANGGAL CETAK</td>
    <td width="30%">: <?php echo $cetak; ?></td>
    <td width="20%">NO/TANGGAL RETUR</td>
    <td width="35%">: <?php echo $id_parent_retur; ?> / <?php echo date('d F Y', strtotime($head['created_at'])); ?></td>
  </tr>
  <tr>
    <td width="15%">STATUS</td>
    <td width="30%">: <?php echo $head['jpasien']; ?></td>
    <td width="20%">RUANGAN / PETUGAS</td>
    <td width="35%">: <?php echo $head['ruangan'] . " / " . $head['petugas_retur']; ?></td. </tr>
</table>
<table border="1" width="100%">
  <thead>
    <tr align="center">
      <th>NO</th>
      <th>NAMA BARANG</th>
      <th>JUMLAH</th>
      <th>SATUAN</th>
      <th>NO</th>
      <th>NAMA BARANG</th>
      <th>JUMLAH</th>
      <th>SATUAN</th>
    </tr>
  </thead>
  <?php
  for ($i = 0; $i <= 10; $i++) {
    $no = $i + 1;
    $no2 = $i + 12;
    $col1 = $i;
    $col2 = $i + 11;
    $nama_col_1 = isset($trans[$col1]['nama']) ? $trans[$col1]['nama'] : '';
    $jenis_col_1 = isset($trans[$col1]['jenis']) ? $trans[$col1]['jenis'] : '';
    $merk_col_1 = isset($trans[$col1]['merk']) ? $trans[$col1]['merk'] : '';
    $pabrikan_col_1 = isset($trans[$col1]['pabrikan']) ? $trans[$col1]['pabrikan'] : '';
    $kadar_col_1 = isset($trans[$col1]['kadar']) ? $trans[$col1]['kadar'] : '';
    $satuan_kadar_col_1 = isset($trans[$col1]['satuan_kadar']) ? $trans[$col1]['satuan_kadar'] : '';
    $bentuk_sediaan_col_1 = isset($trans[$col1]['bentuk_sediaan']) ? $trans[$col1]['bentuk_sediaan'] : '';
    $kemasan_col_1 = isset($trans[$col1]['kemasan']) ? $trans[$col1]['kemasan'] : '';
    $satuan_jual_col_1 = isset($trans[$col1]['satuan_jual']) ? $trans[$col1]['satuan_jual'] : '';
    if ($jenis_col_1 == 'generik') {
      $nama_1 = $nama_col_1 . " (" . $pabrikan_col_1 . ")";
    } else if ($jenis_col_1 == 'non generik') {
      $nama_1 = trim($merk_col_1 . " " . $kadar_col_1 . " " . $satuan_kadar_col_1 . " " . $bentuk_sediaan_col_1 . " " . $kemasan_col_1);
    } else if ($jenis_col_1 == 'bmhp') {
      $nama_1 = trim($merk_col_1 . " " . $kadar_col_1 . " " . $satuan_kadar_col_1 . " " . $bentuk_sediaan_col_1 . " " . $kemasan_col_1);
    } else {
      $nama_1 = $nama_col_1;
    }

    $nama_col_2 = isset($trans[$col2]['nama']) ? $trans[$col2]['nama'] : '';
    $jenis_col_2 = isset($trans[$col2]['jenis']) ? $trans[$col2]['jenis'] : '';
    $merk_col_2 = isset($trans[$col2]['merk']) ? $trans[$col2]['merk'] : '';
    $pabrikan_col_2 = isset($trans[$col2]['pabrikan']) ? $trans[$col2]['pabrikan'] : '';
    $kadar_col_2 = isset($trans[$col2]['kadar']) ? $trans[$col2]['kadar'] : '';
    $satuan_kadar_col_2 = isset($trans[$col2]['satuan_kadar']) ? $trans[$col2]['satuan_kadar'] : '';
    $bentuk_sediaan_col_2 = isset($trans[$col2]['bentuk_sediaan']) ? $trans[$col2]['bentuk_sediaan'] : '';
    $kemasan_col_2 = isset($trans[$col2]['kemasan']) ? $trans[$col2]['kemasan'] : '';
    $satuan_jual_col_2 = isset($trans[$col2]['satuan_jual']) ? $trans[$col2]['satuan_jual'] : '';
    if ($jenis_col_2 == 'generik') {
      $nama_2 = $nama_col_2 . " (" . $pabrikan_col_2 . ")";
    } else if ($jenis_col_2 == 'non generik') {
      $nama_2 = trim($merk_col_2 . " " . $kadar_col_2 . " " . $satuan_kadar_col_2 . " " . $bentuk_sediaan_col_2 . " " . $kemasan_col_2);
    } else if ($jenis_col_2 == 'bmhp') {
      $nama_2 = trim($merk_col_2 . " " . $kadar_col_2 . " " . $satuan_kadar_col_2 . " " . $bentuk_sediaan_col_2 . " " . $kemasan_col_2);
    } else {
      $nama_2 = $nama_col_2;
    }
    $vol_col_1 = isset($trans[$col1]['volume']) ? $trans[$col1]['volume'] : '';
    $vol_col_2 = isset($trans[$col2]['volume']) ? $trans[$col2]['volume'] : '';
    echo '<tr>
            <td align="center" width="3%">' . $no . '</td>
            <td width="40%">' . $nama_1 . '</td>
            <td align="center">' . $vol_col_1 . '</td>
            <td align="center">' . $satuan_jual_col_1 . '</td>
            <td align="center" width="3%">' . $no2 . '</td>
            <td width="40%">' . $nama_2 . '</td>
            <td align="center">' . $vol_col_2 . '</td>
            <td align="center">' . $satuan_jual_col_2 . '</td>
          </tr>';
  }
  ?>
</table>
<table width="100%">
  <tr>
    <td style="text-align:center;">
      PETUGAS PENERIMA <br><br><br>
      (..................................................)
    </td>
    <td style="font-size:14px;text-align:center;">Total Harga : <?php echo "Rp." . number_format($head['total_retur'], 2, ',', '.'); ?></td>
    <td style="text-align:center;">
      PETUGAS PENYIMPAN <br><br><br>
      (..................................................)
    </td>
  </tr>
</table>
</body>

</html>