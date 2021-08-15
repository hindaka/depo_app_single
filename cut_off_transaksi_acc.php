<?php
//conn
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
$tanggal_awal = isset($_GET['tanggal_awal']) ? $_GET['tanggal_awal'] : '';
$tanggal_akhir = isset($_GET['tanggal_akhir']) ? $_GET['tanggal_akhir'] : '';
$tipe_depo = $tipes[2];
$conf = json_decode(file_get_contents("config/env_depo.json"),true);
$id_depo = $conf[$tipe_depo]["id_depo"];

$get_rajal_igd = $db->query("SELECT r.id_resep,r.tgl_resep,r.dokter,r.ruang,r.nama,a.nama as nama_petugas FROM resep r LEFT JOIN anggota a ON(r.mem_id=a.mem_id) WHERE (r.statusbayar='Sudah dibayar' OR r.statusbayar='Proses Klaim') AND r.ruang LIKE '".$tipe_depo."' AND r.created_at>='".$tanggal_awal."' AND r.created_at<='".$tanggal_akhir."'");
$data_rajal = $get_rajal_igd->fetchAll(PDO::FETCH_ASSOC);
$get_ranap_igd = $db->query("SELECT rp.nama as nama_pasien,rp.nomedrek,rt.id_trans_obat,rt.created_at,ro.dpjp,rt.user,rt.id_rincian_obat,a.nama as nama_petugas FROM rincian_obat_pasien ro LEFT JOIN rincian_transaksi_obat rt ON(ro.id_rincian_obat=rt.id_rincian_obat) INNER JOIN anggota a ON(rt.user=a.mem_id) LEFT JOIN registerpasien rp ON(ro.id_pasien=rp.id_pasien) WHERE rt.id_warehouse='".$id_depo."' AND rt.created_at>='".$tanggal_awal."' AND rt.created_at<='".$tanggal_akhir."'");
$data_ranap = $get_ranap_igd->fetchAll(PDO::FETCH_ASSOC);
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
    <link href="../.css" rel="stylesheet" type="text/css" />
    <!-- DATA TABLES -->
    <link href="../plugins/datatables/dataTables.bootstrap.css" rel="stylesheet" type="text/css" />
		<link href="../plugins/datetimepicker/css/bootstrap-datetimepicker.css" rel="stylesheet" type="text/css">
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
	    <?php if (isset($_GET['status']) && ($_GET['status'] == "1")) { ?><div class="alert alert-success alert-dismissable"><button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button><center><h4><i class="icon fa fa-check"></i>Berhasil</h4>Data pasien telah diinput</center></div>
			<?php } else if (isset($_GET['status']) && ($_GET['status'] == "2")) { ?><div class="alert alert-success alert-dismissable"><button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button><center><h4><i class="icon fa fa-check"></i>Berhasil</h4>Data pasien telah diproses</center></div>
			<?php } else if (isset($_GET['status']) && ($_GET['status'] == "3")) { ?><div class="alert alert-danger alert-dismissable"><button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button><center><h4><i class="icon fa fa-ban"></i>Peringatan!</h4>Data pasien gagal diubah</center></div>
	    <?php } ?>
	    <!-- end pesan -->
        <section class="content-header">
          <h1>
            Daftar Rekapitulasi
            <small><?php echo "Depo Farmasi ".$tipes[2]; ?></small>
          </h1>
          <ol class="breadcrumb">
            <li><a href="index.php"><i class="fa fa-dashboard"></i> Home</a></li>
            <li class="active">Rekapitulasi <?php echo "Depo Farmasi ".$tipes[2]; ?></li>
          </ol>
        </section>

        <!-- Main content -->
        <section class="content">
					<div class="row">
						<div class="col-md-12">
							<div class="alert bg-purple">Klik <a style="text-decoration:none;" class="btn btn-xs btn-primary" href="export_cut_off.php?tanggal_awal=<?php echo $tanggal_awal; ?>&tanggal_akhir=<?php echo $tanggal_akhir; ?>" target="_blank"><i class="fa fa-download"></i> Export To Excel</a> untuk Export data Cut Off Depo Farmasi <?php echo $tipe_depo; ?></div>
						</div>
						<div class="col-md-6">
							<div class="box" style="border-top:3px solid #605ca8">
		              <div class="box-header">
		                <i class="fa fa-user"></i>
					  				<h3 class="box-title">Data Cut Off Transaksi Rajal</h3>
		              </div><!-- /.box-header -->
		              <div class="box-body">
										<div class="table-responsive">
											<table id="rajal" class="table table-hover table-striped">
												<thead>
													<tr class="bg-purple">
														<th>#</th>
														<th>#ID</th>
														<th>Tanggal</th>
														<th>Nama</th>
														<th>Dokter</th>
														<th>Petugas Farmasi</th>
													</tr>
												</thead>
												<tbody>
													<?php
														foreach ($data_rajal as $row) {
															echo '<tr>
																			<td><a href="cut_off_detail_rajal.php?tanggal_awal='.$tanggal_awal.'&tanggal_akhir='.$tanggal_akhir.'&d='.$row['id_resep'].'" class="btn btn-xs btn-primary"><i class="fa fa-search"></i></a></td>
																			<td>#'.$row['id_resep'].'</td>
																			<td>'.$row['tgl_resep'].'</td>
																			<td>'.$row['nama'].'</td>
																			<td>'.$row['dokter'].'</td>
																			<td>'.$row['nama_petugas'].'</td>
																	</tr>';
														}
													?>
												</tbody>
											</table>
										</div>
		              </div>
						  </div>
						</div>
						<div class="col-md-6">
							<div class="box" style="border-top:3px solid #605ca8">
		              <div class="box-header">
		                <i class="fa fa-user"></i>
					  				<h3 class="box-title">Data Cut Off Transaksi Ranap</h3>
		              </div><!-- /.box-header -->
		              <div class="box-body">
										<div class="table-responsive">
											<table id="ranap" class="table table-hover table-striped">
												<thead>
													<tr class="bg-purple">
														<th>#</th>
														<th>#ID</th>
														<th>Tanggal</th>
														<th>Nomedrek</th>
														<th>Nama</th>
														<th>Dokter</th>
														<th>Petugas Farmasi</th>
													</tr>
												</thead>
												<tbody>
													<?php
														foreach ($data_ranap as $d) {
															echo '<tr>
																			<td><a href="cut_off_detail_ranap.php?tanggal_awal='.$tanggal_awal.'&tanggal_akhir='.$tanggal_akhir.'&d='.$d['id_rincian_obat'].'&t='.$d['id_trans_obat'].'" class="btn btn-xs btn-primary"><i class="fa fa-search"></i></a></td>
																			<td>#'.$d['id_trans_obat'].'</td>
																			<td>'.$d['created_at'].'</td>
																			<td>'.$d['nomedrek'].'</td>
																			<td>'.$d['nama_pasien'].'</td>
																			<td>'.$d['dpjp'].'</td>
																			<td>'.$d['nama_petugas'].'</td>
																	</tr>';
														}
													?>
												</tbody>
											</table>
										</div>
		              </div>
						  </div>
						</div>
					</div>

        </section><!-- /.content -->
      </div><!-- /.content-wrapper -->
      <!-- static footer -->
	  <?php include "footer.php"; ?><!-- /.static footer -->
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
    <script src="../plugins/datetimepicker/js/moment-with-locales.js" type="text/javascript"></script>
		<script src="../plugins/datetimepicker/js/bootstrap-datetimepicker.js" type="text/javascript"></script>
    <!-- FastClick -->
    <script src='../plugins/fastclick/fastclick.min.js'></script>
    <!-- AdminLTE App -->
    <script src="../dist/js/app.min.js" type="text/javascript"></script>
    <!-- page script -->
    <script type="text/javascript">
      $(function () {
        $("#rajal").dataTable();
				$("#ranap").dataTable();
      });
		  //Date range picker
      $('#datetimepicker1').datetimepicker({
				format: "YYYY-MM-DD h:mm:ss"
			});
			$('#datetimepicker2').datetimepicker({
				format: "YYYY-MM-DD h:mm:ss",
				useCurrent: false
			});
			$("#datetimepicker1").on("dp.change", function (e) {
          $('#datetimepicker2').data("DateTimePicker").minDate(e.date);
      });
      $("#datetimepicker2").on("dp.change", function (e) {
          $('#datetimepicker1').data("DateTimePicker").maxDate(e.date);
      });
    </script>

  </body>
</html>
