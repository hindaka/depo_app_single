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
$id_tpn = isset($_GET['tpn']) ? $_GET['tpn'] : '';
$nomedrek = isset($_GET['nomedrek']) ? $_GET['nomedrek'] : '';
$data_pasien = $db->prepare("SELECT * FROM pasien WHERE nomedrek=:nomedrek");
$data_pasien->bindParam(":nomedrek",$nomedrek,PDO::PARAM_INT);
$data_pasien->execute();
$pasien = $data_pasien->fetch(PDO::FETCH_ASSOC);

$stmt = $db->prepare("SELECT * FROM tpn_apotek WHERE id_tpn_apotek=:id");
$stmt->bindParam(":id",$id_tpn,PDO::PARAM_INT);
$stmt->execute();
$data = $stmt->fetch(PDO::FETCH_ASSOC);
?>
<html>
<head>
<!-- <meta http-equiv="refresh" content="1;url=tpn.php"> -->
<style>
	@page{
		size : 160mm 200mm;
		margin: 2mm 10mm 3mm 10mm;
	}
	body{
		margin: 0mm;
	}
	.table1{
		width: 160mm;
	}
	.space{
		width: 10mm;
	}
	.space_bar{
		height: 20mm;
	}
	.panjang_gambar{
		width: 5mm;
	}
	.panjang_content{
		width: 100mm;
	}
	.lebar{
		width: 38mm;
	}
	.rotateimg180 {
		width: 50%;
		-webkit-transform:rotate(-90deg);
		-moz-transform: rotate(-90deg);
		-ms-transform: rotate(-90deg);
		-o-transform: rotate(-90deg);
		transform: rotate(-90deg);
	}
</style>
</head>
<body onload="loadPrint()">
<!-- <body> -->
	<table class="table1" border="0">
		<!--Baris 1-->
		<tr>
			<td align="left" valign="middle" class="panjang_content">
				<table>
					<tr>
						<td>
							<font size="1" face="Arial" >
								<b><?php echo $data['tipe_tpn']." 1:".$data['gr']." ml Dx ".$data['konsentrasi']."%"; ?></b> <br>
								AS 6% : <?php echo $data['amino_acid']." cc (Belum)"; ?> <br>
								Dx 40% : <?php echo $data['dex40']." cc"; ?> <br>
								Dx 10% : <?php echo $data['dex10']." cc"; ?> <br>
								Kcl 7,46% : <?php echo $data['kcl']." cc"; ?> <br>
								Ca Gluko : <?php echo $data['ca_glu_10']." cc"; ?> <br>
								Mgso4 40% : <?php echo $data['mgso4_40']." cc"; ?> <br>
								Nacl 3% : <?php echo  $data['ns_3']." cc"; ?> <br>
							</font>
						</td>
						<td valign="top">
							<font size="1" face="Arial" >
								Nomedrek : <?php echo $pasien['nomedrek']; ?><br>
								Nama : <?php echo $pasien['nama']; ?><br>
								Tanggal Cetak : <?php echo date('d/m/Y'); ?>
							</font>
						</td>
					</tr>
				</table>
			</td>
			<td class="space"></td>
			<td align="left" valign="middle" class="panjang_content">
				<table>
					<tr>
						<td>
							<font size="1" face="Arial" >
								<b><?php echo $data['tipe_tpn']." 1:".$data['gr']." ml Dx ".$data['konsentrasi']."%"; ?></b> <br>
								AS 6% : <?php echo $data['amino_acid']." cc (Belum)"; ?> <br>
								Dx 40% : <?php echo $data['dex40']." cc"; ?> <br>
								Dx 10% : <?php echo $data['dex10']." cc"; ?> <br>
								Kcl 7,46% : <?php echo $data['kcl']." cc"; ?> <br>
								Ca Gluko : <?php echo $data['ca_glu_10']." cc"; ?> <br>
								Mgso4 40% : <?php echo $data['mgso4_40']." cc"; ?> <br>
								Nacl 3% : <?php echo  $data['ns_3']." cc"; ?> <br>
							</font>
						</td>
						<td valign="top">
							<font size="1" face="Arial" >
								Nomedrek : <?php echo $pasien['nomedrek']; ?><br>
								Nama : <?php echo $pasien['nama']; ?><br>
								Tanggal Cetak : <?php echo date('d/m/Y'); ?>
							</font>
						</td>
					</tr>
				</table>
			</td>
		</tr>
		<tr class="space_bar"></tr>
		<!-- baris 3 -->
		<tr>
			<td align="left" valign="middle" class="panjang_content">
				<table>
					<tr>
						<td>
							<font size="1" face="Arial" >
								<b><?php echo $data['tipe_tpn']." 1:".$data['gr']." ml Dx ".$data['konsentrasi']."%"; ?></b> <br>
								AS 6% : <?php echo $data['amino_acid']." cc (Belum)"; ?> <br>
								Dx 40% : <?php echo $data['dex40']." cc"; ?> <br>
								Dx 10% : <?php echo $data['dex10']." cc"; ?> <br>
								Kcl 7,46% : <?php echo $data['kcl']." cc"; ?> <br>
								Ca Gluko : <?php echo $data['ca_glu_10']." cc"; ?> <br>
								Mgso4 40% : <?php echo $data['mgso4_40']." cc"; ?> <br>
								Nacl 3% : <?php echo  $data['ns_3']." cc"; ?> <br>
							</font>
						</td>
						<td valign="top">
							<font size="1" face="Arial" >
								Nomedrek : <?php echo $pasien['nomedrek']; ?><br>
								Nama : <?php echo $pasien['nama']; ?><br>
								Tanggal Cetak : <?php echo date('d/m/Y'); ?>
							</font>
						</td>
					</tr>
				</table>
			</td>
			<td class="space"></td>
			<td align="left" valign="middle" class="panjang_content">
				<table>
					<tr>
						<td>
							<font size="1" face="Arial" >
								<b><?php echo $data['tipe_tpn']." 1:".$data['gr']." ml Dx ".$data['konsentrasi']."%"; ?></b> <br>
								AS 6% : <?php echo $data['amino_acid']." cc (Belum)"; ?> <br>
								Dx 40% : <?php echo $data['dex40']." cc"; ?> <br>
								Dx 10% : <?php echo $data['dex10']." cc"; ?> <br>
								Kcl 7,46% : <?php echo $data['kcl']." cc"; ?> <br>
								Ca Gluko : <?php echo $data['ca_glu_10']." cc"; ?> <br>
								Mgso4 40% : <?php echo $data['mgso4_40']." cc"; ?> <br>
								Nacl 3% : <?php echo  $data['ns_3']." cc"; ?> <br>
							</font>
						</td>
						<td valign="top">
							<font size="1" face="Arial" >
								Nomedrek : <?php echo $pasien['nomedrek']; ?><br>
								Nama : <?php echo $pasien['nama']; ?><br>
								Tanggal Cetak : <?php echo date('d/m/Y'); ?>
							</font>
						</td>
					</tr>
				</table>
			</td>
		</tr>
		<tr class="space_bar"></tr>
		<tr>
			<td align="left" valign="middle" class="panjang_content">
				<table>
					<tr>
						<td>
							<font size="1" face="Arial" >
								<b><?php echo $data['tipe_tpn']." 1:".$data['gr']." ml Dx ".$data['konsentrasi']."%"; ?></b> <br>
								AS 6% : <?php echo $data['amino_acid']." cc (Belum)"; ?> <br>
								Dx 40% : <?php echo $data['dex40']." cc"; ?> <br>
								Dx 10% : <?php echo $data['dex10']." cc"; ?> <br>
								Kcl 7,46% : <?php echo $data['kcl']." cc"; ?> <br>
								Ca Gluko : <?php echo $data['ca_glu_10']." cc"; ?> <br>
								Mgso4 40% : <?php echo $data['mgso4_40']." cc"; ?> <br>
								Nacl 3% : <?php echo  $data['ns_3']." cc"; ?> <br>
							</font>
						</td>
						<td valign="top">
							<font size="1" face="Arial" >
								Nomedrek : <?php echo $pasien['nomedrek']; ?><br>
								Nama : <?php echo $pasien['nama']; ?><br>
								Tanggal Cetak : <?php echo date('d/m/Y'); ?>
							</font>
						</td>
					</tr>
				</table>
			</td>
			<td class="space"></td>
			<td align="left" valign="middle" class="panjang_content">
				<table>
					<tr>
						<td>
							<font size="1" face="Arial" >
								<b><?php echo $data['tipe_tpn']." 1:".$data['gr']." ml Dx ".$data['konsentrasi']."%"; ?></b> <br>
								AS 6% : <?php echo $data['amino_acid']." cc (Belum)"; ?> <br>
								Dx 40% : <?php echo $data['dex40']." cc"; ?> <br>
								Dx 10% : <?php echo $data['dex10']." cc"; ?> <br>
								Kcl 7,46% : <?php echo $data['kcl']." cc"; ?> <br>
								Ca Gluko : <?php echo $data['ca_glu_10']." cc"; ?> <br>
								Mgso4 40% : <?php echo $data['mgso4_40']." cc"; ?> <br>
								Nacl 3% : <?php echo  $data['ns_3']." cc"; ?> <br>
							</font>
						</td>
						<td valign="top">
							<font size="1" face="Arial" >
								Nomedrek : <?php echo $pasien['nomedrek']; ?><br>
								Nama : <?php echo $pasien['nama']; ?><br>
								Tanggal Cetak : <?php echo date('d/m/Y'); ?>
							</font>
						</td>
					</tr>
				</table>
			</td>
		</tr>
		<tr class="space_bar"></tr>
		<!-- baris 3 -->
		<tr>
			<td align="left" valign="middle" class="panjang_content">
				<table>
					<tr>
						<td>
							<font size="1" face="Arial" >
								<b><?php echo $data['tipe_tpn']." 1:".$data['gr']." ml Dx ".$data['konsentrasi']."%"; ?></b> <br>
								AS 6% : <?php echo $data['amino_acid']." cc (Belum)"; ?> <br>
								Dx 40% : <?php echo $data['dex40']." cc"; ?> <br>
								Dx 10% : <?php echo $data['dex10']." cc"; ?> <br>
								Kcl 7,46% : <?php echo $data['kcl']." cc"; ?> <br>
								Ca Gluko : <?php echo $data['ca_glu_10']." cc"; ?> <br>
								Mgso4 40% : <?php echo $data['mgso4_40']." cc"; ?> <br>
								Nacl 3% : <?php echo  $data['ns_3']." cc"; ?> <br>
							</font>
						</td>
						<td valign="top">
							<font size="1" face="Arial" >
								Nomedrek : <?php echo $pasien['nomedrek']; ?><br>
								Nama : <?php echo $pasien['nama']; ?><br>
								Tanggal Cetak : <?php echo date('d/m/Y'); ?>
							</font>
						</td>
					</tr>
				</table>
			</td>
			<td class="space"></td>
			<td align="left" valign="middle" class="panjang_content">
				<table>
					<tr>
						<td>
							<font size="1" face="Arial" >
								<b><?php echo $data['tipe_tpn']." 1:".$data['gr']." ml Dx ".$data['konsentrasi']."%"; ?></b> <br>
								AS 6% : <?php echo $data['amino_acid']." cc (Belum)"; ?> <br>
								Dx 40% : <?php echo $data['dex40']." cc"; ?> <br>
								Dx 10% : <?php echo $data['dex10']." cc"; ?> <br>
								Kcl 7,46% : <?php echo $data['kcl']." cc"; ?> <br>
								Ca Gluko : <?php echo $data['ca_glu_10']." cc"; ?> <br>
								Mgso4 40% : <?php echo $data['mgso4_40']." cc"; ?> <br>
								Nacl 3% : <?php echo  $data['ns_3']." cc"; ?> <br>
							</font>
						</td>
						<td valign="top">
							<font size="1" face="Arial" >
								Nomedrek : <?php echo $pasien['nomedrek']; ?><br>
								Nama : <?php echo $pasien['nama']; ?><br>
								Tanggal Cetak : <?php echo date('d/m/Y'); ?>
							</font>
						</td>
					</tr>
				</table>
			</td>
		</tr>
		<tr class="space_bar"></tr>
		<tr>
			<td align="left" valign="middle" class="panjang_content">
				<table>
					<tr>
						<td>
							<font size="1" face="Arial" >
								<b><?php echo $data['tipe_tpn']." 1:".$data['gr']." ml Dx ".$data['konsentrasi']."%"; ?></b> <br>
								AS 6% : <?php echo $data['amino_acid']." cc (Belum)"; ?> <br>
								Dx 40% : <?php echo $data['dex40']." cc"; ?> <br>
								Dx 10% : <?php echo $data['dex10']." cc"; ?> <br>
								Kcl 7,46% : <?php echo $data['kcl']." cc"; ?> <br>
								Ca Gluko : <?php echo $data['ca_glu_10']." cc"; ?> <br>
								Mgso4 40% : <?php echo $data['mgso4_40']." cc"; ?> <br>
								Nacl 3% : <?php echo  $data['ns_3']." cc"; ?> <br>
							</font>
						</td>
						<td valign="top">
							<font size="1" face="Arial" >
								Nomedrek : <?php echo $pasien['nomedrek']; ?><br>
								Nama : <?php echo $pasien['nama']; ?><br>
								Tanggal Cetak : <?php echo date('d/m/Y'); ?>
							</font>
						</td>
					</tr>
				</table>
			</td>
			<td class="space"></td>
			<td align="left" valign="middle" class="panjang_content">
				<table>
					<tr>
						<td>
							<font size="1" face="Arial" >
								<b><?php echo $data['tipe_tpn']." 1:".$data['gr']." ml Dx ".$data['konsentrasi']."%"; ?></b> <br>
								AS 6% : <?php echo $data['amino_acid']." cc (Belum)"; ?> <br>
								Dx 40% : <?php echo $data['dex40']." cc"; ?> <br>
								Dx 10% : <?php echo $data['dex10']." cc"; ?> <br>
								Kcl 7,46% : <?php echo $data['kcl']." cc"; ?> <br>
								Ca Gluko : <?php echo $data['ca_glu_10']." cc"; ?> <br>
								Mgso4 40% : <?php echo $data['mgso4_40']." cc"; ?> <br>
								Nacl 3% : <?php echo  $data['ns_3']." cc"; ?> <br>
							</font>
						</td>
						<td valign="top">
							<font size="1" face="Arial" >
								Nomedrek : <?php echo $pasien['nomedrek']; ?><br>
								Nama : <?php echo $pasien['nama']; ?><br>
								Tanggal Cetak : <?php echo date('d/m/Y'); ?>
							</font>
						</td>
					</tr>
				</table>
			</td>
		</tr>
	</table>
	<script type="text/javascript">
		function loadPrint(){
			window.print();
			setTimeout(function(){
				window.close();
			},100);
		}
	</script>
</body>
</html>
