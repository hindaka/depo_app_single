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
$id_resep = isset($_GET["id"]) ? $_GET['id'] : '';
$tipe_depo = $tipes[2];
$conf = json_decode(file_get_contents("config/env_depo.json"), true);
$id_depo = $conf[$tipe_depo]["id_depo"];
$hariini = date("d/m/Y");
//tampilkan data obat keluar
$h3 = $db->query("SELECT wo.*,g.nama FROM `warehouse_out` wo INNER JOIN warehouse_stok ws ON(ws.id_warehouse_stok=wo.id_warehouse_stok) INNER JOIN gobat g ON(g.id_obat=ws.id_obat) WHERE id_resep='$id_resep'");
$data3 = $h3->fetchAll(PDO::FETCH_ASSOC);
//get tanggal
$h4 = $db->query("SELECT * FROM resep WHERE id_resep='$id_resep'");
$data4 = $h4->fetch(PDO::FETCH_ASSOC);
$tgl_resep = $data4["tgl_resep"];
$nomedrek = $data4["nomedrek"];
$nama = $data4["nama"];
//get list obat
$list_obat = $db->query("SELECT ks.*,g.nama,g.flag_single_id FROM kartu_stok_ruangan ks INNER JOIN gobat g ON(ks.id_obat=g.id_obat) WHERE ks.in_out='masuk' AND ks.volume_kartu_akhir>0 AND ks.id_warehouse='" . $id_depo . "'");
$obat = $list_obat->fetchAll(PDO::FETCH_ASSOC);
$list_rtt = $db->query("SELECT ws.id_warehouse_stok,ws.id_warehouse,ws.id_obat,ws.stok,g.nama,g.sumber FROM warehouse_stok ws INNER JOIN warehouse w ON(w.id_warehouse=ws.id_warehouse) INNER JOIN gobat g ON(g.id_obat=ws.id_obat) WHERE w.id_warehouse='" . $id_depo . "' ORDER BY g.nama ASC");
$obat_rtt = $list_rtt->fetchAll(PDO::FETCH_ASSOC);
function pembulatan($total)
{
	$ratusan = substr($total, -3);
	if ($ratusan < 450) {
		$total_harga = $total - $ratusan;
	} else if (($ratusan >= 450) && ($ratusan < 950)) {
		$total_harga = ($total - $ratusan) + 500;
	} else {
		$total_harga = $total + (1000 - $ratusan);
	}
	return $total_harga;
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
	<!-- daterange picker -->
	<link href="../plugins/datepicker/datepicker3.css" rel="stylesheet" type="text/css" />
	<!-- BootsrapSelect -->
	<link href="../plugins/bootstrap-select/bootstrap-select.min.css" rel="stylesheet" type="text/css" />
	<link rel="stylesheet" href="../plugins/select2/select2.min.css">
	<!-- iCheck for checkboxes and radio inputs -->
	<link href="../plugins/iCheck/all.css" rel="stylesheet" type="text/css" />
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
						<h4><i class="icon fa fa-check"></i>Berhasil</h4>Data obat telah diinput
					</center>
				</div>
			<?php } else if (isset($_GET['status']) && ($_GET['status'] == "2")) { ?><div class="alert alert-danger alert-dismissable"><button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>
					<center>
						<h4><i class="icon fa fa-ban"></i>Peringatan!</h4>Data obat tidak ditemukan
					</center>
				</div>
			<?php } else if (isset($_GET['status']) && ($_GET['status'] == "3")) { ?><div class="alert alert-danger alert-dismissable"><button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>
					<center>
						<h4><i class="icon fa fa-ban"></i>Peringatan!</h4>Stok obat tidak mencukupi / volume obat tidak boleh kosong!
					</center>
				</div>
			<?php } ?>
			<!-- end pesan -->
			<section class="content-header">
				<h1>
					Transaksi
					<small>obat keluar</small>
				</h1>
				<ol class="breadcrumb">
					<li><a href="index.php"><i class="fa fa-dashboard"></i> Home</a></li>
					<li>Transaksi</li>
					<li class="active">Obat Keluar</li>
				</ol>
			</section>

			<!-- Main content -->
			<section class="content">
				<div class="row">
					<div class="col-md-12">
						<div class="box box-success collapsed-box">
							<div class="box-header with-border">
								<h3 class="box-title">Data RTT</h3>

								<div class="box-tools pull-right">
									<button class="btn btn-sm btn-primary" data-toggle="modal" data-target="#myModal"><i class="fa fa-plus"></i> Tambah Data RTT</button>
									<button type="button" class="btn btn-box-tool" data-widget="collapse"><i class="fa fa-plus"></i>
									</button>
								</div>
								<!-- /.box-tools -->
							</div>
							<!-- /.box-header -->
							<div class="box-body">
								<div class="table-responsive">
									<table id="tableRTT" class="table table-striped" style="width:100%">
										<thead>
											<tr class="bg-blue">
												<th>#</th>
												<th>Nama Obat</th>
												<th>Jenis Obat</th>
												<th>Keterangan</th>
												<th>Aksi</th>
											</tr>
										</thead>
									</table>
								</div>
							</div>
							<!-- /.box-body -->
						</div>
						<!-- Modal -->
						<div class="modal fade" id="myModal" role="dialog">
							<div class="modal-dialog">

								<!-- Modal content-->
								<div class="modal-content">
									<div class="modal-header bg-blue">
										<button type="button" class="close" data-dismiss="modal">&times;</button>
										<h4 class="modal-title">Tambah Data Resep RTT</h4>
									</div>
									<form action="">
										<div class="modal-body">
											<input type="hidden" name="id_resep" id="id_resep" value="<?php echo $id_resep; ?>">
											<div class="form-group">
												<label for="">Formularium RS? <span style="color:red">*</span></label>
												<select name="formularium_rs" id="fr" class="form-control" required>
													<option value="">--- Pilih Salah Satu ---</option>
													<option value="ya">Ya</option>
													<option value="tidak">Tidak</option>
												</select>
											</div>
											<div class="form-group" id="inside_rs">
												<label for="">Nama Obat <span style="color:red">*</span></label>
												<select name="obatrtt" id="obatrtt" class="form-control select2" style="width:100%">
													<option value=""></option>
													<?php
													foreach ($obat_rtt as $o) {
														echo "<option value='" . $o['id_obat'] . "'>" . $o['nama'] . "</option>";
													}
													?>
												</select>
											</div>
											<div class="form-group" id="outside_rs">
												<label for="">Nama Obat <span style="color:red">*</span></label>
												<input type="text" class="form-control" name="obatrtt" id="obatrtt_outside">
											</div>
											<div class="row">
												<div class="col-xs-12 col-sm-12 col-md-4">
													<div class="form-group">
														<label for="">Jumlah <span style="color:red">*</span></label>
														<input type="number" class="form-control" id="jumlah" name="jumlah" min="1" autocomplete="off">
													</div>
												</div>
												<div class="col-xs-12 col-sm-12 col-md-4">
													<div class="form-group">
														<label for="">Satuan <span style="color:red">*</span></label>
														<input type="text" class="form-control" id="satuan" name="satuan" autocomplete="off">
													</div>
												</div>
												<div class="col-xs-12 col-sm-12 col-md-4">
													<div class="form-group">
														<label for="">harga <span style="color:red">*</span></label>
														<input type="number" class="form-control" id="harga" name="harga" min="0" autocomplete="off">
													</div>
												</div>
											</div>
											<div class="form-group">
												<label for="">Jenis Obat <span style="color:red">*</span></label><br>
												<input type="radio" name="jenis_obat" id="jenis_obat1" data-value="Non Generik"> Non Generik&nbsp;&nbsp;&nbsp;
												<input type="radio" name="jenis_obat" id="jenis_obat2" data-value="Generik"> Generik&nbsp;&nbsp;&nbsp;
											</div>
											<div class="form-group">
												<label for="">Keterangan <span style="color:red">*</span></label><br>
												<input type="radio" name="keterangan" id="keterangan1" data-value="Non-Formularium RS"> Non Formularium RS&nbsp;&nbsp;&nbsp;
												<input type="radio" name="keterangan" id="keterangan2" data-value="Obat dibeli diluar"> Obat dibeli diluar&nbsp;&nbsp;&nbsp;
												<input type="radio" name="keterangan" id="keterangan3" data-value="Obat Kosong"> Obat Kosong&nbsp;&nbsp;&nbsp;
											</div>
										</div>
										<div class="modal-footer">
											<button id="rttBtn" type="button" class="btn btn-primary"><i class="fa fa-save"></i> Simpan</button>
											<button type="button" class="btn btn-danger" data-dismiss="modal"><i class="fa fa-times"></i> Close</button>
										</div>
									</form>
								</div>

							</div>
						</div>
					</div>
				</div>
				<div class="row">
					<!-- left column -->
					<div class="col-md-5">
						<div class="box box-primary">
							<div class="box-header">
								<i class="fa fa-medkit"></i>
								<h3 class="box-title">Data Pasien & Obat</h3>
							</div><!-- /.box-header -->
							<!-- form start -->
							<form role="form" action="keluareditacc.php?id=<?php echo $id_resep; ?>" method="post">
								<div class="box-body">
									<div class="form-group">
										<label for="nama">No. Transaksi Resep: <b><?php echo $id_resep; ?></b></label>
									</div>
									<div class="form-group">
										<label for="nama">Nama Pasien</label>
										<input type="text" class="form-control" id="nama" name="nama" value="<?php echo $nama; ?>" required disabled>
									</div>
									<div class="form-group">
										<label for="tglkeluar">Tanggal Resep</label>
										<input type="text" class="form-control" id="tglkeluar" name="tglkeluar" value="<?php echo $tgl_resep; ?>" required disabled>
									</div>
									<div class="form-group">
										<label for="namaobat">Nama Obat</label>
										<!-- <input type="text" class="form-control" id="namaobat" name="namaobat" placeholder="Nama Obat" autocomplete="off" required> -->
										<select class="form-control selectpicker" data-live-search="true" name="namaobat" id="namaobat" required>
											<option value="">---Pilih Obat---</option>
											<?php
											foreach ($obat as $o) {
												if ($o['flag_single_id'] == 'new') {
													if ($o['jenis'] == 'generik') {
														$text_nama = "(Single ID) " . $o['nama'] . "(" . $o['pabrikan'] . ")" . $o['volume_kartu_akhir'] . " | " . $o['no_batch'];
													} else if ($o['jenis'] == 'non generik') {
														$text_nama = "(Single ID) " . $o['nama'] . "(" . $o['merk'] . ")" . $o['volume_kartu_akhir'] . " | " . $o['no_batch'];
													} else {
														$text_nama = "(Single ID) " . $o['nama'] . " | " . $o['volume_kartu_akhir'] . " | " . $o['no_batch'];
													}
												} else {
													$text_nama = $o['nama'] . " (" . $o['volume_kartu_akhir'] . ")";
												}
												echo "<option value='" . $o['id_kartu_ruangan'] . "|" . $o['id_obat'] . "|" . $o['volume_kartu_akhir'] . "|" . $o['id_warehouse'] . "'>" . $text_nama . "</option>";
											}
											?>
										</select>
									</div>
									<div class="form-group">
										<label for="volume">Volume</label>
										<input type="text" class="form-control" id="volume" name="volume" placeholder="Volume" autocomplete="off" required>
										<input type="hidden" class="form-control" id="tgglkeluar" name="tgglkeluar" value="<?php echo $tgl_resep; ?>">
									</div>
									<div class="form-group">
										<label for="tuslah">Tuslah</label><br />
										<input type="radio" name="tuslah" class="flat-blue" value="1" required>
										Rajal&nbsp;&nbsp;&nbsp;
										<input type="radio" name="tuslah" class="flat-blue" value="2" required>
										Rajal racik&nbsp;&nbsp;&nbsp;
										<input type="radio" name="tuslah" class="flat-blue" value="3" required>
										Ranap&nbsp;&nbsp;&nbsp;
										<input type="radio" name="tuslah" class="flat-blue" value="4" required>
										Ranap racik&nbsp;&nbsp;&nbsp;
										<input type="radio" name="tuslah" class="flat-blue" value="5" required>
										Non tuslah
										</label>
									</div>
									<div class="form-group">
										<label for="tambah">&nbsp;</label>
										<button type="submit" class="btn btn-primary">Tambah Obat Keluar</button>
									</div>
								</div><!-- /.box-body -->

						</div>
					</div><!-- /.left column -->
					<!-- right column -->
					<div class="col-md-7">
						<div class="box box-primary">
							<div class="box-header">
								<i class="fa fa-medkit"></i>
								<h3 class="box-title">Data Obat Keluar</h3>
							</div><!-- /.box-header -->
							<!-- form start -->
							<div class="box-body">
								<div class="form-group">
									<table class="table table-striped">
										<thead>
											<tr class="bg-blue">
												<th>No.</th>
												<th>Nama</th>
												<th>Volume</th>
												<th>Harga Satuan</th>
												<th>Total + tuslah</th>
												<th>Hapus</th>
											</tr>
										</thead>
										<tbody>
											<?php
											$nomer = 1;
											$subtot = 0;
											foreach ($data3 as $r3) {
												echo "<tr>
																<td>" . $nomer++ . "</td>
																<td>" . $r3['nama'] . "</td>
																<td>" . $r3['volume'] . "</td>
																<td>" . $r3['harga_satuan'] . "</td>
																<td>" . $r3['total_harga'] . "</td>
																<td>
																	<a class='btn btn-sm btn-danger' href='hapus_edit.php?id=" . $r3['id_warehouse_out'] . "&kartu=" . $r3['id_kartu_ruangan'] . "&resep=" . $id_resep . "'><i class='fa fa-trash'></i> Hapus</a>
																</td>
															</tr>";
												$subtot += $r3['total_harga'];
											} ?>
										</tbody>
										<tfoot>
											<tr class="info">
												<td colspan="4" class="text-right"><b>Total Transaksi</b></td>
												<td colspan="2"><b>Rp.<?php echo number_format($subtot, 4, ',', '.'); ?></b></td>
											</tr>
											<tr class="warning">
												<td colspan="4" class="text-right"><b>Pembulatan</b></td>
												<td colspan="2"><b>Rp.(<?php echo number_format(pembulatan($subtot) - $subtot, 4, ',', '.'); ?>)</b></td>
											</tr>
											<tr class="success">
												<td colspan="4" class="text-right"><b>Total yang harus dibayarkan</b></td>
												<td colspan="2"><b>Rp.<?php echo number_format(pembulatan($subtot), 4, ',', '.'); ?></b></td>
											</tr>
										</tfoot>
									</table>
								</div>
							</div><!-- /.box-body -->
							<div class="box-footer">
								<!-- <a class="btn btn-app" href="resepkasir_edit.php?id=<?php echo $id_resep; ?>&total=<?php echo $subtot; ?>"><i class="fa fa-save"></i>Simpan</a> -->
								<a class="btn btn-app bg-green" href="resepkasir_edit.php?id=<?php echo $id_resep; ?>"><i class="fa fa-save"></i>Simpan</a>
								<?php
								if ($subtot == 0) {
									echo "<a class=\"btn btn-app bg-red\" href=\"batal.php?resep=$id_resep\"><i class=\"fa fa-trash\"></i>Batal</a>";
								} else {
									echo "";
								}
								?>
							</div>
						</div>
					</div><!-- /.right column -->
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
	<!-- DATA TABES SCRIPT -->
	<script src="../plugins/datatables/jquery.dataTables.min.js" type="text/javascript"></script>
	<script src="../plugins/datatables/dataTables.bootstrap.js" type="text/javascript"></script>
	<!-- SlimScroll -->
	<script src="../plugins/slimScroll/jquery.slimscroll.min.js" type="text/javascript"></script>
	<!-- date-picker -->
	<script src="../plugins/datepicker/bootstrap-datepicker.js" type="text/javascript"></script>
	<!-- BootsrapSelect -->
	<script src="../plugins/bootstrap-select/bootstrap-select.min.js" type="text/javascript"></script>
	<script src="../plugins/select2/select2.full.min.js" type="text/javascript"></script>
	<!-- typeahead -->
	<script src="../plugins/typeahead/typeahead.bundle.js" type="text/javascript"></script>
	<!-- iCheck 1.0.1 -->
	<script src="../plugins/iCheck/icheck.min.js" type="text/javascript"></script>
	<!-- FastClick -->
	<script src='../plugins/fastclick/fastclick.min.js'></script>
	<!-- AdminLTE App -->
	<script src="../dist/js/app.min.js" type="text/javascript"></script>
	<script src="../plugins/sweetalert/sweetalert.min.js" type="text/javascript"></script>
	<!-- page script -->
	<script type="text/javascript">
		//Flat red color scheme for iCheck
		$('input[type="radio"].flat-blue').iCheck({
			radioClass: 'iradio_flat-blue'
		});
		var master_resep;
		var inside;
		var outside;
		$(function() {
			inside = $('#inside_rs');
			outside = $('#outside_rs')
			inside.hide();
			outside.hide();
			$('.modal').on('hidden.bs.modal', function() {
				$(this).find('form')[0].reset();
				$("#obatrtt").val("").trigger('change');
				$("#obatrtt_outside").val("");
				inside.hide();
				outside.hide();
			});
			master_resep = $('#tableRTT').DataTable({
				"processing": true,
				"serverSide": true,
				"ajax": "ajax_data/data_rtt.php?r=<?php echo $id_resep; ?>",
				"columns": [{
						"data": "id_rtt"
					},
					{
						"data": "nama_obat"
					},
					{
						"data": "jenis_obat",
						"render": function(data, type, row, meta) {
							var jenis;
							if (data == 'Non Generik') {
								jenis = '<span class="label label-primary">Non Generik</span>';
							} else if (data == 'Generik') {
								jenis = '<span class="label label-success">Generik</span>';
							} else {
								jenis = '<span class="label label-default">UNKNOWN</span>';
							}
							return jenis;
						}
					},
					{
						"data": "ket"
					},
					{
						"data": "id_rtt",
						"render": function(data, type, row, meta) {
							var btn = '<button class="btn btn-sm btn-danger" onclick="hapus_perubahan(' + data + ')"><i class="fa fa-trash"></i> Hapus</button>';
							return btn;
						}
					},
				],
				"order": [
					[1, "asc"]
				]
			});
			$('#rttBtn').on("click", function(event) {
				event.preventDefault();
				var fr = $('#fr').val();
				var id_resep = $('#id_resep').val();
				var keterangan = $("input[name='keterangan']:checked").data("value");
				var jenis_obat = $("input[name='jenis_obat']:checked").data("value");
				if (fr == 'ya') {
					var obatrtt = $('#obatrtt').val();
				} else if (fr == 'tidak') {
					var obatrtt = $('#obatrtt_outside').val();
				} else {
					var obatrtt = $('#obatrtt').val();
				}
				if (fr == "") {
					swal({
						title: "Peringatan!",
						text: "Field Formularium RS belum dipilih!",
						icon: "warning",
						button: "Tutup",
					});
				} else if (obatrtt == "") {
					swal({
						title: "Peringatan!",
						text: "Nama Obat RTT Belum diisi / dipilih!",
						icon: "warning",
						button: "Tutup",
					});
				} else if (typeof jenis_obat == "undefined") {
					swal({
						title: "Peringatan!",
						text: "Jenis Obat Belum diisi!",
						icon: "warning",
						button: "Tutup",
					});
				} else if (typeof keterangan == "undefined") {
					swal({
						title: "Peringatan!",
						text: "Field Keterangan Belum diisi!",
						icon: "warning",
						button: "Tutup",
					});
				} else {
					// post ajax
					var fd = new FormData();
					fd.append('task', "add");
					fd.append('id', id_resep);
					fd.append('obatrtt', obatrtt);
					fd.append('ket', keterangan);
					fd.append('jenis_obat', jenis_obat);
					$.ajax({
						type: "POST",
						url: "ajax_data/add_rtt.php",
						data: fd,
						contentType: false,
						cache: false,
						processData: false,
						success: function(respon) {
							console.log(respon);
							swal({
								title: "Berhasil!",
								text: "Data RTT Berhasil ditambahkan",
								icon: "success",
								button: "OK!",
							}).then((value) => {
								$('#myModal').modal('hide');
								master_resep.ajax.reload();
							});
						},
						error: function(e) {
							alert(e);
							// console.log("ERROR : ", e.responseText);
							master_resep.ajax.reload();
						}
					});
				}
			});
			$('#fr').on("change", function(e) {
				e.preventDefault();
				var fr = $('#fr').val();
				if (fr == 'ya') {
					inside.show();
					outside.hide();
					$("#jenis_obat1").prop("checked", false); //Non Generik
					$("#jenis_obat2").prop("checked", false); //Generik
					$("#keterangan1").prop("checked", false);
					$("#keterangan2").prop("checked", false);
					$("#keterangan3").prop("checked", true);
				} else if (fr == 'tidak') {
					outside.show();
					inside.hide();
					$("#jenis_obat1").prop("checked", true); //Non Generik
					$("#jenis_obat2").prop("checked", false); //Generik
					$("#keterangan1").prop("checked", true);
					$("#keterangan2").prop("checked", false);
					$("#keterangan3").prop("checked", false);
				} else {
					inside.hide();
					outside.hide();
					$("#jenis_obat1").prop("checked", false); //Non Generik
					$("#jenis_obat2").prop("checked", false); //Generik
					$("#keterangan1").prop("checked", false);
					$("#keterangan2").prop("checked", false);
					$("#keterangan3").prop("checked", false);
				}
			});
			$('#obatrtt').on('change', function(e) {
				e.preventDefault();
				obatrtt = this.value;
				if (obatrtt != '') {
					var fd = new FormData();
					fd.append('task', "change");
					fd.append('obatrtt', obatrtt);
					$.ajax({
						type: "POST",
						url: "ajax_data/add_rtt.php",
						data: fd,
						contentType: false,
						cache: false,
						processData: false,
						success: function(respon) {
							console.log(respon);
							if (respon == 'Non Generik') {
								$("#jenis_obat1").prop("checked", true); //Non Generik
							} else if (respon == 'Generik') {
								$("#jenis_obat2").prop("checked", true); //Generik
							} else {
								$("#obatrtt").val("").trigger('change');
								$("#jenis_obat1").prop("checked", false); //Non Generik
								$("#jenis_obat2").prop("checked", false); //Generik
								swal({
									title: "Peringatan!",
									text: "Hanya dapat dipilih obat Non Generik & Generik Saja",
									icon: "warning",
									button: "Tutup",
								});
							}
						},
						error: function(e) {
							// alert(e);
							console.log("ERROR : ", e.responseText);
						}
					});
				}
			});
		});
		$('.select2').select2({
			placeholder: "Pilih Nama Obat",
			allowClear: true,
			width: 'resolve'
		});

		function hapus_perubahan(id) {
			// post ajax
			var fd = new FormData();
			fd.append('task', "delete");
			fd.append('id_rtt', id);
			$.ajax({
				type: "POST",
				url: "ajax_data/add_rtt.php",
				data: fd,
				contentType: false,
				cache: false,
				processData: false,
				success: function(respon) {
					console.log(respon);
					swal({
						title: "Berhasil!",
						text: "Data RTT Berhasil dihapus",
						icon: "success",
						button: "OK!",
					}).then((value) => {
						$('#myModal').modal('hide');
						master_resep.ajax.reload();
					});
				},
				error: function(e) {
					alert(e);
					master_resep.ajax.reload();
				}
			});
		}
	</script>

</body>

</html>