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
$nomedrek = isset($_GET['id']) ? $_GET['id'] : '';
$sql_pasien = "SELECT id_pasien,nama,nomedrek FROM registerpasien WHERE nomedrek='".$nomedrek."' ORDER BY id_pasien DESC LIMIT 1";
$get_pasien = $db->query($sql_pasien);
$pasien = $get_pasien->fetch(PDO::FETCH_ASSOC);

//ambil data obat
$h3=$db->query("SELECT nama FROM nmdokter WHERE nama<>'Bidan' AND nama<>'Perawat' AND nama<>'-' ORDER BY nama ASC");
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
    <!-- Ionicons -->
    <link href="../plugins/ionicons/2.0.0/ionicon.min.css" rel="stylesheet" type="text/css" />
    <!-- DATA TABLES -->
    <link href="../plugins/datatables/dataTables.bootstrap.css" rel="stylesheet" type="text/css" />
		<!-- BootsrapSelect -->
	    <link href="../plugins/bootstrap-select/bootstrap-select.min.css" rel="stylesheet" type="text/css" />
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
			<?php } else if (isset($_GET['status']) && ($_GET['status'] == "4")) { ?><div class="alert alert-danger alert-dismissable"><button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button><center><h4><i class="icon fa fa-ban"></i>Peringatan!</h4>Data pasien tidak ditemukan</center></div>
			<?php } else if (isset($_GET['status']) && ($_GET['status'] == "5")) { ?><div class="alert alert-success alert-dismissable"><button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button><center><h4><i class="icon fa fa-check"></i>Berhasil!</h4>Rincian Obat Berhasil dibatalkan</center></div>
			<?php } else if (isset($_GET['status']) && ($_GET['status'] == "6")) { ?><div class="alert alert-success alert-dismissable"><button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button><center><h4><i class="icon fa fa-check"></i>Berhasil!</h4>Data Rincian Berhasil divalidasi.</center></div>
	    <?php } ?>
	    <!-- end pesan -->
        <section class="content-header">
          <h1>
            Rincian Obat
            <small><?php echo $r1["tipe"]; ?></small>
          </h1>
          <ol class="breadcrumb">
            <li><a href="index.php"><i class="fa fa-dashboard"></i> Home</a></li>
            <li class="active">Rincian Obat</li>
          </ol>
        </section>

        <!-- Main content -->
        <section class="content">
          <div class="box box-primary">
                <div class="box-header">
                  <i class="fa fa-user"></i>
				  <h3 class="box-title">Input DPJP</h3>
                </div><!-- /.box-header -->
				<!-- form start -->
            <form role="form" action="obat_ranap_c_dokter.php" method="get">
              <div class="box-body">
                <div class="form-group">
                  <label for="Nomedrek">No.Rekam Medis</label>
                  <input type="text" name="nomedrek" class="form-control" value="<?php echo $pasien['nomedrek']; ?>" readonly>
                </div>
                <div class="form-group">
                  <label for="Nomedrek">Nama Pasien</label>
                  <input type="text" name="nama_pasien" class="form-control" value="<?php echo $pasien['nama']; ?>" readonly>
                </div>
								<label for="dokter">DPJP <span style="color:red;">*</span></label>
								<select class="form-control selectpicker" data-live-search="true" name="dpjp" id="dpjp" required>
									<option value="">---Pilih DPJP---</option>
									<?php
										foreach ($data3 as $dok) {
											echo "<option value='".$dok['nama']."'>".$dok['nama']."</option>";
										}
									 ?>
								</select>
              </div><!-- /.box-body -->
				  <div class="box-footer">
            <button type="submit" class="btn btn-primary">Submit <i class="fa fa-send"></i></button>
          </div>
				</form>
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
    <!-- typeahead -->
      <script src="../plugins/typeahead/typeahead.bundle.js" type="text/javascript"></script>
	<!-- date-picker -->
    <script src="../plugins/datepicker/bootstrap-datepicker.js" type="text/javascript"></script>
		<!-- BootsrapSelect -->
	    <script src="../plugins/bootstrap-select/bootstrap-select.min.js" type="text/javascript"></script>
    <!-- FastClick -->
    <script src='../plugins/fastclick/fastclick.min.js'></script>
    <!-- AdminLTE App -->
    <script src="../dist/js/app.min.js" type="text/javascript"></script>
    <!-- page script -->
    <script type="text/javascript">
      $(function () {
        $("#example1").dataTable();
      });
	  //Date range picker
      $('#tanggalklinik').datepicker({
	    format: 'dd/mm/yyyy',
		todayHighlight: true,
		autoclose: true
	  });
    </script>

  </body>
</html>
