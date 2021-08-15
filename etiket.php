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
$id_obatkeluar=isset($_GET["id"]) ? $_GET['id'] : '';
$id_resep=isset($_GET["resep"]) ? $_GET['resep'] : '';
//get nama obat
$sql_obat = $db->query("SELECT * FROM resep r INNER JOIN apotekkeluar a ON(a.id_resep=r.id_resep) WHERE r.id_resep='".$id_resep."' AND a.id_obatkeluar='".$id_obatkeluar."'");
$obat = $sql_obat->fetch(PDO::FETCH_ASSOC);
// get data etiket_apotek
$etiket_q = $db->query("SELECT * FROM etiket_apotek_rajal WHERE id_obatkeluar='".$id_obatkeluar."' ORDER BY id_etiket_rajal DESC LIMIT 1");
$etiket = $etiket_q->fetch(PDO::FETCH_ASSOC);
$total_etiket = $etiket_q->rowCount();
if($total_etiket>0){
	$sehari_x = isset($etiket['sehari_x']) ? $etiket['sehari_x'] : '';
	$takaran = isset($etiket['takaran']) ? $etiket['takaran'] : '';
	$diminum = isset($etiket['diminum']) ? $etiket['diminum'] : '';
	$petunjuk_khusus = isset($etiket['petunjuk_khusus']) ? $etiket['petunjuk_khusus'] : '';
	$expire_date = $etiket['expired_date'];
	$label_khusus = $etiket['label_khusus'];
}else{
	$sehari_x = '';
	$takaran = '';
	$diminum = '';
	$petunjuk_khusus = '';
	$expire_date = "";
	$label_khusus="";
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
	<!-- iCheck for checkboxes and radio inputs -->
    <link href="../plugins/iCheck/all.css" rel="stylesheet" type="text/css" />
	<!-- BootsrapSelect -->
    <link href="../plugins/bootstrap-select/bootstrap-select.min.css" rel="stylesheet" type="text/css" />
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
	    <?php if (isset($_GET['status']) && ($_GET['status'] == "1")) { ?><div class="alert alert-success alert-dismissable"><button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button><center><h4><i class="icon fa fa-check"></i>Berhasil</h4>Data resep telah diinput</center></div>
		<?php } else if (isset($_GET['status']) && ($_GET['status'] == "2")) { ?><div class="alert alert-success alert-dismissable"><button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button><center><h4><i class="icon fa fa-check"></i>Berhasil</h4>Data pasien telah diproses</center></div>
	<?php } else if (isset($_GET['status']) && ($_GET['status'] == "3")) { ?><div class="alert alert-danger alert-dismissable"><button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button><center><h4><i class="icon fa fa-ban"></i>Peringatan!</h4>Data resep tidak boleh kosong</center></div>
	    <?php } ?>
	    <!-- end pesan -->
        <section class="content-header">
          <h1>
            Cetak
            <small>etiket</small>
          </h1>
          <ol class="breadcrumb">
            <li><a href="index.php"><i class="fa fa-dashboard"></i> Home</a></li>
            <li>Cetak</li><li class="active">etiket</li>
          </ol>
        </section>

        <!-- Main content -->
        <section class="content">
          <div class="box box-primary">
                <div class="box-header">
                  <i class="fa fa-user"></i>
				  <h3 class="box-title">Input data etiket <?php echo $obat['namaobat']; ?></h3>
                </div><!-- /.box-header -->
				<!-- form start -->
				<?php $url_save = "save_etiket_rajal.php?i=".$id_obatkeluar."&resep=".$id_resep; ?>
                <form role="form" action="<?php echo $url_save; ?>" method="post">
									<div class="box-body">
					<div class="row">
					<div class="col-xs-2">
                      <label for="sehari">Sehari X</label>
                      <input type="text" class="form-control" id="sehari" name="sehari" placeholder="misal 3 X 1" value="<?php echo $sehari_x; ?>" required>
                    </div>
					<div class="col-xs-2">
                      <label for="takaran">Takaran</label>
                      <input type="text" class="form-control" id="takaran" name="takaran" value="<?php echo $takaran; ?>" required>
                    </div>
					<div class="col-xs-2">
                      <label for="minum">Diminum</label>
                      <input type="text" class="form-control" id="minum" name="minum" value="<?php echo $diminum; ?>" required>
                    </div>
					<div class="col-xs-5">
                      <label for="petunjuk">Petunjuk Khusus</label>
                      <input type="text" class="form-control" id="petunjuk" name="petunjuk" value="<?php echo $petunjuk_khusus; ?>" required>
                    </div>
					<div class="col-xs-2">
                      <label for="ed">ED</label>
                      <input type="text" class="form-control" id="edate" name="edate" value="<?php echo substr($expire_date,0,10); ?>" required>
                    </div>
										<div class="col-xs-2">
											<div class="form-group">
											  <label for="">Label Khusus</label>
											  <select name="label_khusus" id="label_khusus" class="form-control">
													<?php
														if($label_khusus=='Awas! Obat Keras Tidak boleh di telan'){
															echo '<option value="">--Pilih Label Khusus--</option>
															<option value="Awas! Obat Keras Tidak boleh di telan" selected>Awas! Obat Keras Tidak boleh di telan</option>
															<option value="KOCOK DAHULU">Kocok Dahulu</option>
															<option value="Elektrolit Pekat,<br> Harus diencerkan sebelum diberikan">Elektrolit Pekat, Harus diencerkan sebelum diberikan</option>';
														}else if($label_khusus=='KOCOK DAHULU'){
															echo '<option value="">--Pilih Label Khusus--</option>
															<option value="Awas! Obat Keras Tidak boleh di telan">Awas! Obat Keras Tidak boleh di telan</option>
															<option value="KOCOK DAHULU" selected>Kocok Dahulu</option>
															<option value="Elektrolit Pekat,<br> Harus diencerkan sebelum diberikan">Elektrolit Pekat, Harus diencerkan sebelum diberikan</option>';
														}else if($label_khusus=='Elektrolit Pekat, Harus diencerkan sebelum diberikan'){
															echo '<option value="">--Pilih Label Khusus--</option>
															<option value="Awas! Obat Keras Tidak boleh di telan">Awas! Obat Keras Tidak boleh di telan</option>
															<option value="KOCOK DAHULU">Kocok Dahulu</option>
															<option value="Elektrolit Pekat,<br> Harus diencerkan sebelum diberikan" selected>Elektrolit Pekat, Harus diencerkan sebelum diberikan</option>';
														}else{
															echo '<option value="">--Pilih Label Khusus--</option>
															<option value="Awas! Obat Keras Tidak boleh di telan">Awas! Obat Keras Tidak boleh di telan</option>
															<option value="KOCOK DAHULU">Kocok Dahulu</option>
															<option value="Elektrolit Pekat,<br> Harus diencerkan sebelum diberikan">Elektrolit Pekat, Harus diencerkan sebelum diberikan</option>';
														}
													?>
											  </select>
											</div>
										</div>
					</div><br>
                  </div><!-- /.box-body -->

				  <div class="box-footer">
                    <button type="submit" class="btn btn-primary">Print</button>
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
	<!-- date-picker -->
    <script src="../plugins/datepicker/bootstrap-datepicker.js" type="text/javascript"></script>
	<!-- iCheck 1.0.1 -->
    <script src="../plugins/iCheck/icheck.min.js" type="text/javascript"></script>
	<!-- BootsrapSelect -->
    <script src="../plugins/bootstrap-select/bootstrap-select.min.js" type="text/javascript"></script>
	<!-- typeahead -->
    <script src="../plugins/typeahead/typeahead.bundle.js" type="text/javascript"></script>
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
	  $(document).ready(function() {
		  $("#nomedrek").change(function()
	  {
		  if($(this).val() == "-") {
			  $("#namaValue").show(500);
		  } else {
			  $("#namaValue").hide(500);
		  }
	  });
	  $("#namaValue").hide();
	  });
	  $(function () {
        $("#example1").dataTable();
      });
	  //Date range picker
      $('#tanggalr').datepicker({
	    format: 'dd/mm/yyyy',
		todayHighlight: true,
		autoclose: true
	  });
    </script>

  </body>
</html>
