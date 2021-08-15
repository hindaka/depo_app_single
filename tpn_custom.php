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
?>
<!DOCTYPE html>
<html>
  <head>
    <meta charset="UTF-8">
    <title>SIMRS <?php echo $version_depo; ?> | <?php echo $r1["tipe"]; ?></title>
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
	    <?php if (isset($_GET['status']) && ($_GET['status'] == "1")) { ?><div class="alert alert-success alert-dismissable"><button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button><center><h4><i class="icon fa fa-check"></i>Berhasil</h4>Data TPN Berhasil ditambahkan</center></div>
		<?php }else if (isset($_GET['status']) && ($_GET['status'] == "2")) { ?><div class="alert alert-warning alert-dismissable"><button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button><center><h4><i class="icon fa fa-warning"></i>Peringatan</h4>TIPE TPN dengan Konsentrasi yang anda inputkan tersebut sudah terdaftar</center></div>
	    <?php } ?>
	    <!-- end pesan -->
        <section class="content-header">
          <h1>
            Pengelolaan Stiker
            <small>Custom Stiker TPN</small>
          </h1>
          <ol class="breadcrumb">
            <li><a href="index.php"><i class="fa fa-dashboard"></i> Home</a></li>
            <li class="active">Custom Stiker TPN</li>
          </ol>
        </section>

        <!-- Main content -->
        <section class="content">
          <div class="row">
            <div class="col-xs-12">
              <div class="box box-primary">
                <div class="box-header">
                  <i class="fa fa-user"></i>
								  <h3 class="box-title">Form Input Data TPN</h3>
                </div><!-- /.box-header -->
								<form class="" action="cetak_tpn_custom.php" id="tpn_form" method="post">
                <div class="box-body">
									<div class="form-group">
									  <label for="">No.Rekam Medis <span style="color:red">*</span></label>
									  <input type="text" class="form-control" id="nomedrek" name="nomedrek" placeholder="Masukan Nomor Rekam Medis Pasien" required>
									</div>
									<!-- <div class="row">
										<div class="col-md-6">
											<div class="form-group">
												<label for="konsentrasi">Konsentrasi <span style="color:red;">*</span></label>
												<div class="input-group">
													<input type="text" class="form-control" id="konsentrasi" name="konsentrasi" placeholder="Masukan Konsentrasi Dextrose" required>
													<span class="input-group-addon">%</span>
												</div>
											</div>
										</div>
										<div class="col-md-6">
											<div class="form-group">
												<label for="gr">1 gram ? <span style="color:red;">*</span></label>
												<div class="input-group">
													<input type="text" class="form-control" id="gr" name="gr" placeholder="Masukan 1 gram = brp ml?" required>
													<span class="input-group-addon">ml</span>
												</div>
											</div>
										</div>
									</div> -->
									<div class="row">
										<div class="col-sm-3">
											<div class="form-group">
											  <label for="amino_acid">Amino Acid 6% <span style="color:red;">*</span></label>
												<div class="input-group">
												  <input type="text" class="form-control" id="amino_acid" name="amino_acid" placeholder="Amino Acid 6% dalam cc" required>
													<span class="input-group-addon">cc</span>
												</div>
											</div>
										</div>
										<div class="col-sm-3">
											<div class="form-group">
											  <label for="dex40">Dextrose 40% <span style="color:red;">*</span></label>
												<div class="input-group">
												  <input type="text" class="form-control" id="dex40" name="dex40" placeholder="Dextrose 40% dalam cc" required>
													<span class="input-group-addon">cc</span>
												</div>
											</div>
										</div>
										<div class="col-sm-3">
											<div class="form-group">
											  <label for="dex10">Dextrose 10% <span style="color:red;">*</span></label>
												<div class="input-group">
												  <input type="text" class="form-control" id="dex10" name="dex10" placeholder="Dextrose 10% dalam cc" required>
													<span class="input-group-addon">cc</span>
												</div>
											</div>
										</div>
										<div class="col-sm-3">
											<div class="form-group">
												<label for="kcl">KCL<span style="color:red;">*</span></label>
												<div class="input-group">
													<input type="text" class="form-control" id="kcl" name="kcl" placeholder="KCL dalam cc" required>
													<span class="input-group-addon">cc</span>
												</div>
											</div>
										</div>
									</div>
									<div class="row">
										<div class="col-md-3">
											<div class="form-group">
												<label for="ca_glu_10">Ca glukonas 10% <span style="color:red">*</span></label>
												<div class="input-group">
													<input type="text" class="form-control" id="ca_glu_10" name="ca_glu_10" placeholder="Ca Glukonas 10% dalam cc" required>
													<span class="input-group-addon">cc</span>
												</div>
											</div>
										</div>
										<div class="col-md-3">
											<div class="form-group">
												<label for="mgso4">MgSO4 40% <span style="color:red">*</span></label>
												<div class="input-group">
													<input type="text" class="form-control" id="mgso4" name="mgso4" placeholder="MgSO4 40% dalam cc" required>
													<span class="input-group-addon">cc</span>
												</div>
											</div>
										</div>
										<div class="col-md-3">
											<div class="form-group">
												<label for="ns3">NS 3% <span style="color:red">*</span></label>
												<div class="input-group">
													<input type="text" class="form-control" id="ns3" name="ns3" placeholder="NS 3% dalam cc" required>
													<span class="input-group-addon">cc</span>
												</div>
											</div>
										</div>
										<div class="col-md-3">
											<div class="form-group">
												<label for="ns3">Heparin <span style="color:red">*</span></label>
												<div class="input-group">
													<input type="text" class="form-control" id="heparin" name="heparin" placeholder="Heparin dalam cc" required>
													<span class="input-group-addon">cc</span>
												</div>
											</div>
										</div>
									</div>
                </div><!-- /.box-body -->
								<div class="box-footer">
									<button type="submit" id="simpan_tpn" class="btn btn-md btn-success"><i class="fa fa-print"></i> Cetak</button>
								</div>
							</form>
              </div><!-- /.box -->
            </div><!-- /.col -->
          </div><!-- /.row -->
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
    <script src="../plugins/datatables/jquery.dataTables.min.js" type="text/javascript"></script>
    <script src="../plugins/datatables/dataTables.bootstrap.js" type="text/javascript"></script>
    <!-- SlimScroll -->
    <script src="../plugins/slimScroll/jquery.slimscroll.min.js" type="text/javascript"></script>
    <!-- FastClick -->
    <script src='../plugins/fastclick/fastclick.min.js'></script>
    <!-- AdminLTE App -->
    <script src="../dist/js/app.min.js" type="text/javascript"></script>

  </body>
</html>
