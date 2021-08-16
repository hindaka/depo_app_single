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
$ruangan = isset($_GET['ruangan']) ? $_GET['ruangan'] : '';
$bulan = isset($_GET["bulan"]) ? $_GET['bulan'] : date('m');
$tahun = isset($_GET["tahun"]) ? $_GET['tahun'] : date('Y');
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
	<!-- Ionicons -->
	<link href="../plugins/ionicons/2.0.0/ionicon.min.css" rel="stylesheet" type="text/css" />
	<!-- DATA TABLES -->
	<link href="../plugins/datatables/dataTables.bootstrap.css" rel="stylesheet" type="text/css" />
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
						<h4><i class="icon fa fa-check"></i>Berhasil</h4>Data TPN berhasil ditambahkan
					</center>
				</div>
			<?php } else if (isset($_GET['status']) && ($_GET['status'] == "2")) { ?><div class="alert alert-success alert-dismissable"><button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>
					<center>
						<h4><i class="icon fa fa-check"></i>Berhasil</h4>Data TPN berhasil dihapus
					</center>
				</div>
			<?php } else if (isset($_GET['status']) && ($_GET['status'] == "3")) { ?><div class="alert alert-danger alert-dismissable"><button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>
					<center>
						<h4><i class="icon fa fa-ban"></i>Peringatan!</h4>Data pasien gagal diubah
					</center>
				</div>
			<?php } ?>
			<!-- end pesan -->
			<section class="content-header">
				<h1>
					Pengelolaan Stiker
					<small>Stiker TPN</small>
				</h1>
				<ol class="breadcrumb">
					<li><a href="index.php"><i class="fa fa-dashboard"></i> Home</a></li>
					<li class="active">Pengelolaan Stiker TPN</li>
				</ol>
			</section>

			<!-- Main content -->
			<section class="content">
				<div class="row">
					<div class="col-xs-12">
						<div class="box box-primary">
							<div class="box-header">
								<i class="fa fa-user"></i>
								<h3 class="box-title">Data TPN</h3>
								<div class="pull-right">
									<!-- <a class="btn bg-purple" href="tpn_custom.php"><i class="fa fa-pencil"></i> Custom TPN</a> -->
									<a class="btn btn-primary" href="tpn_add.php"><i class="fa fa-plus"></i> Tambah TPN</a>
								</div>
							</div><!-- /.box-header -->
							<div class="box-body">
								<div class="table-responsive">
									<table id="example1" class="table table-bordered table-striped" width="100%">
										<thead>
											<tr class="info">
												<th>Tipe TPN</th>
												<th>Konsentrasi</th>
												<th>Gram</th>
												<th>Amino Acid 6%</th>
												<th>Dextrose 40%</th>
												<th>Dextrose 10%</th>
												<th>KCL</th>
												<th>Ca Glukonas 10%</th>
												<th>MgSO4 40%</th>
												<th>NS 3%</th>
												<th>Hapus</th>
											</tr>
										</thead>
									</table>
								</div>
							</div><!-- /.box-body -->
						</div><!-- /.box -->
					</div><!-- /.col -->
				</div><!-- /.row -->
			</section><!-- /.content -->
		</div><!-- /.content-wrapper -->
		<div id="myModal" class="modal fade" role="dialog">
			<div class="modal-dialog modal-primary">
				<!-- Modal content-->
				<div class="modal-content">
					<div class="modal-header">
						<button type="button" class="close" data-dismiss="modal">&times;</button>
						<h4 class="modal-title">Form Cetak Stiker TPN</h4>
					</div>
					<div class="modal-body">
						<input type="hidden" name="id_tpn" id="id_tpn">
						<div class="form-group">
							<label for="">Nomor Rekam Medis <span style="color:red">*</span></label>
							<input type="text" class="form-control" id="nomedrek" name="nomedrek" placeholder="Masukan Nomor Rekam Medis Pasien" autocomplete="off" required>
						</div>
						<div class="form-group">
							<label for="">Heparin <span style="color:red">*</span></label>
							<input type="text" class="form-control" id="heparin" name="heparin" placeholder="Masukan Jumlah Penggunaan Heparin" autocomplete="off" required>
						</div>
					</div>
					<div class="modal-footer">
						<button id="kirimTpn" class="btn btn-success"><i class="fa fa-send"></i> Kirim</button>
						<button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
					</div>
				</div>

			</div>
		</div>
		<!-- static footer -->
		<?php include "footer.php"; ?>
		<!-- /.static footer -->
	</div><!-- ./wrapper -->

	<!-- jQuery 2.1.3 -->
	<script src="../plugins/jQuery/jQuery-2.1.3.min.js"></script>
	<!-- Bootstrap 3.3.2 JS -->
	<script src="../bootstrap/js/bootstrap.min.js" type="text/javascript"></script>
	<!-- DATA TABES SCRIPT -->
	<script src="../plugins/datatables/jquery.dataTables.min.js" type="text/javascript"></script>
	<script src="../plugins/datatables/dataTables.bootstrap.js" type="text/javascript"></script>
	<!-- SlimScroll -->
	<script src="../plugins/slimScroll/jquery.slimscroll.min.js" type="text/javascript"></script>
	<!-- FastClick -->
	<script src='../plugins/fastclick/fastclick.min.js'></script>
	<!-- AdminLTE App -->
	<script src="../dist/js/app.min.js" type="text/javascript"></script>
	<script src="../plugins/sweetalert/sweetalert.min.js" type="text/javascript"></script>
	<!-- page script -->
	<script type="text/javascript">
		var master_resep;
		$(function() {
			$(document).on("click", ".opentpn", function() {
				var tpnId = $(this).data('tpnid');
				$(".modal-body #nomedrek").val('');
				$(".modal-body #id_tpn").val(tpnId);
			});
			$('#kirimTpn').on('click', function() {
				var id_tpn = $('#id_tpn').val();
				var nomedrek = $('#nomedrek').val();
				var heparin = $('#heparin').val();
				window.location.href = "cetak_tpn_barcode.php?tpn=" + id_tpn + "&nomedrek=" + nomedrek + "&heparin=" + heparin;
			});
			master_obat = $('#example1').DataTable({
				"processing": true,
				"serverSide": true,
				"ajax": {
					"url": "ajax_data/tpn_data.php",
					"type": "GET"
				},
				"columns": [{
						"data": 'tipe_tpn',
						"render": function(data, type, row) {
							return data;
						}
					},
					{
						"data": 'konsentrasi',
						"render": function(data, type, row) {
							return data;
						}
					},
					{
						"data": 'gr',
						"render": function(data, type, row) {
							return data;
						}
					},
					{
						"data": 'amino_acid',
						"render": function(data, type, row) {
							return data;
						}
					},
					{
						"data": 'dex40',
						"render": function(data, type, row) {
							return data;
						}
					},
					{
						"data": 'dex10',
						"render": function(data, type, row) {
							return data;
						}
					},
					{
						"data": 'kcl',
						"render": function(data, type, row) {
							return data;
						}
					},
					{
						"data": 'ca_glu_10',
						"render": function(data, type, row) {
							return data;
						}
					},
					{
						"data": 'mgso4_40',
						"render": function(data, type, row) {
							return data;
						}
					},
					{
						"data": 'ns_3',
						"render": function(data, type, row) {
							return data;
						}
					},
					{
						"data": null,
						"render": function(data, type, row) {
							var btn_tpn = '<button type="button" onclick="del_data(' + data.id_tpn_apotek + ')" class="btn btn-block btn-danger btn-sm"><i class="fa fa-trash"></i> Hapus</button>';
							return btn_tpn;
						}
					},
				],
				"order": [
					[0, 'asc']
				],
			});
		});

		function del_data(id_tpn) {
			console.log(id_tpn);
			swal({
					title: "Apakah anda yakin?",
					text: "Data yang sudah dihapus tidak dapat dikembalikan!",
					icon: "warning",
					buttons: true,
					dangerMode: true,
				})
				.then((willDelete) => {
					if (willDelete) {
						var fd = new FormData();
						fd.append("id", id_tpn);
						$.ajax({
							type: 'POST',
							url: 'ajax_data/delete_tpn.php',
							data: fd,
							contentType: false,
							cache: false,
							processData: false,
							success: function(msg) {
								var response = JSON.parse(msg);
								swal({
									title: response.title,
									text: response.text,
									icon: response.icon,
								}).then((value) => {
									window.location.href = "tpn.php?status=2";
								})
							}
						});
					}
				});
		}
	</script>

</body>

</html>