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
$id_resep = isset($_GET['d']) ? $_GET['d'] : '';
$tipe_depo = $tipes[2];
$conf = json_decode(file_get_contents("config/env_depo.json"),true);
$id_depo = $conf[$tipe_depo]["id_depo"];
$header = $db->query("SELECT * FROM resep WHERE id_resep='".$id_resep."'");
$head = $header->fetch(PDO::FETCH_ASSOC);
$get_rajal_igd = $db->query("SELECT * FROM apotekkeluar WHERE id_resep='".$id_resep."'");
$data_rajal = $get_rajal_igd->fetchAll(PDO::FETCH_ASSOC);
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
					<div class="col-md-12">
						<div class="box" style="border-top:3px solid #605ca8">
	              <div class="box-header">
	                <i class="fa fa-user"></i>
				  				<h3 class="box-title">Data Rincian Transaksi ( <?php echo $head['nama']." | ".$head['ruang']." | ".$head['dokter']; ?>)</h3>
									<span class="pull-right">
										<a href="cut_off_transaksi_acc.php?tanggal_awal=<?php echo $tanggal_awal; ?>&tanggal_akhir=<?php echo $tanggal_akhir; ?>" class="btn btn-md btn-primary">&laquo; Kembali</a>
									</span>
	              </div><!-- /.box-header -->
	              <div class="box-body">
									<div class="table-responsive">
										<table id="rajal" class="table table-hover table-striped">
											<thead>
												<tr class="bg-purple">
													<th>Nama</th>
													<th>Sumber</th>
													<th>Volume</th>
													<th>No Batch</th>
													<th>Expired Date</th>
												</tr>
											</thead>
											<tbody>
												<?php
													foreach ($data_rajal as $row) {
														echo '<tr>
																		<td>'.$row['namaobat'].'</td>
																		<td>'.$row['sumber'].'</td>
																		<td>'.$row['volume'].'</td>
																		<td>'.$row['no_batch'].'</td>
																		<td>'.$row['expired_date'].'</td>
																</tr>';
													}
												?>
											</tbody>
										</table>
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
