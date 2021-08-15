<?php
session_start();
include("../inc/pdo.conf.php");
include("../inc/version.php");
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
    <!-- bootstrap datepicker -->
    <link rel="stylesheet" href="../plugins/datepicker/datepicker3.css">
    <!-- select2 -->
    <link rel="stylesheet" href="../plugins/select2/select2.min.css">
    <!-- DATA TABLES -->
    <link href="../plugins/datatables/dataTables.bootstrap.css" rel="stylesheet" type="text/css" />
		<!-- Select2 -->
	  <link rel="stylesheet" href="../plugins/select2/select2.min.css" type="text/css">
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
	  <?php
      include('header.php');
      include "menu_index.php"; ?>
      <div class="content-wrapper">
				<?php if (isset($_GET['status']) && ($_GET['status'] == "1")) { ?><div class="alert alert-success alert-dismissable"><button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button><center><h4><i class="icon fa fa-check"></i>Berhasil!</h4>Data Awal Stock Opname Berhasil disimpan</center></div>
		    <?php } ?>
        <!-- Content Header (Page header) -->
        <section class="content-header">
          <h1>
            Stock Opname Depo
          </h1>
          <ol class="breadcrumb">
            <li><a href="index.php"><i class="fa fa-dashboard"></i> Stock Opname Depo</a></li>
          </ol>
        </section>
        <!-- Main content -->
        <section class="content">
        <div class="row">
          <div class="col-md-12 col-xs-12">
            <div class="box box-info">
              <div class="box-header with-border">
                <h3 class="box-title">Stock Opname Depo</h3>
              </div>
              <form action="stok_opnam_depo_koreksi_list.php" method="GET">
              <div class="box-body">
                <div class="row">
                  <div class="col-xs-12 col-md-4">
                    <div class="form-group">
                      <label for="">Bulan <span style="color:red">*</span></label>
											<select name="bulan" id="bulan" class="form-control" required>
												<option value="">--- Pilih Bulan ---</option>
												<option value="01">Januari</option>
												<option value="02">Februari</option>
												<option value="03">Maret</option>
												<option value="04">April</option>
												<option value="05">Mei</option>
												<option value="06">Juni</option>
												<option value="07">Juli</option>
												<option value="08">Agustus</option>
												<option value="09">September</option>
												<option value="10">Oktober</option>
												<option value="11">November</option>
												<option value="12">Desember</option>
											</select>
                    </div>
                  </div>
                  <div class="col-xs-12 col-md-8">
										<div class="form-group">
										  <label for="">Tahun <span style="color:red">*</span></label>
										  <select name="tahun" id="tahun" class="form-control" required>
										  	<option value="">--- Pilih Tahun ---</option>
												<?php
													for ($i=date('Y'); $i>=2020 ; --$i) {
														echo '<option value="'.$i.'">'.$i.'</option>';
													}
												?>
										  </select>
										</div>
									</div>
									<!-- <div class="col-md-3">
										<div class="form-group">
										  <label for="">Sumber Dana</label>
										  <select class="form-control" name="sumber_dana" id="sumber_dana" required>
										  	<option value="">--- Pilih Sumber Dana ---</option>
												<option value="APBD">APBD</option>
												<option value="BLUD">BLUD</option>
										  </select>
										</div>
									</div> -->
									<!-- <div class="col-xs-12 col-md-3">
										<div class="form-group">
											<label for="jenis">JENIS/KATEGORI</label>
											<select class="form-control" name="jenis" required>
												<option value="Generik">Generik</option>
												<option value="Non Generik">Non Generik</option>
												<option value="BHP">BHP</option>
												<option value="Narkotik">Narkotik</option>
												<option value="ADM">ADM</option>
											</select>
										</div>
                  </div>
                  <div class="col-xs-12 col-md-3">
										<div class="form-group">
		                  <label for="">Petugas Stock Opname</label><br>
		                  <select class="form-control select2" name="petugas" id="petugas" required>
												<?php
													foreach ($pegawai as $p) {
														echo '<option value="'.$p['id_pegawai'].'">'.$p['nama'].'</option>';
													}
												?>
		                  </select>
		                </div>
									</div> -->
                </div>
              </div>
              <div class="box-footer">
                <button type="submit" class="btn bg-purple"><i class="fa fa-search"></i> Cari Data</button>
              </div>
              </form>
            </div><!-- /.box -->
          </div>
        </div>

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
		<!-- Select2 -->
		<script src="../plugins/select2/select2.full.min.js"></script>
    <!-- bootstrap datepicker -->
    <script src="../plugins/datepicker/bootstrap-datepicker.js"></script>
    <!-- select2 -->
    <script src="../plugins/select2/select2.full.min.js"></script>
    <!-- DATA TABES SCRIPT -->
    <script src="../plugins/datatables/jquery.dataTables.js" type="text/javascript"></script>
    <script src="../plugins/datatables/dataTables.bootstrap.js" type="text/javascript"></script>
    <!-- SlimScroll -->
    <script src="../plugins/slimScroll/jquery.slimscroll.min.js" type="text/javascript"></script>
    <!-- FastClick -->
    <script src='../plugins/fastclick/fastclick.min.js'></script>
    <!-- AdminLTE App -->
    <script src="../dist/js/app.min.js" type="text/javascript"></script>
    <!-- page script -->
    <script type="text/javascript">
      $(function () {
        //Date picker
        $('#tanggal_so').datepicker({
          autoclose: true,
          format: "dd/mm/yyyy"
        });
				$("#penyimpanan").select2({
          placeholder : "Pilih Tempat Penyimpanan",
          allowClear: true,
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
      });
    </script>

  </body>
</html>
