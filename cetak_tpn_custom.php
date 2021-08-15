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
$id_tpn_list = isset($_GET['id_list']) ? $_GET['id_list'] : '';
$data_pasien = $db->prepare("SELECT al.*,rp.nama,rp.nomedrek FROM registerpasien rp INNER JOIN apotek_tpn_list al ON(rp.id_pasien=al.id_register) WHERE al.id_tpn_list=:id");
$data_pasien->bindParam(":id",$id_tpn_list,PDO::PARAM_INT);
$data_pasien->execute();
$pasien = $data_pasien->fetch(PDO::FETCH_ASSOC);
$amino_acid = isset($pasien['amino_acid']) ? $pasien['amino_acid'] : '0';
$dex40 = isset($pasien['dex40']) ? $pasien['dex40'] : '0';
$dex10 = isset($pasien['dex10']) ? $pasien['dex10'] : '0';
$kcl = isset($pasien['kcl']) ? $pasien['kcl'] : '0';
$ca_glu_10 = isset($pasien['ca_glu_10']) ? $pasien['ca_glu_10'] :'0';
$mgso4_40 = isset($pasien['mgso4_40']) ? $pasien['mgso4_40'] : '0';
$ns_3 = isset($pasien['ns_3']) ? $pasien['ns_3'] : '0';
$heparin = isset($pasien['heparin']) ? $pasien['heparin'] : '0';


?>
<!doctype html>
<html lang="en">
  <head>
    <title>TPN STICKER</title>
    <!-- Required meta tags -->
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
		<meta http-equiv="refresh" content="1;url=tpn.php">
  </head>
  <body onload="loadPrint()">
		<table class="table1" border="1" width="100%" style="border-collapse: collapse;font-size:12px">
			<tr>
				<td colspan="2" width="70%"><b><?php echo $pasien['nama']."<br />(".$pasien['nomedrek'].")"; ?></b></td>
				<td width="30%"><?php echo date('d/m/Y',strtotime($pasien['created_at'])); ?></td>
			</tr>
			<tr>
				<td colspan="3">
					<table style="font-size:12px;">
						<!-- <tr>
							<td colspan="3">
								<b><?php echo $data['tipe_tpn']." 1:".$data['gr']." ml Dx ".$data['konsentrasi']."%"; ?></b>
							</td>
						</tr> -->
						<tr>
							<td>AS 6%</td>
							<td>:</td>
							<td><?php echo $amino_acid." cc (Belum)"; ?></td>
							<td>Ca Gluko</td>
							<td>:</td>
							<td><?php echo $ca_glu_10." cc"; ?></td>
						</tr>
						<tr>
							<td>Dx 40%</td>
							<td>:</td>
							<td><?php echo $dex40." cc"; ?></td>
							<td>Mgso4 40%</td>
							<td>:</td>
							<td><?php echo $mgso4_40." cc"; ?></td>
						</tr>
						<tr>
							<td>Dx 10%</td>
							<td>:</td>
							<td><?php echo $dex10." cc"; ?></td>
							<td>Nacl 3%</td>
							<td>:</td>
							<td><?php echo  $ns_3." cc"; ?></td>
						</tr>
						<tr>
							<td>Kcl 7,46%</td>
							<td>:</td>
							<td><?php echo $kcl." cc"; ?></td>
							<td>Heparin</td>
							<td>:</td>
							<td><?php echo  $heparin." IU"; ?></td>
						</tr>
					</table>
				</td>
			</tr>
		</table>

    <!-- Optional JavaScript -->
  </body>
	<script type="text/javascript">
		function loadPrint(){
			window.print();
			setTimeout(function(){
				window.close();
			},100);
		}
	</script>
</html>
