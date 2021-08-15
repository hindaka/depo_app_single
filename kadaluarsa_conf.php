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
// $myfile = fopen("ajax_data/kadaluarsa.txt", "w");
if(file_exists('config/kadaluarsa.txt')){
	$myData = file_get_contents('config/kadaluarsa.txt');
	$split = explode(";",$myData);
	$red = $split[0];
	$yellow = $split[1];
	$green = $split[2];
}else{
	$myData = 0;
	$red =0;
	$yellow = 0;
	$green = 0;
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
    <!-- DATA TABLES -->
    <link href="../plugins/datatables/dataTables.bootstrap.css" rel="stylesheet" type="text/css" />
	<!-- iCheck for checkboxes and radio inputs -->
    <link href="../plugins/iCheck/all.css" rel="stylesheet" type="text/css" />
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
      <div class="content-wrapper">
        <?php if (isset($_GET['status']) && ($_GET['status'] == "1")) { ?><div class="alert alert-success alert-dismissable"><button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button><center><h4><i class="icon fa fa-check"></i>Berhasil</h4>Pengaturan Waktu Kontrol Kadaluarsa Berhasil dilakukan</center></div>
  	    <?php } ?>
        <!-- Content Header (Page header) -->
        <section class="content-header">
          <h1>
            Pengaturan
            <small>Waktu Kontrol Kadaluarsa</small>
          </h1>
          <ol class="breadcrumb">
            <li><a href="index.php"><i class="fa fa-dashboard"></i> Home</a></li>
            <li>Pengaturan</li>
            <li class="active">Waktu Kontrol Kadaluarsa</li>
          </ol>
        </section>

        <!-- Main content -->
        <section class="content">

              <!-- general form elements -->
			  <!-- left column -->
			  <div class="box box-success">
          <div class="box-header">
            <i class="fa fa-user"></i>
  				  <h3 class="box-title">Waktu Kontrol Kadaluarsa</h3>
          </div><!-- /.box-header -->
					<form action="kadaluarsa_conf_acc.php" method="post">
	          <div class="box-body">
							<div class="row">
								<div class="col-md-4">
									<div style="border-left:3px solid red;height:30px;padding-left:20px;font-size:20px;background-color: rgba(0,0,0,0.1)">
										Batas Bawah Kadaluarsa
									</div>
								</div>
								<div class="col-md-4">
									<div style="border-left:3px solid orange;height:30px;padding-left:20px;font-size:20px;background-color: rgba(0,0,0,0.1)">
										Batas Tengah Kadaluarsa
									</div>
								</div>
								<div class="col-md-4">
									<div style="border-left:3px solid green;height:30px;padding-left:20px;font-size:20px;background-color: rgba(0,0,0,0.1)">
										Batas Atas Kadaluarsa
									</div>
								</div>
							</div>
							<br>
							<div class="form-group">
								<div class="input-group">
							    <input id="waktu_kadaluarsa" name="waktu_kadaluarsa_red" type="number" class="form-control" placeholder="Masukan Berapa Bulan Periode Pengecekan" value="<?php echo (int)$red; ?>">
									<span class="input-group-addon bg-red">Bulan</span>
							  </div>
							</div>
							<div class="form-group">
								<div class="input-group">
							    <input id="waktu_kadaluarsa" name="waktu_kadaluarsa_yellow" type="number" class="form-control" placeholder="Masukan Berapa Bulan Periode Pengecekan" value="<?php echo (int)$yellow; ?>">
									<span class="input-group-addon bg-yellow">Bulan</span>
							  </div>
							</div>
							<div class="form-group">
								<div class="input-group">
							    <input id="waktu_kadaluarsa" name="waktu_kadaluarsa_green" type="number" class="form-control" placeholder="Masukan Berapa Bulan Periode Pengecekan" value="<?php echo (int)$green; ?>">
									<span class="input-group-addon bg-green">Bulan</span>
							  </div>
							</div>
	          </div>
						<div class="box-footer">
							<button type="submit" class="btn btn-success btn-md"><i class="fa fa-save"></i> Simpan</button>
						</div>
					</form>
			  </div><!-- /.left column -->

        </section><!-- /.content -->
      </div><!-- /.content-wrapper -->
      <!-- static footer -->
	  <?php include('footer.php'); ?><!-- /.static footer -->
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
	<!-- typeahead -->
    <script src="../plugins/typeahead/typeahead.bundle.js" type="text/javascript"></script>
	<!-- iCheck 1.0.1 -->
    <script src="../plugins/iCheck/icheck.min.js" type="text/javascript"></script>
    <!-- FastClick -->
    <script src='../plugins/fastclick/fastclick.min.js'></script>
    <!-- AdminLTE App -->
    <script src="../dist/js/app.min.js" type="text/javascript"></script>
    <!-- page script -->
    <script type="text/javascript">
      //Flat red color scheme for iCheck
      $('input[type="radio"].flat-blue').iCheck({
        radioClass: 'iradio_flat-blue'
      });
	  //Date range picker
      $('#tanggala').datepicker({
	    format: 'dd/mm/yyyy',
		todayHighlight: true,
		autoclose: true
	  });
    </script>

  </body>
</html>
