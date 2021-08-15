<?php
session_start();
include("../inc/pdo.conf.php");
include("../inc/version.php");
date_default_timezone_set('Asia/Jakarta');
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
$id_cetak = isset($_GET['ctx']) ? trim($_GET['ctx']) : '';
$h4 = $db->query("SELECT sd.*,g.nama,g.satuan,g.sumber as sumber_dana FROM stok_opname_det sd INNER JOIN gobat g ON(sd.id_obat=g.id_obat) WHERE id_parent_so='".$id_cetak."'");
$data4 = $h4->fetchAll(PDO::FETCH_ASSOC);
$id_petugas = $r1['mem_id'];
$get_conf = $db->query("SELECT * FROM stok_opname WHERE id_so='".$id_cetak."'");
$data_conf = $get_conf->fetch(PDO::FETCH_ASSOC);
$pj = $data_conf['petugas_so'];
$get_pegawai = $db->query("SELECT * FROM pegawai WHERE id_pegawai='".$pj."'");
$pegawai = $get_pegawai->fetch(PDO::FETCH_ASSOC);

?>
<!DOCTYPE html>
<html>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<meta http-equiv="refresh" content="0;url=stok_opnam_depo.php?status=1">
<title>Form Stok Opname</title>
<style>
  body{
    font-size:14px;
  }
  .bottom{
    border-bottom:5px double black;
  }
</style>
</head>

<body onload="loadPrint()">
  <table width="100%" border="0" cellspacing="0" cellpadding="0">
    <tr>
      <td class='bottom'><img src="clip_image002.png"/></td>
      <td class='bottom'>
        <div align="center">
          <span style="font-size:18px;font-weight:bold;">PEMERINTAH KOTA BANDUNG<br>RUMAH SAKIT KHUSUS IBU DAN ANAK</span><br>
          Jl. KH. Wahid Hasyim (Kopo) No. 311 Bandung. Telepon: 022-86037777 Fax. (022) 5200505Bandung 40242<br />
          Email : sekretariat@rskiakotabandung.com <br />
        </div>
    </td>
  </tr>
  <tr>
    <td colspan="2">
      <div align="center" style="font-size:20px;font-weight:bold"><br />
        FORMULIR STOK OPNAME <br>
				<small>Ruangan : <?php echo "DEPO FARMASI ".$tipes[2]; ?><br>Nama Penyimpanan : <?php echo $data_conf['penyimpanan']; ?> <br> Penanggungjawab : <?php echo $pegawai['nama']; ?></small>
      </div>
    </td>
  </tr>
  <tr>
    <td colspan="2" style="padding:10px">
      <div align="center">
				<table border="1" cellpadding="5">
					<thead>
						<tr class="info">
							<th>No</th>
							<th>Nama</th>
							<th>Satuan</th>
							<th>No Batch</th>
							<th>Expired</th>
							<th>Sumber</th>
							<th>Stok Sistem</th>
							<th>Fisik</th>
						</tr>
					</thead>
					<tbody>
						<?php
						$no=1;
							foreach ($data4 as $row) {
								echo '<tr>
        								<td>'.$no++.'</td>
        								<td>'.$row['nama'].'</td>
        								<td>'.$row['satuan'].'</td>
        								<td>'.$row['no_batch'].'</td>
        								<td>'.date('d-m-Y',strtotime($row['expired'])).'</td>
        								<td>'.$row['sumber_dana'].'</td>
        								<td align="right">'.$row['stok_sistem'].'</td>
        								<td width="50px"></td>
											</tr>';
							}
						?>
					</tbody>
				</table>
      </div>
    </td>
  </tr>
  <tr>
    <td colspan="2" align="right" style="padding-top:20px;padding-right:150px">
      <table border="0" cellspacing="0" cellpadding="0">
        <tr>
          <td>&nbsp;</td>
          <td>&nbsp;</td>
          <td align="center">Bandung, <?php echo date('d F Y'); ?></td>
        </tr>
        <tr>
          <td align="center"></td>
          <td>&nbsp;</td>
          <td align="center"></td>
        </tr>
        <tr>
          <td align="center"></td>
          <td>&nbsp;</td>
          <td align="center">Petugas Stok Opname</td>
        </tr>
        <tr>
          <td colspan='3' align="center" height="100px"><p>&nbsp;</p></td>
        </tr>
        <tr>
          <td align="center"></td>
          <td>&nbsp;</td>
          <td align="center"><u><?php echo $pegawai['nama']; ?></u></td>
        </tr>
        <tr>
          <td align="center">&nbsp;</td>
          <td>&nbsp;</td>
          <td align="center">(NIP. <?php echo $pegawai['nip']; ?>)</td>
        </tr>
      </table>
    </td>
  </tr>
  </table>
	<script type="text/javascript">
		function loadPrint(){
			window.print();
		}
	</script>
</body>
</html>
