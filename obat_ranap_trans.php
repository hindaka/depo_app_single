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
$id_rincian = isset($_GET['id']) ? $_GET['id'] : '';
$f_today = date("Y-m-d");
$tipe_depo = $tipes[2];
$conf = json_decode(file_get_contents("config/env_depo.json"), true);
$id_depo = $conf[$tipe_depo]["id_depo"];
$nama_depo = $conf[$tipe_depo]["nama_depo"];
$get_peg = $db->query("SELECT * FROM pegawai WHERE id_depart='3' OR id_depart='4'");
$pegawai = $get_peg->fetchAll(PDO::FETCH_ASSOC);
//get data pasien
$sql_pasien = "SELECT rp.id_pasien,rp.nama,rp.nomedrek,rp.jpasien,ro.dpjp FROM registerpasien rp INNER JOIN rincian_obat_pasien ro ON(rp.id_pasien=ro.id_pasien) WHERE ro.id_rincian_obat=" . $id_rincian . " LIMIT 1";
$get_pasien = $db->query($sql_pasien);
$pasien = $get_pasien->fetch(PDO::FETCH_ASSOC);
$id_register = $pasien['id_pasien'];
//get farmasi_pelayanan (count data)
$get_inv = $db->query("SELECT id_invoice_all FROM invoice_all WHERE id_register='" . $id_register . "' AND status_inv='belum dibayar' AND jenis_rawat='ranap' LIMIT 1");
$inv = $get_inv->fetch(PDO::FETCH_ASSOC);
$total_inv = $get_inv->rowCount();

$get_pel = $db->query("SELECT COUNT(*) as total_pel FROM farmasi_pelayanan WHERE id_register='" . $id_register . "'");
$pel = $get_pel->fetch(PDO::FETCH_ASSOC);
//get transaksi
$sql_trans = "SELECT rt.*,w.nama_ruang FROM rincian_transaksi_obat rt LEFT JOIN warehouse w ON(w.id_warehouse=rt.id_warehouse) WHERE id_rincian_obat=" . $id_rincian . " ORDER BY rt.created_at ASC";
$trans = $db->query($sql_trans);
$total_item = $trans->rowCount();
//get retur barang
$retur_item = $db->query("SELECT * FROM parent_retur_obat WHERE id_rincian_obat='" . $id_rincian . "'");
$retur_barang = $retur_item->fetchAll(PDO::FETCH_ASSOC);
//list retur ruangan
$list_retur = $db->query("SELECT DISTINCT(rt.ruang) as ruangan FROM `rincian_obat_pasien` ro INNER JOIN rincian_transaksi_obat rt ON(ro.id_rincian_obat=rt.id_rincian_obat) WHERE ro.id_rincian_obat='" . $id_rincian . "' AND rt.id_warehouse='" . $id_depo . "'");
$lokasi_retur = $list_retur->fetchAll(PDO::FETCH_ASSOC);
//get total_tpn
$get_total_tpn = $db->query("SELECT COUNT(*) as total_rec FROM apotek_tpn_list WHERE id_register='" . $id_register . "'");
$total_tpn = $get_total_tpn->fetch(PDO::FETCH_ASSOC);
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
	<!-- Ionicons -->
	<link href="../plugins/ionicons/2.0.0/ionicon.min.css" rel="stylesheet" type="text/css" />
	<!-- select2 -->
	<link href="../plugins/select2/select2.min.css" rel="stylesheet" type="text/css" />
	<!-- notification -->
	<link href="../plugins/pnotify/css/animate.css" rel="stylesheet" type="text/css" />
	<link href="../plugins/pnotify/css/pnotify.custom.min.css" rel="stylesheet" type="text/css" />
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
						<h4><i class="icon fa fa-ban"></i>Peringatan!</h4>Stok obat tidak mencukupi
					</center>
				</div>
			<?php } else if (isset($_GET['status']) && ($_GET['status'] == "4")) { ?><div class="alert alert-danger alert-dismissable"><button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>
					<center>
						<h4><i class="icon fa fa-ban"></i>Peringatan!</h4>Data Gagal diinput ! Silakan hubungi divisi IT.
					</center>
				</div>
			<?php } else if (isset($_GET['status']) && ($_GET['status'] == "5")) { ?><div class="alert alert-success alert-dismissable"><button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>
					<center>
						<h4><i class="icon fa fa-check"></i>Berhasil!</h4>Data Transaksi berhasil dihapus.
					</center>
				</div>
			<?php } else if (isset($_GET['status']) && ($_GET['status'] == "6")) { ?><div class="alert alert-danger alert-dismissable"><button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>
					<center>
						<h4><i class="icon fa fa-ban"></i>Peringatan!</h4>Gagal melakukan penghapusan obat dari rincian ! Silakan hubungi divisi IT.
					</center>
				</div>
			<?php } else if (isset($_GET['status']) && ($_GET['status'] == "7")) { ?><div class="alert alert-success alert-dismissable"><button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>
					<center>
						<h4><i class="icon fa fa-check"></i>Berhasil!</h4>Data Transaksi Retur Berhasil dihapus
					</center>
				</div>
			<?php } ?>
			<!-- end pesan -->
			<section class="content-header">
				<h1>
					Data Transaksi Obat
					<small><?php echo $pasien['nama']; ?></small>
				</h1>
				<ol class="breadcrumb">
					<li><a href="index.php"><i class="fa fa-dashboard"></i> Home</a></li>
					<li>Data Transaksi Obat</li>
					<li class="active"><?php echo $pasien['nama']; ?></li>
				</ol>
			</section>

			<!-- Main content -->
			<section class="content">
				<div class="row">
					<!-- left column -->
					<div class="col-md-12">
						<div class="box box-primary">
							<div class="box-header">
								<i class="fa fa-medkit"></i>
								<h3 class="box-title">Data Identitas Pasien</h3>
							</div><!-- /.box-header -->
							<!-- form start -->
							<form class="form-inline" role="form" action="obat_trans_check.php" method="get">
								<div class="box-body">
									<div class="row">
										<div class="col-md-12">
											<div class="table-responsive">
												<table class="table table-bordered table-responsive table-striped">
													<tr>
														<th class="info">No. Rekam Medis</th>
														<th><?php echo $pasien['nomedrek']; ?></th>
													</tr>
													<tr>
														<th class="info">Nama Pasien</th>
														<th><?php echo $pasien['nama']; ?></th>
													</tr>
													<tr>
														<th class="info">DPJP</th>
														<th><?php echo $pasien['dpjp']; ?></th>
													</tr>
													<tr>
														<th class="info">Cara Bayar</th>
														<th><?php echo strtoupper($pasien['jpasien']); ?></th>
													</tr>
													<tr>
														<th class="info">Ruangan</th>
														<th>
															<select class="form-control" name="ruang" required>
																<?php
																if ($nama_depo == 'Depo Farmasi IGD') {
																	echo '<option value="">Pilih Ruangan</option>
															<option value="IGD">IGD</option>';
																} else if ($nama_depo = 'Depo Farmasi OK') {
																	echo '<option value="">Pilih Ruangan</option>
															<option value="OK">OK</option>';
																} else {
																}
																?>
																<!-- <option value="ICU">ICU (LANTAI 4)</option>
													<option value="NICU">NICU (LANTAI 4)</option>
													<option value="OK">OK (LANTAI 5)</option>
													<option value="VK">VK (LANTAI 7)</option>
													<option value="PERINATOLOGI">PERINATOLOGI (LANTAI 7)</option>
													<option value="LANTAI 8">LANTAI 8</option>
													<option value="RUANG ANAK">RUANG ANAK (LANTAI 9)</option>
													<option value="NIFAS LT4">NIFAS LT 4 (LANTAI 10)</option>
													<option value="LANTAI 11">IPD & BEDAH (LANTAI 11)</option> -->
																<!-- <option value="LANTAI 12">VVIP (LANTAI 12)</option> -->
															</select>

														</th>
													</tr>
													<tr>
														<th class="info">Obat Khusus</th>
														<th>
															<select class="form-control" name="obat_khusus" required>
																<option value="">---Pilih Obat Khusus---</option>
																<option value="-">Obat Umum</option>
																<option value="Narkotik">Narkotika</option>
																<option value="Psikotropika">Psikotropika</option>
																<option value="OOT">OOT</option>
																<option value="Precusor">Precusor</option>
																<option value="Sitostatika">Sitostatika</option>
															</select>
														</th>
													</tr>
												</table>
											</div>
										</div>
									</div>
								</div><!-- /.box-body -->
								<div class="box-footer">
									<div class="form-group">
										<label for="tambah">&nbsp;</label>
										<input type="hidden" name="tambah" value="ok">
										<input type="hidden" name="nomedrek" value="<?php echo $pasien['nomedrek']; ?>" disabled>
										<input type="hidden" name="nama" value="<?php echo $pasien['nama']; ?>" disabled>
										<input type="hidden" name="dpjp" value="<?php echo $pasien['dpjp']; ?>" disabled>
										<input type="hidden" name="cara_bayar" value="<?php echo $pasien['jpasien']; ?>" disabled>
										<input type="hidden" name="id" value="<?php echo $id_rincian; ?>">
										<input type="hidden" name="task" value="new">
										<button type="submit" class="btn btn-primary">Tambah Transaksi <i class="fa fa-send"></i></button>
										<?php if ($total_item == 0) : ?>
											<a href="obat_ranap_batal.php?id=<?php echo $id_rincian; ?>" onclick="return my_alert_batal('batal_ranap.php',<?php echo $id_rincian; ?>)" class="btn btn-danger">Batalkan Transaksi <i class="fa fa-cut"></i></a>
										<?php endif; ?>
										<a href="obat_ranap.php" onclick="return my_alert('obat_ranap.php')" class="btn btn-info">Kembali <i class="fa fa-undo"></i></a>
									</div>
								</div>
							</form>
						</div>
					</div>
				</div>
				<div class="row">
					<div class="col-xs-12 col-md-12">
						<div class="box box-info box-solid">
							<div class="box-header with-border">
								<i class="fa fa-book"></i>
								<h3 class="box-title">Data Pelayanan Farmasi</h3>
								<div class="box-tools pull-right">
									<button type="button" class="btn btn-box-tool" data-widget="collapse">
										<i class="fa fa-minus"></i>
									</button>
								</div>
							</div>
							<div class="box-body">
								<?php if ($total_inv > 0) {
									if ($pel['total_pel'] > 0) {
										$text = '<strong>Ada <span class="label label-info" style="font-size:14px">' . $pel['total_pel'] . '</span> Pelayanan Farmasi yang sudah terdaftar.</strong>';
									} else {
										$text = '<strong>Belum Ada Data Pelayanan Farmasi yang terdaftar.</strong>';
									}
									echo $text; ?>
									<a target="_blank" href="tindakan_pasien_log.php?inv=<?php echo $inv['id_invoice_all']; ?>&reg=<?php echo $id_register; ?>" class="btn btn-xs btn-primary"><i class="fa fa-search"></i> Lihat / Tambah</a>
								<?php	} else {
									echo $id_register; ?>
									Data Invoice Pelayanan Farmasi tidak ditemukan. Silahkan Hubungi IT untuk dilakukan Pengecekan.
								<?php	} ?>
							</div>
						</div>
					</div>
					<!-- <div class="col-xs-12 col-md-6">
						<div class="box box-warning box-solid">
							<div class="box-header with-border">
								<i class="fa fa-book"></i>
								<h3 class="box-title">Data TPN</h3>
								<div class="box-tools pull-right">
									<button type="button" class="btn btn-box-tool" data-widget="collapse">
										<i class="fa fa-minus"></i>
									</button>
								</div>
							</div>
							<div class="box-body">
								<?php
								if ($total_tpn['total_rec'] > 0) {
									$text = '<strong>Ada <span class="label label-default" style="font-size:14px">' . $total_tpn['total_rec'] . '</span> DATA TPN yang sudah terdaftar.</strong>';
								} else {
									$text = '<strong>Belum Ada Data TPN yang terdaftar.</strong>';
								}
								echo $text; ?>
								<a href="tpn_list.php?rincian=<?php echo $id_rincian; ?>&reg=<?php echo $id_register; ?>" class="btn btn-xs btn-primary"><i class="fa fa-search"></i> Lihat / Tambah</a>
							</div>
						</div>
					</div> -->
				</div>
				<div class="row">
					<!-- right column -->
					<div class="col-md-6">
						<div class="box box-primary">
							<div class="box-header">
								<i class="fa fa-medkit"></i>
								<h3 class="box-title">Data Transaksi Obat Pasien</h3>
							</div><!-- /.box-header -->
							<!-- form start -->
							<div class="box-body">
								<div class="table-responsive">
									<table class="table table-striped">
										<thead>
											<tr class="info">
												<th>No.</th>
												<th>#</th>
												<th>Tanggal Transaksi</th>
												<th>Ruang</th>
												<th>Asal Input</th>
												<th>Sub Total</th>
												<th>Aksi</th>
											</tr>
										</thead>
										<tbody>
											<?php
											$no = 1;
											$total_biaya = 0;
											$total_bulat = 0;
											foreach ($trans as $data) {
												$new_date = date('d-M-Y H:i:s', strtotime($data['created_at']));
												$total_biaya += $data['biaya_trans'];
												$total_bulat += $data['biaya_bulat'];
												if ($data['biaya_trans'] == 0) {
													$get_retur_hapus = $db->query("SELECT COUNT(*) as data_retur FROM rincian_transaksi_obat ro INNER JOIN rincian_detail_obat rd ON(ro.id_trans_obat=rd.id_trans_obat) INNER JOIN rincian_retur_obat rr ON(rd.id_detail_rincian=rr.id_detail_rincian) WHERE ro.id_trans_obat='" . $data['id_trans_obat'] . "'");
													$get_retur = $get_retur_hapus->fetch(PDO::FETCH_ASSOC);
													if ($get_retur['data_retur'] > 0) {
														$code = "<span class='label label-primary'>R</span>";
														$hapus_button = "";
													} else {
														$code = "<span class='label label-default'>NR</span>";
														$hapus_button = "<a onclick=\" return del_alert('obat_trans_hapus.php?id=" . $id_rincian . "&t=" . $data['id_trans_obat'] . "&task=hapus')\" href='obat_trans_hapus.php?id=" . $id_rincian . "&t=" . $data['id_trans_obat'] . "&task=hapus' class='btn btn-block btn-sm btn-danger'>Hapus</a>";
													}
												} else {
													$get_retur_hapus = $db->query("SELECT COUNT(*) as data_retur FROM rincian_transaksi_obat ro INNER JOIN rincian_detail_obat rd ON(ro.id_trans_obat=rd.id_trans_obat) INNER JOIN rincian_retur_obat rr ON(rd.id_detail_rincian=rr.id_detail_rincian) WHERE ro.id_trans_obat='" . $data['id_trans_obat'] . "'");
													$get_retur = $get_retur_hapus->fetch(PDO::FETCH_ASSOC);
													if ($get_retur['data_retur'] > 0) {
														$code = "<span class='label label-primary'>R</span>";
													} else {
														$code = "<span class='label label-default'>NR</span>";
													}
													$hapus_button = "";
												}
												echo "<tr>
														<td>" . $no++ . "</td>
														<td>" . $code . "</td>
														<td>" . $new_date . "</td>
														<td>" . $data['ruang'] . "</td>
														<td>" . $data['nama_ruang'] . "</td>
														<td>Rp." . number_format($data['biaya_trans'], 0, ',', '.') . "</td>
														<td>" . $hapus_button . "
														<a href='obat_ranap_detail.php?id=" . $id_rincian . "&t=" . $data['id_trans_obat'] . "&r=" . $data['ruang'] . "&task=detail' class='btn btn-block btn-xs btn-info'><i class=\"fa fa-search\"></i> Detail</a>
																							<a href='cetak_bukti_transaksi.php?id=" . $id_rincian . "&t=" . $data['id_trans_obat'] . "&r=" . $data['ruang'] . "' class='btn btn-block btn-warning btn-xs' target='_blank'><i class=\"fa fa-print\"></i>Cetak</a>
														</td>
													</tr>";
											}
											// <a href='obat_ranap_retur.php?id=".$id_rincian."&t=".$data['id_trans_obat']."&task=retur' class='btn btn-sm btn-warning'>Retur</a>
											$ratusan = substr($total_biaya, -3);
											if ($ratusan < 450) {
												$total_harga = $total_biaya - $ratusan;
											} else if (($ratusan >= 450) && ($ratusan < 950)) {
												$total_harga = ($total_biaya - $ratusan) + 500;
											} else {
												$total_harga = $total_biaya + (1000 - $ratusan);
											}
											$selisih = $total_harga - $total_biaya;
											//update total_harga
											$update_total = $db->query("UPDATE rincian_obat_pasien SET total_biaya_apotek=" . $total_biaya . ",selisih_pembulatan=" . $selisih . ",pembayaran_pasien=" . $total_harga . " WHERE id_rincian_obat=" . $id_rincian);
											?>
										</tbody>
										<tfoot>
											<tr>
												<th colspan="5" style='text-align:right'>Total Biaya</th>
												<td colspan="2"><b>Rp.<?php echo number_format($total_biaya, 0, ',', '.'); ?></b></td>
											</tr>
											<tr>
												<th colspan="5" style='text-align:right'>Pembulatan</th>
												<td colspan="2"><b>Rp.(<?php echo number_format($selisih, 0, ',', '.'); ?>)</b></td>
											</tr>
											<tr>
												<th colspan="5" style='text-align:right'>Total yg harus dibayar pasien</th>
												<td colspan="2"><b>Rp.<?php echo number_format($total_harga, 0, ',', '.'); ?></b></td>
											</tr>
										</tfoot>
									</table>
								</div>
							</div>
							<div class="box-footer">
								<?php
								if ($total_biaya != 0) {
									echo "<a class=\"btn btn-app\" onclick=\"return my_validasi('obat_val_kasir.php?rincian=" . $id_rincian . "&total=" . $total_biaya . "')\"\"><i class=\"fa fa-check\"></i>Validasi</a>";
									//echo "<a class=\"btn btn-app\" href=\"obat_to_kasir.php?rincian=".$id_rincian."&total=".$total_biaya."\"><i class=\"fa fa-save\"></i>Simpan</a>";
									echo "<br>
  										<b>* <i>Klik Validasi jika seluruh obat ranap keluar sudah diinputkan. (validasi pembayaran untuk kasir)</i><br>
  										</b>";
								}
								//update total_harga
								$update_total = $db->query("UPDATE rincian_obat_pasien SET total_biaya_apotek=" . $total_biaya . " WHERE id_rincian_obat=" . $id_rincian);
								?>
							</div>
						</div><!-- /.right column -->
					</div><!-- /.row -->
					<!-- right column -->
					<div class="col-md-6">
						<div class="box box-primary">
							<div class="box-header">
								<i class="fa fa-medkit"></i>
								<h3 class="box-title">Data Retur Obat</h3>
								<button type="button" class="btn btn-primary btn-sm pull-right" data-toggle="modal" data-target="#myModal" data-task="retur" data-id="<?php echo $id_rincian ?>"><i class="fa fa-plus"></i> Tambah Retur</button>
								<!-- <span><a href="obat_ranap_retur_search.php?id=<?php echo $id_rincian; ?>&task=retur" class="btn btn-xs btn-warning pull-right"><i class="fa fa-plus"></i>Tambah Retur</a></span> -->
							</div><!-- /.box-header -->
							<!-- form start -->
							<div class="box-body">
								<div class="form-group">
									<table class="table table-striped">
										<thead>
											<tr class="info">
												<th>No.</th>
												<th>Tanggal</th>
												<th>Ruang</th>
												<th>Petugas Retur</th>
												<th>Total Biaya</th>
												<th>Lihat</th>
											</tr>
										</thead>
										<tbody>
											<?php
											$no = 1;
											$total_biaya = 0;
											foreach ($retur_barang as $ret) {
												$ret_date = date('d-M-Y H:i:s', strtotime($ret['created_at']));
												$total_biaya += $ret['total_retur'];
												if ($ret['total_retur'] == 0) {
													$hapus = "<button id='btnHapusRetur' data-id='" . $id_rincian . "' data-p='" . $ret['id_parent_retur'] . "' class='btn btn-block btn-sm btn-danger'><i class=\"fa fa-trash\"></i> Batalkan</button>";
												} else {
													$hapus = "";
												}
												echo "<tr>
														<td>" . $no++ . "</td>
														<td>" . $ret_date . "</td>
														<td>" . $ret['ruangan'] . "</td>
														<td>" . $ret['petugas_retur'] . "</td>
														<td>Rp " . number_format($ret['total_retur'], 0, ',', '.') . "</td>
														<td>
															<button type=\"button\" class=\"btn btn-block btn-info btn-xs\" id=\"myBtn\" data-id=\"" . $ret['id_parent_retur'] . "\" data-nama=\"Data Retur Ruangan " . $ret['ruangan'] . "\" data-toggle=\"modal\" data-backdrop=\"static\" data-target=\"#myDokumen\"><i class=\"fa fa-search\"></i> Lihat</button>
															<a href='cetak_bukti_retur.php?id=" . $ret['id_parent_retur'] . "' class='btn btn-block btn-warning btn-xs' target='_blank'><i class=\"fa fa-print\"></i>Cetak</a>
															" . $hapus . "
														</td>
													</tr>";
											}
											?>
										</tbody>
										<tfoot>
											<tr>
												<th colspan="4" style='text-align:right'>Total Biaya Retur</th>
												<td><b>Rp<?php echo number_format($total_biaya, 0, ',', '.'); ?></b></td>
											</tr>
										</tfoot>
									</table>
								</div>
							</div>
						</div><!-- /.right column -->
					</div><!-- /.row -->
			</section><!-- /.content -->
			<!-- Modal -->
			<div id="myModal" class="modal fade" role="dialog">
				<div class="modal-dialog">
					<!-- Modal content-->
					<div class="modal-content">
						<div class="modal-header bg-blue">
							<button type="button" class="close" data-dismiss="modal">&times;</button>
							<h4 class="modal-title">Form Transaksi Retur Obat/BMHP</h4>
						</div>
						<div class="modal-body">
							<input type="hidden" name="id_rincian" id="id_rincian" value="<?php echo $id_rincian; ?>">
							<div class="form-group">
								<label for="">Petugas yg meretur barang</label>
								<select name="petugas_retur" id="petugas_retur" class="form-control select2" style="width:100%;" required>
									<option value=""></option>
									<?php
									foreach ($pegawai as $peg) {
										echo '<option value="' . $peg['nama'] . '">' . $peg['nama'] . '</option>';
									}
									?>
								</select>
							</div>
							<div class="form-group">
								<label for="">Ruang Retur</label>
								<select class="form-control select21" name="ruang_retur" id="ruang_retur" style="width:100%;" required>
									<option value=""></option>
									<?php
									foreach ($lokasi_retur as $l) {
										echo '<option value="' . $l['ruangan'] . '">' . $l['ruangan'] . '</option>';
									}
									?>
								</select>
							</div>
						</div>
						<div class="modal-footer">
							<button type="button" id="tambahRetur" class="btn btn-primary"><i class="fa fa-plus"></i> Tambah</button>
							<button type="button" class="btn btn-danger" data-dismiss="modal"><i class="fa fa-times"></i> Tutup</button>
						</div>
					</div>
				</div>
			</div>
			<!-- dokumen Modal -->
			<div id="myDokumen" class="modal fade" role="dialog">
				<div class="modal-dialog modal-lg">

					<!-- Modal content-->
					<div class="modal-content">
						<div class="modal-header bg-blue">
							<button type="button" class="close" data-dismiss="modal">&times;</button>
							<h4 class="modal-title" id="modal_title">judul</h4>
						</div>
						<div class="modal-body">
							<div class="table-responsive">
								<span id="table_ref" class="hide">test</span>
								<table id="tabel_dokumen" class="table table-hover table-striped">
									<thead>
										<tr class="info">
											<th>No</th>
											<th>Tanggal Input</th>
											<th>Nama Obat/BMHP</th>
											<th>Jenis</th>
											<th>Merk/Pabrikan</th>
											<th>Jumlah Retur</th>
											<th>Total Harga</th>
											<th>Petugas Farmasi</th>
										</tr>
									</thead>
								</table>
							</div>
						</div>
						<div class="modal-footer">
							<button type="button" id="closeDokumen" class="btn btn-danger" data-dismiss="modal"><i class="fa fa-times"></i> Close</button>
						</div>
					</div>

				</div>
			</div> <!-- end modul dokumen -->
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
	<!-- typeahead -->
	<script src="../plugins/typeahead/typeahead.bundle.js" type="text/javascript"></script>
	<!-- notification -->
	<script src="../plugins/pnotify/js/pnotify.custom.min.js" type="text/javascript"></script>
	<script src="../plugins/pnotify/js/modular_pnotify.js" type="text/javascript"></script>
	<!-- select2 -->
	<script src="../plugins/select2/select2.full.min.js" type="text/javascript"></script>
	<!-- iCheck 1.0.1 -->
	<script src="../plugins/iCheck/icheck.min.js" type="text/javascript"></script>
	<!-- FastClick -->
	<script src='../plugins/fastclick/fastclick.min.js'></script>
	<!-- AdminLTE App -->
	<script src="../dist/js/app.min.js" type="text/javascript"></script>
	<!-- page script -->
	<script type="text/javascript">
		var tabel_dokumen;
		$(document).ready(function() {
			function format1(n, currency) {
				return currency + n.toFixed(2).replace(/./g, function(c, i, a) {
					return i > 0 && c !== "," && (a.length - i) % 3 === 0 ? "." + c : c;
				});
			}
			$('#btnHapusRetur').on("click", function() {
				var id_rincian = $('#btnHapusRetur').data('id');
				console.log(id_rincian);
				var parent = $('#btnHapusRetur').data('p');
				my_alert_retur('retur_ranap_delete.php', parent, id_rincian);
			});
			$('#myDokumen').on('show.bs.modal', function(event) {
				var button = $(event.relatedTarget) // Button that triggered the modal
				var recipient = button.data('id') // Extract info from data-* attributes
				var title = button.data('nama')
				// If necessary, you could initiate an AJAX request here (and then do the updating in a callback).
				// Update the modal's content. We'll use jQuery here, but you could use a data binding library or other methods instead.
				var modal = $(this)
				modal.find('#table_ref').text(recipient)
				modal.find('#modal_title').text(title)
				$.ajax({
					url: 'ajax_data/data_list_retur.php?p=' + recipient,
					dataType: 'JSON',
					success: function(response) {
						console.log(response.data);
						var data = response.data;

						//clear the table to remove cloned rows
						if (tabel_dokumen) tabel_dokumen.clear();

						//reinitialise the dataTable
						tabel_dokumen = $('#tabel_dokumen').DataTable({
							destroy: true,
							bLengthChange: false,
							paging: false
						});
						$.each(data, function(i, item) {
							var id_button = 'btnEdit' + i;
							var loop = i + 1;
							var total_harga = item.jumlah_retur * item.harga_jual;
							tabel_dokumen.row.add([
								loop,
								item.created_at,
								item.nama_obat,
								item.jenis,
								item.merk_pabrikan,
								item.jumlah_retur,
								// format1(total_harga, 'Rp'),
								total_harga,
								item.nama_petugas,
							]).draw();
						});
					}
				});
			});
			$('.select2').select2({
				placeholder: 'Masukan Nama Petugas',
				allowClear: true,
				width: 'resolve'
			});
			$('.select21').select2({
				placeholder: 'Masukan Nama Ruangan',
				allowClear: true,
				width: 'resolve'
			});
			$("#example1").dataTable();
			$('#example2').dataTable({
				"bPaginate": true,
				"bLengthChange": false,
				"bFilter": false,
				"bSort": true,
				"bInfo": true,
				"bAutoWidth": false
			});
			$('#tambahRetur').on("click", function() {
				var id_rincian = $('#id_rincian').val();
				var petugas_retur = $('#petugas_retur').val();
				var ruang_retur = $('#ruang_retur').val();
				if (petugas_retur == '') {
					buatNotifikasi('Peringatan', 'Petugas Retur Belum diisi', 'warning', true);
				} else if (ruang_retur == '') {
					buatNotifikasi('Peringatan', 'Ruang Retur Belum diisi', 'warning', true);
				} else {
					var fd = new FormData();
					fd.append("id_rincian", id_rincian);
					fd.append("petugas_retur", petugas_retur);
					fd.append("ruang_retur", ruang_retur);
					$.ajax({
						type: 'POST',
						url: 'ajax_data/add_retur_parent.php',
						data: fd,
						contentType: false,
						cache: false,
						processData: false,
						success: function(msg) {
							var response = JSON.parse(msg);
							if (response.status == 'sukses') {
								window.location = "obat_ranap_retur_search.php?p=" + response.id_parent + "&id=" + response.id_rincian + "&task=retur";
							} else {
								buatNotifikasi('Gagal', response.pesan, 'error', true);
							}
						}
					});
				}
			});
		});
		//Date range picker
		$('#tglkeluar').datepicker({
			format: 'dd/mm/yyyy',
			todayHighlight: true,
			autoclose: true
		});

		function my_alert(url_link) {
			var x = confirm("Apakah Anda Yakin?");
			if (x == true) {
				window.location = url_link;
			} else {
				return false;
			}
		}

		function my_alert_batal(url_link, id) {
			var x = confirm("Apakah Anda Yakin?");
			if (x == true) {
				window.location = url_link + "?id=" + id;
			} else {
				return false;
			}
		}

		function my_alert_retur(url_link, id, param) {
			var x = confirm("Apakah Anda Yakin?");
			if (x == true) {
				window.location = url_link + "?p=" + id + "&id=" + param;
			} else {
				return false;
			}
		}

		function my_validasi(url_link) {
			var x = confirm("Apakah Anda Yakin akan memvalidasi Transaksi Obat Pasien ini ?");
			if (x == true) {
				window.location = url_link;
			} else {
				return false;
			}
		}

		function del_alert(url_link) {
			var x = confirm("Peringatan!\nData yang sudah dihapus tidak dapat dikembalikan\nApakah Anda Yakin?");
			if (x == true) {
				window.location = url_link;
			} else {
				return false;
			}
		}
	</script>

</body>

</html>