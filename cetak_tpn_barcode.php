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
$pasien_pasien = $db->prepare("SELECT al.*,rp.nama,rp.nomedrek FROM registerpasien rp INNER JOIN apotek_tpn_list al ON(rp.id_pasien=al.id_register) WHERE al.id_tpn_list=:id");
$pasien_pasien->bindParam(":id",$id_tpn_list,PDO::PARAM_INT);
$pasien_pasien->execute();
$pasien = $pasien_pasien->fetch(PDO::FETCH_ASSOC);

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
						<tr>
							<td colspan="6">
								<b><?php echo $pasien['tipe_tpn']." 1:".$pasien['gr']." ml Dx ".$pasien['konsentrasi']."%"; ?></b>
							</td>
						</tr>
						<tr>
							<td>AS 6%</td>
							<td>:</td>
							<td><?php echo $pasien['amino_acid']." cc (Belum)"; ?></td>
							<td>Ca Gluko</td>
							<td>:</td>
							<td><?php echo $pasien['ca_glu_10']." cc"; ?></td>
						</tr>
						<tr>
							<td>Dx 40%</td>
							<td>:</td>
							<td><?php echo $pasien['dex40']." cc"; ?></td>
							<td>Mgso4 40%</td>
							<td>:</td>
							<td><?php echo $pasien['mgso4_40']." cc"; ?></td>
						</tr>
						<tr>
							<td>Dx 10%</td>
							<td>:</td>
							<td><?php echo $pasien['dex10']." cc"; ?></td>
							<td>Nacl 3%</td>
							<td>:</td>
							<td><?php echo  $pasien['ns_3']." cc"; ?></td>
						</tr>
						<tr>
							<td>Kcl 7,46%</td>
							<td>:</td>
							<td><?php echo $pasien['kcl']." cc"; ?></td>
							<td>Heparin</td>
							<td>:</td>
							<td><?php echo $pasien['heparin']." IU"; ?></td>
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
