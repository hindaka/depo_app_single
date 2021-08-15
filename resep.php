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

$h3 = $db->query("SELECT nama FROM nmdokter WHERE nama<>'Bidan' AND nama<>'Perawat' ORDER BY nama ASC");
$data3 = $h3->fetchAll(PDO::FETCH_ASSOC);

?>
<!DOCTYPE html>
<html>

<head>
	<meta charset="UTF-8">
	<title>SIMRS <?php echo $version_depo; ?> | <?php echo $tipes[0]; ?></title>
	<meta content='width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no' name='viewport'>
	<!-- Bootstrap 3.3.2 -->
	<link href="../bootstrap/css/bootstrap.min.css" rel="stylesheet" type="text/css" />
	<!-- Font Awesome Icons -->
	<link href="../plugins/font-awesome/4.3.0/font-awesome.min.css" rel="stylesheet" type="text/css" />
	<!-- daterange picker -->
	<link href="../plugins/datepicker/datepicker3.css" rel="stylesheet" type="text/css" />
	<!-- iCheck for checkboxes and radio inputs -->
	<link href="../plugins/iCheck/all.css" rel="stylesheet" type="text/css" />
	<!-- BootsrapSelect -->
	<link href="../plugins/bootstrap-select/bootstrap-select.min.css" rel="stylesheet" type="text/css" />
	<!-- Ionicons -->
	<link href="../plugins/ionicons/2.0.0/ionicon.min.css" rel="stylesheet" type="text/css" />
	<!-- DATA TABLES -->
	<link href="../plugins/datatables/dataTables.bootstrap.css" rel="stylesheet" type="text/css" />
	<link href="../plugins/pnotify/css/animate.css" rel="stylesheet" type="text/css" />
	<link href="../plugins/pnotify/css/pnotify.custom.min.css" rel="stylesheet" type="text/css" />
	<!-- Theme style -->
	<link href="../dist/css/AdminLTE.min.css" rel="stylesheet" type="text/css" />
	<!-- AdminLTE Skins. Choose a skin from the css/skins
         folder instead of downloading all of them to reduce the load. -->
	<link href="../dist/css/skins/_all-skins.min.css" rel="stylesheet" type="text/css" />

	<!-- HTML5 Shim and Respond.js IE8 support of HTML5 elements and media queries -->
	<!-- WARNING: Respond.js doesn't work if you view the page via file:// -->
	<!--[if lt IE 9]>
        <script src="https://oss.maxcdn.com/libs/html5shiv/3.7.0/html5shiv.js"></script>
        <script src="https://oss.maxcdn.com/libs/respond.js/1.3.0/respond.min.js"></script>
    <![endif]-->
	<style>
		.loader {
			position: absolute;
			top: 0;
			left: 0;
			width: 100%;
			height: 100%;
			background: rgba(255, 255, 255, 0.8) url('../dist/img/test_2.gif') no-repeat 50%;
		}
	</style>
</head>

<body class="<?php echo $skin_depo; ?>">
	<div class="wrapper">

		<!-- static header -->
		<?php include("header.php"); ?>
		<?php include "menu_index.php"; ?>
		<!-- Content Wrapper. Contains page content -->
		<div class="content-wrapper">
			<!-- Content Header (Page header) -->
			<!-- pesan feedback -->
			<?php if (isset($_GET['status']) && ($_GET['status'] == "1")) { ?><div class="alert alert-success alert-dismissable"><button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>
					<center>
						<h4><i class="icon fa fa-check"></i>Berhasil</h4>Data resep telah diinput
					</center>
				</div>
			<?php } else if (isset($_GET['status']) && ($_GET['status'] == "2")) { ?><div class="alert alert-success alert-dismissable"><button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>
					<center>
						<h4><i class="icon fa fa-check"></i>Berhasil</h4>Data pasien telah diproses
					</center>
				</div>
			<?php } else if (isset($_GET['status']) && ($_GET['status'] == "3")) { ?><div class="alert alert-danger alert-dismissable"><button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>
					<center>
						<h4><i class="icon fa fa-ban"></i>Peringatan!</h4>Data resep tidak boleh kosong
					</center>
				</div>
			<?php } else if (isset($_GET['status']) && ($_GET['status'] == "4")) { ?><div class="alert alert-danger alert-dismissable"><button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>
					<center>
						<h4><i class="icon fa fa-ban"></i>Peringatan!</h4>Nomor Rekam Medis tidak terdaftar / Pasien tersebut tidak berobat hari ini!
					</center>
				</div>
			<?php } ?>
			<!-- end pesan -->
			<section class="content-header">
				<h1>
					Transaksi
					<small>resep</small>
				</h1>
				<ol class="breadcrumb">
					<li><a href="index.php"><i class="fa fa-dashboard"></i> Home</a></li>
					<li>Transaksi</li>
					<li class="active">Resep</li>
				</ol>
			</section>

			<!-- Main content -->
			<section class="content">
				<div class="alert alert-info"><span style="color:red">**</span> Tekan Tombol Enter jika sudah selesai menginput No.Rekam Medis untuk mendapatkan Cara Bayar Pasien</div>
				<div class="box">
					<div class="box-header">
						<i class="fa fa-user"></i>
						<h3 class="box-title">Input data resep</h3>
					</div><!-- /.box-header -->
					<!-- form start -->
					<form role="form" action="resepacc.php" method="post">
						<div class="box-body isi">
							<div class="row">
								<div class="col-xs-3">
									<label for="nomedrek">No. Medrek <span style="color:red">**</span></label>
									<input type="text" class="form-control" id="nomedrek" name="nomedrek" placeholder="No. Medrek" autocomplete="off" required>
								</div>
								<div class="col-xs-3">
									<label for="nomedrek">Cara Bayar</label>
									<input type="text" class="form-control" id="cara_bayar" placeholder="Cara Bayar" name="cabar" readonly>
								</div>
								<div class="col-xs-5">
									<label for="namaValue">&nbsp;</label>
									<input type="text" class="form-control" id="namaValue" name="namaValue" placeholder="Isikan Nama Untuk Resep Bebas">
								</div>
							</div><br>
							<div class="row">
								<div class="col-xs-3">
									<label for="tanggalr">Tanggal Resep <span style="color:red">*</span></label>
									<input type="text" class="form-control" autocomplete="off" id="tanggalr" name="tanggalr" placeholder="Tanggal" required>
								</div>
								<div class="col-xs-5">
									<label for="dokter">Dokter <span style="color:red">*</span></label>
									<select class="form-control selectpicker" data-live-search="true" name="dokter" id="dokter" onchange="fixTanggal()" required>
										<option value="">---Pilih Dokter---</option>
										<?php
										foreach ($data3 as $dok) {
											echo "<option value='" . $dok['nama'] . "'>" . $dok['nama'] . "</option>";
										}
										?>
									</select>
								</div>
								<div class="col-xs-3">
									<label for="ruang">Ruang <span style="color:red">*</span></label>
									<select class="form-control selectpicker" data-live-search="true" name="ruang" required>
										<option value="IGD" selected>IGD</option>
									</select>
								</div>
							</div>
							<div class="row">
								<div class="col-xs-3">
									<div class="form-group">
										<label for="bayar"><br>Pembayaran</label><br />
										<input type="radio" name="bayar" id="bayarTunai" class="flat-blue" value="Tunai" required>
										Tunai&nbsp;&nbsp;&nbsp;
										<input type="radio" name="bayar" id="bayarNonTunai" class="flat-blue" value="Non Tunai" required>
										Non Tunai
									</div>
								</div>
								<div class="col-xs-3">
									<div class="form-group">
										<label for=""><br>Jenis Transaksi</label><br>
										<input type="radio" name="jenis_transaksi" id="transaksi1" class="flat-blue" value="Resep" required> Resep &nbsp;&nbsp;&nbsp;
										<input type="radio" name="jenis_transaksi" id="transaksi2" class="flat-blue" value="Bon" required> Bon &nbsp;&nbsp;&nbsp;
									</div>
								</div>
							</div>
						</div><!-- /.box-body -->

						<div class="box-footer">
							<button type="submit" class="btn btn-primary"><i class="fa fa-send"></i> Proses</button>
						</div>
					</form>
				</div>
			</section><!-- /.content -->
		</div><!-- /.content-wrapper -->
		<!-- static footer -->
		<?php include "footer.php"; ?>
		<!-- /.static footer -->
	</div><!-- ./wrapper -->

	<!-- jQuery 2.1.3 -->
	<script src="../plugins/jQuery/jQuery-2.1.3.min.js"></script>
	<!-- Bootstrap 3.3.2 JS -->
	<script src="../bootstrap/js/bootstrap.min.js" type="text/javascript"></script>
	<!-- DATA TABES SCRIPT -->
	<script src="../plugins/datatables/jquery.dataTables.js" type="text/javascript"></script>
	<script src="../plugins/datatables/dataTables.bootstrap.js" type="text/javascript"></script>
	<!-- SlimScroll -->
	<script src="../plugins/slimScroll/jquery.slimscroll.min.js" type="text/javascript"></script>
	<!-- date-picker -->
	<script src="../plugins/datepicker/bootstrap-datepicker.js" type="text/javascript"></script>
	<!-- iCheck 1.0.1 -->
	<script src="../plugins/iCheck/icheck.min.js" type="text/javascript"></script>
	<!-- BootsrapSelect -->
	<script src="../plugins/bootstrap-select/bootstrap-select.min.js" type="text/javascript"></script>
	<!-- typeahead -->
	<script src="../plugins/typeahead/typeahead.bundle.js" type="text/javascript"></script>
	<!-- FastClick -->
	<script src='../plugins/fastclick/fastclick.min.js'></script>
	<!-- pnotify -->
	<script src='../plugins/pnotify/js/pnotify.custom.min.js'></script>
	<script src='../plugins/pnotify/js/modular_pnotify.js'></script>
	<!-- AdminLTE App -->
	<script src="../dist/js/app.min.js" type="text/javascript"></script>
	<script type="text/javascript">
		function fixTanggal() {
			var tanggalr = document.getElementById('tanggalr').value;
			var res = tanggalr.substring(0, 10);
			document.getElementById('tanggalr').value = res;
		}

		function pindah_halaman(param) {
			window.location = 'resep.php?status=' + param;
		}
	</script>
	<script type="text/javascript">
		//setup before functions
		var typingTimer; //timer identifier
		var doneTypingInterval = 1000; //time in ms, 5 second for example
		var resep = $('#nomedrek');
		// //on keyup, start the countdown
		// resep.on('keyup', function () {
		// 		clearTimeout(typingTimer);
		// 		typingTimer = setTimeout(doneTyping, doneTypingInterval);
		// });
		//on keydown, clear the countdown
		resep.on('keydown', function(e) {
			if (e.keyCode == 13) {
				var medrek = document.getElementById("nomedrek").value;
				var data_ajax = {
					"medrek": medrek
				};
				var res = {
					loader: $('<div />', {
						class: 'loader'
					}),
					container: $('.isi')
				}
				if (medrek == '-') {
					document.getElementById('cara_bayar').value = '';
				} else {
					$.ajax({
						type: 'POST',
						url: 'get_cara_bayar.php',
						data: data_ajax,
						dataType: 'json',
						beforeSend: function() {
							res.container.append(res.loader);
						},
						error: function() {
							document.getElementById('nomedrek').value = '';
							$("div.loader").remove();
							// res.container.find(res.loader).remove();
							pindah_halaman(4);
							// buatNotifikasi('Peringatan!','Nomor Rekam Medis yang anda masukan tidak terdaftar!','warning',true);
						},
						success: function(data) {
							var obj = data;
							//set value input nama bayi dengan prefix By.Ny
							document.getElementById('cara_bayar').value = obj.slug_apotek;
							if (obj.slug_apotek == 'umum') {
								console.log('umum');
								$('#bayarTunai').iCheck('check');
								$('#bayarNonTunai').iCheck('uncheck');
							} else {
								$('#bayarNonTunai').iCheck('check');
								$('#bayarTunai').iCheck('uncheck');
							}
							$("div.loader").remove();
							//  res.container.find(res.loader).remove();
						}
					});
				}
			} else {

			}
		});
	</script>
	<!-- page script -->
	<script type="text/javascript">
		//Flat red color scheme for iCheck
		$('input[type="radio"].flat-blue').iCheck({
			radioClass: 'iradio_flat-blue'
		});
		$(document).ready(function() {
			$("#nomedrek").change(function() {
				if ($(this).val() == "-") {
					$("#namaValue").show(500);
				} else {
					$("#namaValue").hide(500);
				}
			});
			$("#namaValue").hide();
		});
		$(function() {
			$("#example1").dataTable();
			$('#example2').dataTable({
				"bPaginate": true,
				"bLengthChange": false,
				"bFilter": false,
				"bSort": true,
				"bInfo": true,
				"bAutoWidth": false
			});
		});
		//Date range picker
		$('#tanggalr').datepicker({
			format: 'dd/mm/yyyy',
			todayHighlight: true,
			autoclose: true
		});
	</script>

</body>

</html>