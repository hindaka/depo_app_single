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
$id_rincian_obat = isset($_GET['i']) ? $_GET['i'] : '';
$id_kartu_ruangan = isset($_GET['id_kartu_ruangan']) ? $_GET['id_kartu_ruangan'] : '';
$id_transaksi = isset($_GET['t']) ? $_GET['t'] : '';
$id_detail_rincian = isset($_GET['rincian']) ? $_GET['rincian'] : '';

//get nama obat
$sql_obat = $db->query("SELECT expired,no_batch,nama_obat FROM rincian_detail_obat ro WHERE ro.id_detail_rincian='" . $id_detail_rincian . "'");
$obat = $sql_obat->fetch(PDO::FETCH_ASSOC);
//get data etiket_apotek
$etiket_q = $db->query("SELECT * FROM etiket_apotek WHERE id_detail_rincian='" . $id_detail_rincian . "' ORDER BY id_etiket DESC LIMIT 1");
$etiket = $etiket_q->fetch(PDO::FETCH_ASSOC);
$total_etiket = $etiket_q->rowCount();
if ($total_etiket > 0) {
  $expire_date = isset($etiket['expired_date']) ? $etiket['expired_date'] : '';
  $no_batch = isset($etiket['no_batch']) ? $etiket['no_batch'] : '';
  $sehari_x = isset($etiket['sehari_x']) ? $etiket['sehari_x'] : '';
  $takaran = isset($etiket['takaran']) ? $etiket['takaran'] : '';
  $diminum = isset($etiket['diminum']) ? $etiket['diminum'] : '';
  $petunjuk_khusus = isset($etiket['petunjuk_khusus']) ? $etiket['petunjuk_khusus'] : '';
} else {
  $expire_date = isset($obat['expired']) ? $obat['expired'] : '';
  $no_batch = isset($obat['no_batch']) ? $obat['no_batch'] : '';
  $sehari_x = isset($obat['sehari_x']) ? $obat['sehari_x'] : '';
  $takaran = isset($obat['takaran']) ? $obat['takaran'] : '';
  $diminum = isset($obat['diminum']) ? $obat['diminum'] : '';
  $petunjuk_khusus = isset($obat['petunjuk_khusus']) ? $obat['petunjuk_khusus'] : '';
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
  <!-- daterange picker -->
  <link href="../plugins/datepicker/datepicker3.css" rel="stylesheet" type="text/css" />
  <!-- iCheck for checkboxes and radio inputs -->
  <link href="../plugins/iCheck/all.css" rel="stylesheet" type="text/css" />
  <!-- BootsrapSelect -->
  <link href="../plugins/bootstrap-select/bootstrap-select.min.css" rel="stylesheet" type="text/css" />
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
      <?php if (isset($_GET['status']) && ($_GET['status'] == "1")) { ?><div class="alert alert-success alert-dismissable"><button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>
          <center>
            <h4><i class="icon fa fa-check"></i>Berhasil</h4>Data resep telah diinput
          </center>
        </div>
      <?php } else if (isset($_GET['status']) && ($_GET['status'] == "2")) { ?><div class="alert alert-success alert-dismissable"><button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>
          <center>
            <h4><i class="icon fa fa-check"></i>Berhasil</h4>Data pasien telah diproses
          </center>
        </div>
      <?php } else if (isset($_GET['status']) && ($_GET['status'] == "3")) { ?><div class="alert alert-danger alert-dismissable"><button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>
          <center>
            <h4><i class="icon fa fa-ban"></i>Peringatan!</h4>Data resep tidak boleh kosong
          </center>
        </div>
      <?php } ?>
      <!-- end pesan -->
      <section class="content-header">
        <h1>
          Cetak
          <small>etiket</small>
        </h1>
        <ol class="breadcrumb">
          <li><a href="index.php"><i class="fa fa-dashboard"></i> Home</a></li>
          <li>Cetak</li>
          <li class="active">etiket</li>
        </ol>
      </section>

      <!-- Main content -->
      <section class="content">
        <div class="box box-primary">
          <div class="box-header">
            <i class="fa fa-user"></i>
            <h3 class="box-title">Etiket <i><?php echo $obat['nama_obat']; ?></i></h3>
          </div><!-- /.box-header -->
          <!-- form start -->
          <?php $url = "cetak_etiket_ranap.php?i=" . $id_rincian_obat . "&d=" . $id_detail_rincian; ?>
          <?php $url_save = "save_etiket_ranap.php?i=" . $id_rincian_obat . "&d=" . $id_detail_rincian; ?>
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
                  <input type="text" class="form-control" id="edate" name="edate" value="<?php echo substr($expire_date, 0, 10); ?>" required>
                </div>
                <div class="col-xs-2">
                  <label for="ed">No Batch</label>
                  <input type="text" class="form-control" id="no_batch" name="no_batch" value="<?php echo $no_batch; ?>" required>
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
    <?php include "footer.php"; ?>
    <!-- /.static footer -->
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
      $("#nomedrek").change(function() {
        if ($(this).val() == "-") {
          $("#namaValue").show(500);
        } else {
          $("#namaValue").hide(500);
        }
      });
      $("#namaValue").hide();
    });
    $(function() {
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
    //Date range picker
    $('#tanggalr').datepicker({
      format: 'dd/mm/yyyy',
      todayHighlight: true,
      autoclose: true
    });
    //typeahead picker ruang
    $('#ruang').typeahead({
      source: ['IGD', 'VK', 'NIFAS 3', 'NIFAS 4', 'PERINATOLOGI', 'RUANG ANAK', 'POLI ANAK', 'POLI KANDUNGAN', 'ICU', 'OK']
    });
    //typeahead picker dokter
    $('#dokter').typeahead({
      source: ['dr. Siti Mardiani, SpA', 'dr. Ratnaningsih, SpA', 'dr. Ike Ernawati', 'dr. Septiani, M.Kes', 'dr. Nenden Leilawati, SpA', 'dr. Busye, SpAn', 'dr. Agnes, SpOG', 'dr. Djoelaika', 'dr. Riza Prihadi, SpA', 'dr. Nova Dianthy', 'dr. Ogi Dewangga, SpOG', 'dr. Nuning', 'dr. Ira Hastuti', 'dr. Rima Yulia, SpOG', 'dr. Ita Fatati, SpOG', 'dr. Diana Indriani', 'dr. Ari Wiyanti', 'dr. Dwi Sutrisno', 'dr. Afriani Altis', 'dr. Indah Mona', 'dr. Berlian', 'dr. Hesti', 'dr. Husna Lathifa', 'dr. Martin Hermawan, SpOG', 'dr. Lia Nazliah, SpA', 'dr. Dewi, SpAn', 'dr. Nenny Gustiani, SpPK', 'dr. Hidayat, SpRad', 'dr. Rena Nurita', 'dr. Alinda Hartini']
    });
  </script>

</body>

</html>