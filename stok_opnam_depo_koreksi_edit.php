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
$id_so = isset($_GET['koreksi']) ? $_GET['koreksi'] : '';
$id_obats = isset($_GET['o']) ? $_GET['o'] : '';
$batch = isset($_GET['batch']) ? $_GET['batch'] : '';
$tipe_depo = $tipes[2];
$conf = json_decode(file_get_contents("config/env_depo.json"), true);
$id_depo = $conf[$tipe_depo]["id_depo"];

$get_so = $db->query("SELECT * FROM stok_opname WHERE id_so='" . $id_so . "'");
$data_so = $get_so->fetch(PDO::FETCH_ASSOC);
$tanggal_so = isset($data_so['tanggal_so']) ? $data_so['tanggal_so'] : '';
$split = explode("-", $tanggal_so);
$tahun = $split[0];
$penyimpanan = isset($data_so['penyimpanan']) ? $data_so['penyimpanan'] : '';
$get_obat = $db->query("SELECT ks.*,g.nama FROM kartu_stok_ruangan ks INNER JOIN  gobat g ON(ks.id_obat=g.id_obat) WHERE ks.id_warehouse='" . $id_depo . "' AND in_out='masuk' AND (YEAR(ks.created_at)='" . $tahun . "' OR YEAR(ks.created_at)='2020') GROUP BY ks.id_obat");
$data_obat = $get_obat->fetchAll(PDO::FETCH_ASSOC);
if ($id_obats != '' && $batch != '') {
	if ($batch == 'all') {
		$get_all_obat = $db->query("SELECT ks.*,g.nama FROM kartu_stok_ruangan ks INNER JOIN  gobat g ON(ks.id_obat=g.id_obat) WHERE ks.id_warehouse='" . $id_depo . "' AND in_out='masuk' AND (YEAR(ks.created_at)='" . $tahun . "' OR YEAR(ks.created_at)='2020') AND ks.id_obat='" . $id_obats . "' ORDER BY ks.id_kartu_ruangan DESC");
	} else {
		$get_all_obat = $db->query("SELECT ks.*,g.nama FROM kartu_stok_ruangan ks INNER JOIN  gobat g ON(ks.id_obat=g.id_obat) WHERE ks.id_warehouse='" . $id_depo . "' AND in_out='masuk' AND (YEAR(ks.created_at)='" . $tahun . "' OR YEAR(ks.created_at)='2020') AND ks.id_obat='" . $id_obats . "' AND ks.no_batch LIKE '" . $batch . "' ORDER BY ks.id_kartu_ruangan DESC");
	}
	$all_data_obat = $get_all_obat->fetchAll(PDO::FETCH_ASSOC);
}
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
	<!-- select2 -->
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
	<style media="screen">
		.dataTables_wrapper .dataTables_processing {
			position: absolute;
			top: 30%;
			left: 50%;
			width: 30%;
			height: 40px;
			margin-left: -20%;
			margin-top: -25px;
			padding-top: 20px;
			text-align: center;
			font-size: 1.2em;
			background: none;
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
					Data Stok Opnam
					<small><?php echo $penyimpanan; ?> ; Tanggal SO : <?php echo $tanggal_so; ?></small>
				</h1>
				<ol class="breadcrumb">
					<li><a href="index.php"><i class="fa fa-dashboard"></i> Home</a></li>
					<li class="active">Data Stok Opnam</li>
				</ol>
			</section>

			<!-- Main content -->
			<section class="content">
				<div class="row">
					<div class="col-xs-12">
						<div class="box box-primary">
							<div class="box-header">
								<i class="fa fa-list"></i>
								<h3 class="box-title">Tambah Data Stok Opnam</h3>
							</div>
							<div class="box-body">
								<div class="row">
									<input type="hidden" name="id_so_search" id="id_so_search" value="<?php echo $id_so; ?>">
									<div class="col-md-6">
										<div class="form-group">
											<label for="">Nama Obat <span style="color:red">*</span></label>
											<select name="nama_obat" id="nama_obat" class="form-control selectobat" required>
												<option value=""></option>
												<?php
												foreach ($data_obat as $do) {
													echo '<option value="' . $do['id_obat'] . '">' . $do['nama'] . ' - ' . $do['sumber_dana'] . '</option>';
												}
												?>
											</select>
										</div>
									</div>
									<div class="col-md-6">
										<div class="form-group">
											<label for="">No Batch <span style="color:red">*</span></label>
											<input type="text" name="no_batch_search" id="no_batch_search" class="form-control" required>
										</div>
									</div>
								</div>
								<?php if ($batch != '') { ?>
									<div class="row">
										<div class="col-md-12">
											<table class="table">
												<thead>
													<tr class="bg-purple">
														<th>CREATED AT</th>
														<th>ID OBAT</th>
														<th>NAMA</th>
														<th>STOK SISTEM</th>
														<th>NO BATCH</th>
														<th>EXPIRED</th>
														<th>HARGA BELI</th>
														<th>AKSI</th>
													</tr>
												</thead>
												<tbody>
													<?php
													foreach ($all_data_obat as $ad) {
														echo '<tr>
																				<td>' . $ad['created_at'] . '</td>
																				<td>' . $ad['id_obat'] . '</td>
																				<td>' . $ad['nama'] . '</td>
																				<td>' . $ad['volume_kartu_akhir'] . '</td>
																				<td>' . $ad['no_batch'] . '</td>
																				<td>' . $ad['expired'] . '</td>
																				<td>' . $ad['harga_beli'] . '</td>
																				<td><button onclick="addObat(this)" id="btn' . $ad['id_kartu_ruangan'] . '" data-kartu="' . $ad['id_kartu_ruangan'] . '" class="btn btn-sm btn-success"><i class="fa fa-check"></i> Pilih</button></td>
																		 </tr>';
													}
													?>
												</tbody>
											</table>
										</div>
									</div>
								<?php } ?>

							</div>
							<div class="box-footer">
								<button id="btnCariData" class="btn btn-primary btn-primary"><i class="fa fa-search"></i> Cari Data</button>
							</div>
						</div>
					</div>
					<div class="col-xs-12">
						<div class="box box-primary">
							<div class="box-header">
								<i class="fa fa-user"></i>
								<h3 class="box-title">Daftar Stok Opnam <?php echo $penyimpanan; ?></h3>
								<div class="pull-right">
									<button id="selesaiBtn" onclick="check_selesai(this)" data-id_so="<?php echo $id_so; ?>" class="btn btn-md btn-success"><i class="fa fa-send"></i> Selesai</button>
								</div>
							</div><!-- /.box-header -->
							<div class="box-body">
								<div class="table-responsive">
									<table class="table" id="example2">
										<thead>
											<tr class="bg-purple">
												<th>#</th>
												<th>ID OBAT</th>
												<th>NAMA OBAT</th>
												<th>SUMBER DANA</th>
												<th>NO BATCH</th>
												<th>EXPIRED</th>
												<th>HARGA BELI</th>
												<th>STOK SISTEM</th>
												<th>MUTASI MASUK</th>
												<th>PENGURANGAN</th>
												<th>REAL STOK</th>
												<th>FISIK</th>
												<th>SELISIH</th>
												<th>ALASAN</th>
												<th>ACTION</th>
											</tr>
										</thead>
									</table>
								</div>
							</div><!-- /.box-body -->
						</div><!-- /.box -->
					</div><!-- /.col -->
				</div><!-- /.row -->
				<!-- modal -->
				<div class="modal fade" id="koresiModal" tabindex="-1" role="dialog" aria-labelledby="" aria-hidden="true">
					<div class="modal-dialog">
						<div class="modal-content">
							<div class="modal-header bg-purple">
								<button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
								<h4 class="modal-title" id="">-</h4>
							</div>
							<div class="modal-body">
								<div class="row">
									<div class="col-md-12">
										<input type="hidden" name="id_det_so" id="id_det_so">
										<div class="form-group">
											<label for="">Nama Obat</label>
											<input type="text" class="form-control" id="nama" readonly>
										</div>
									</div>
									<div class="col-md-4">
										<div class="form-group">
											<label for="">Stok Sistem</label>
											<input type="text" class="form-control" name="stok_sistem" id="stok_sistem" placeholder="Masukan Stok Sistem disini" readonly>
										</div>
									</div>
									<div class="col-md-4">
										<div class="form-group">
											<label for="">Retur/Barang Masuk</label>
											<input type="text" class="form-control" name="retur_masuk" id="retur_masuk" placeholder="Mutasi Masuk" readonly>
										</div>
									</div>
									<div class="col-md-4">
										<div class="form-group">
											<label for="">Pengurangan</label>
											<input type="text" class="form-control" name="pengurangan" id="pengurangan" placeholder="Pengurangan" readonly>
										</div>
									</div>
									<div class="col-md-12">
										<div class="form-group">
											<label for="">Real Stok Saat Ini</label>
											<input type="text" class="form-control" name="sisa_real" id="sisa_real" placeholder="sisa_real" readonly>
										</div>
									</div>
								</div>
								<div class="form-group">
									<label for="">Fisik <span style="color:red">*</span></label>
									<input type="number" class="form-control" name="fisik" id="fisik">
								</div>
								<div class="form-group">
									<label for="">Alasan</label>
									<textarea name="alasan" id="alasan" class="form-control"></textarea>
								</div>
							</div>
							<div class="modal-footer">
								<button type="button" class="btn btn-danger pull-left" data-dismiss="modal"><i class="fa fa-times"></i> Close</button>
								<button id="SimpanBtn" type="button" class="btn btn-success pull-right"><i class="fa fa-save"></i> Simpan</button>
							</div>
						</div>
					</div>
				</div>
				<!-- end modal -->
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
	<script src="../plugins/datatables/jquery.dataTables.min.js" type="text/javascript"></script>
	<script src="../plugins/datatables/dataTables.bootstrap.js" type="text/javascript"></script>
	<!-- SlimScroll -->
	<script src="../plugins/slimScroll/jquery.slimscroll.min.js" type="text/javascript"></script>
	<!-- FastClick -->
	<script src='../plugins/fastclick/fastclick.min.js'></script>
	<!-- select2 -->
	<script src='../plugins/select2/select2.full.min.js'></script>
	<!-- sweetalert -->
	<script src='../plugins/sweetalert/sweetalert.min.js'></script>
	<!-- AdminLTE App -->
	<script src="../dist/js/app.min.js" type="text/javascript"></script>
	<!-- page script -->
	<script type="text/javascript">
		function up(ele) {
			var btn_id = ele.id;
			var id_det_so = $('#' + btn_id).data('id');
			var id_kartu_ruangan = $('#' + btn_id).data('kartu');
			var stok_sistem = $('#' + btn_id).data('stok');
			var fisik = $('#' + btn_id).data('fisik');
			var alasan = $('#' + btn_id).data('alasan');
			var nama = $('#' + btn_id).data('nama');
			$('#koresiModal').modal();
			$('#koresiModal').on('shown.bs.modal', function() {
				var modal = $(this)
				modal.find('.modal-title').text('Koreksi Data SO')
				modal.find('.modal-body #id_det_so').val(id_det_so)
				modal.find('.modal-body #nama').val(nama)
				modal.find('.modal-body #stok_sistem').val(stok_sistem)
				modal.find('.modal-body #fisik').val(fisik)
				modal.find('.modal-body #alasan').val(alasan)
				$('#SimpanBtn').addClass("disabled");
				var fd = new FormData();
				fd.append('id_det_so', id_det_so);
				$.ajax({
					type: 'POST',
					url: 'ajax_data/so_pengeluaran_obat.php',
					data: fd,
					contentType: false,
					cache: false,
					processData: false,
					success: function(msg) {
						var res = JSON.parse(msg);
						modal.find('.modal-body #retur_masuk').val(res.masuk)
						modal.find('.modal-body #pengurangan').val(res.keluar)
						var sisa_real = stok_sistem - parseInt(res.keluar);
						sisa_real = sisa_real + parseInt(res.masuk);
						modal.find('.modal-body #sisa_real').val(sisa_real)
						setTimeout(function() {
							$('#SimpanBtn').removeClass("disabled");
						}, 800);
					},
					error: function(e) {
						console.log(e);
					}
				});

			});
		}

		function check_selesai(ele) {
			var btn_id = ele.id;
			var id_so = $('#' + btn_id).data('id_so');
			var fd = new FormData();
			fd.append('id_so', id_so);
			$.ajax({
				type: 'POST',
				url: 'ajax_data/check_selesai_so.php',
				data: fd,
				contentType: false,
				cache: false,
				processData: false,
				success: function(msg) {
					var response = JSON.parse(msg);
					if (response.total_data > 0) {
						swal(response.title, response.text, response.icon);
						return;
					} else {
						swal({
								title: response.title,
								text: response.text,
								icon: response.icon,
								buttons: true,
								dangerMode: true,
							})
							.then((willProses) => {
								if (willProses) {
									swal("Koreksi SO Berhasil diselesaikan!", {
										icon: "success",
									}).then((lanjut) => {
										window.location.href = "stok_opnam_depo_koreksi_selesai.php?so=" + id_so;
									});
								}
							});
					}
				},
				error: function(e) {
					console.log(e);
				}
			});
		}

		function addObat(ele) {
			var btn_id = ele.id;
			var id_kartu = $('#' + btn_id).data('kartu');
			var id_so = $('#id_so_search').val();
			var fd = new FormData();
			fd.append('id_so', id_so);
			fd.append('id_kartu', id_kartu);
			$.ajax({
				type: 'POST',
				url: 'ajax_data/add_koreksi_so.php',
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
					}).then((lanjut) => {
						window.location.href = "stok_opnam_depo_koreksi_edit.php?koreksi=" + id_so;
					});
				},
				error: function(e) {
					console.log(e);
				}
			});
		}
		var master_so;
		$(function() {
			$('.selectobat').select2({
				placeholder: "Masukan Nama Obat",
				allowClear: true,
			});
			$('#btnCariData').on('click', function() {
				var id = $('#id_so_search').val();
				var nama_obat = $('#nama_obat').val();
				var no_batch_search = $('#no_batch_search').val();
				window.location.href = "stok_opnam_depo_koreksi_edit.php?koreksi=" + id + "&o=" + nama_obat + "&batch=" + no_batch_search;
			});
			master_so = $('#example2').DataTable({
				"processing": true,
				"serverSide": true,
				"ajax": "ajax_data/stok_opnam_depo_koreksi.php?so=<?php echo $id_so; ?>",
				"pageLength": 100,
				"columns": [{
						"data": "id_det_so"
					},
					{
						"data": "id_obat"
					},
					{
						"data": "nama"
					},
					{
						"data": "sumber"
					},
					{
						"data": "no_batch"
					},
					{
						"data": "expired"
					},
					{
						"data": "harga_beli"
					},
					{
						"data": "stok_sistem",
						"render": function(data, type, row) {
							var stok = '<span class="label label-warning" style="font-size:18px;">' + data + '</span>';
							return stok;
						}
					},
					{
						"data": "mutasi_masuk",
						"render": function(data, type, row) {
							var masuk = '<span class="label bg-maroon" style="font-size:18px;">' + data + '</span>';
							return masuk;
						}
					},
					{
						"data": "pengurangan",
						"render": function(data, type, row) {
							var kurang = '<span class="label label-default" style="font-size:18px;">' + data + '</span>';
							return kurang;
						}
					},
					{
						"data": "sisa_real",
						"render": function(data, type, row) {
							var sisa = '<span class="label label-success" style="font-size:18px;">' + data + '</span>';
							return sisa;
						}
					},
					{
						"data": "fisik",
						"render": function(data, type, row) {
							var fisik = '<span class="label label-primary" style="font-size:18px;">' + data + '</span>';
							return fisik;
						}
					},
					{
						"data": "selisih",
						"render": function(data, type, row) {
							var selisih;
							if (data != 0) {
								selisih = '<span class="label label-danger" style="font-size:18px;">' + data + '</span>';
							} else {
								selisih = '<span class="label label-success" style="font-size:18px;">' + data + '</span>';
							}
							return selisih;
						}
					},
					{
						"data": "alasan"
					},
					{
						"data": null,
						"render": function(data, type, row) {
							var id_det = data.id_det_so;
							var btn_id = "btn" + id_det;
							var btn = '<button id="' + btn_id + '" data-kartu="' + data.id_kartu + '" data-nama="' + data.nama + '" data-id="' + data.id_det_so + '" data-stok="' + data.stok_sistem + '" data-fisik="' + data.fisik + '" data-alasan="' + data.alasan + '" class="btn btn-sm btn-warning" onclick="up(this)"><i class="fa fa-gears"></i> UPDATE/KOREKSI</button>';
							return btn;
						}
					},
				],
				"rowCallback": function(row, data, index) {
					if (data.koreksi == "y") {
						$('td', row).css('background-color', '#87dfd6');
					}
				},
				"language": {
					"lengthMenu": "Menampilkan _MENU_ records per halaman",
					"zeroRecords": "Data Tidak ditemukan - sorry",
					"info": "Halaman _PAGE_ dari _PAGES_",
					"infoEmpty": "No records available",
					"infoFiltered": "(filtered from _MAX_ total records)",
					"search": "Pencarian : ",
					"loadingRecords": "Loading...",
					"processing": '<i class="fa fa-spinner fa-spin fa-3x fa-fw"></i><span class="sr-only">Loading...</span> ',
				},
				"order": [
					[0, "asc"]
				]
			});
			$('#SimpanBtn').on("click", function(event) {
				event.preventDefault();
				let id_det_so = $('#id_det_so').val();
				let stok_sistem = $('#stok_sistem').val();
				let retur_masuk = $('#retur_masuk').val();
				let pengurangan = $('#pengurangan').val();
				let sisa_real = $('#sisa_real').val();
				let fisik = $('#fisik').val();
				let alasan = $('#alasan').val();
				if (typeof parseInt(fisik) != 'number') {
					swal({
						title: "Peringatan!",
						text: "Field Fisik harus angka",
						icon: "warning",
					});
				} else {
					var fd = new FormData();
					fd.append('id_det_so', id_det_so);
					fd.append('stok_sistem', stok_sistem);
					fd.append('retur_masuk', retur_masuk);
					fd.append('pengurangan', pengurangan);
					fd.append('sisa_real', sisa_real);
					fd.append('fisik', fisik);
					fd.append('alasan', alasan);
					$.ajax({
						type: 'POST',
						url: 'ajax_data/koreksi_so.php',
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
								$('#koresiModal').modal('hide');
								$('#example2').DataTable().ajax.reload();
							})
						},
						error: function(e) {
							console.log(e);
						}
					});
				}
			})
		});
	</script>

</body>

</html>