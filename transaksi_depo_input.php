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
if (file_exists('config/waktu_filter.txt')) {
	$myData = file_get_contents('config/waktu_filter.txt');
	$rangeDate = "-" . (int)$myData . " days";
} else {
	$rangeDate = 0;
}
$today = date('Ymd');
$hari_ini = date('d/m/Y');
$h7 = date('Ymd', strtotime($rangeDate));
$tipe_depo = $tipes[2];
$conf = json_decode(file_get_contents("config/env_depo.json"), true);
$id_depo = $conf[$tipe_depo]["id_depo"];

$get_keluar = $db->query("SELECT * FROM `barangkeluar_depo` WHERE status_keluar='draft' AND asal_warehouse='" . $id_depo . "'");
$data_keluar = $get_keluar->fetchAll(PDO::FETCH_ASSOC);
//get data petugas
$get_petugas = $db->query("SELECT p.*,peg.nama FROM petugas p INNER JOIN pegawai peg ON(p.id_pegawai=peg.id_pegawai) WHERE p.instalasi LIKE 'FARMASI'");
$data_petugas = $get_petugas->fetchAll(PDO::FETCH_ASSOC);
//Warehouse
$get_warehouse = $db->query("SELECT * FROM warehouse WHERE depo_set='y' AND id_warehouse<>'" . $id_depo . "'");
$all_warehouse = $get_warehouse->fetchAll(PDO::FETCH_ASSOC);
?>
<!DOCTYPE html>
<html>

<head>
	<meta charset="UTF-8">
	<title>SIMRS <?php echo $version_depo; ?> | <?php echo $tipes[0]; ?></title>
	<meta content='width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no' name='viewport'>
	<!-- Bootstrap 3.3.2 -->
	<link href="../bootstrap/css/bootstrap.min.css" rel="stylesheet" type="text/css" />
	<link href="../plugins/datetimepicker/css/bootstrap-datetimepicker.css" rel="stylesheet" type="text/css" />
	<!-- Font Awesome Icons -->
	<link href="../plugins/font-awesome/4.3.0/font-awesome.min.css" rel="stylesheet" type="text/css" />
	<!-- Ionicons -->
	<link href="../plugins/ionicons/2.0.0/ionicon.min.css" rel="stylesheet" type="text/css" />
	<!-- DATA TABLES -->
	<link href="../plugins/datatables/dataTables.bootstrap.css" rel="stylesheet" type="text/css" />
	<link href="../plugins/select2/select2.min.css" rel="stylesheet" type="text/css" />
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
						<h4><i class="icon fa fa-check"></i>Berhasil</h4>Data obat telah diupdate
					</center>
				</div>
			<?php } else if (isset($_GET['status']) && ($_GET['status'] == "2")) { ?><div class="alert alert-success alert-dismissable"><button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>
					<center>
						<h4><i class="icon fa fa-check"></i>Berhasil</h4>Data pasien telah diproses
					</center>
				</div>
			<?php } else if (isset($_GET['status']) && ($_GET['status'] == "3")) { ?><div class="alert alert-success alert-dismissable"><button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>
					<center>
						<h4><i class="icon fa fa-check"></i>Berhasil!</h4>Transaksi Resep Rawat Jalan Berhasil dibatalkan
					</center>
				</div>
			<?php } else if (isset($_GET['status']) && ($_GET['status'] == "4")) { ?><div class="alert alert-success alert-dismissable"><button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>
					<center>
						<h4><i class="icon fa fa-check"></i>Berhasil!</h4>Transaksi Resep Rawat Jalan Berhasil diselesaikan
					</center>
				</div>
			<?php } ?>
			<!-- end pesan -->
			<section class="content-header">
				<h1>
					Data Transaksi Antar Depo
					<!-- <small></small> -->
				</h1>
				<ol class="breadcrumb">
					<li><a href="index.php"><i class="fa fa-dashboard"></i> Home</a></li>
					<li class="active">Data Transaksi Antar Depo</li>
				</ol>
			</section>

			<!-- Main content -->
			<section class="content">
				<div class="row">
					<div class="col-xs-12">
						<div class="box box-primary">
							<div class="box-header">
								<i class="fa fa-user"></i>
								<h3 class="box-title">Form Input Barang Keluar Depo</h3>
							</div><!-- /.box-header -->
							<form action="transaksi_depo_input_acc.php" method="post">
								<div class="box-body">
									<div class="row">
										<div class="col-md-4">
											<div class="form-group">
												<label for="">Tanggal Permintaan <span style="color:red">*Wajib diisi</span></label>
												<div class='input-group date' id='datetimepicker1'>
													<input type='text' name="tanggal_permintaan" class="form-control" required />
													<span class="input-group-addon">
														<span class="glyphicon glyphicon-calendar"></span>
													</span>
												</div>
											</div>
										</div>
										<div class="col-md-4">
											<div class="form-group">
												<label for="">Warehouse <span style="color:red">*Wajib diisi</span></label>
												<select class="form-control ware" name="id_warehouse" style="width:100%" required>
													<option value=""></option>
													<?php
													foreach ($all_warehouse as $aw) {
														echo '<option value="' . $aw['id_warehouse'] . '">' . $aw['nama_ruang'] . '</option>';
													}
													?>
												</select>
											</div>
										</div>
										<div class="col-md-4">
											<div class="form-group">
												<label for="">Permintaan <span style="color:red">*Wajib diisi</span></label>
												<select class="form-control select2" name="permintaan" id="permintaan" style="width:100%" required>
													<option value=""></option>
													<?php
													foreach ($data_petugas as $dp) {
														echo '<option value="' . $dp['id_pegawai'] . '">' . $dp['nama'] . '</option>';
													}
													?>
												</select>
											</div>
										</div>
									</div>
								</div><!-- /.box-body -->
								<div class="box-footer">
									<button class="btn btn-sm btn-primary"><i class="fa fa-plus"></i>Tambah Data</button>
								</div>
							</form>
						</div><!-- /.box -->
					</div><!-- /.col -->
				</div><!-- /.row -->
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
	<script src="../plugins/datetimepicker/js/moment-with-locales.js" type="text/javascript"></script>
	<script src="../plugins/datetimepicker/js/bootstrap-datetimepicker.js" type="text/javascript"></script>
	<!-- DATA TABES SCRIPT -->
	<script src="../plugins/datatables/jquery.dataTables.min.js" type="text/javascript"></script>
	<script src="../plugins/datatables/dataTables.bootstrap.js" type="text/javascript"></script>
	<!-- SlimScroll -->
	<script src="../plugins/slimScroll/jquery.slimscroll.min.js" type="text/javascript"></script>
	<!-- FastClick -->
	<script src='../plugins/fastclick/fastclick.min.js'></script>
	<!-- select2 -->
	<script src='../plugins/select2/select2.full.min.js'></script>
	<!-- AdminLTE App -->
	<script src="../dist/js/app.min.js" type="text/javascript"></script>
	<!-- page script -->
	<script type="text/javascript">
		$(function() {
			$('.select2').select2({
				placeholder: "Pilih Nama Petugas",
				allowClear: true,
				width: 'resolve'
			});
			$('.ware').select2({
				placeholder: "Pilih Nama Warehouse",
				allowClear: true,
				width: 'resolve'
			});
			$('#example2').DataTable();
			$('#datetimepicker1').datetimepicker({
				format: "DD/MM/YYYY H:m:s"
			});
		});
	</script>

</body>

</html>