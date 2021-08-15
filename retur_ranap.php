<?php
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
$id_parent = isset($_GET['p']) ? $_GET['p'] : '';
$id_rincian_obat = isset($_GET['i']) ? $_GET['i'] : '';
$id_detail_rincian = isset($_GET['rincian']) ? $_GET['rincian'] : '';
$id_transaksi = isset($_GET['t']) ? $_GET['t'] : '';
// $kartu = isset($_GET['kartu']) ? $_GET['kartu'] : '';
//get nama obat
$sql_obat = $db->query("SELECT ro.id_obat,g.nama,ro.volume FROM rincian_detail_obat ro INNER JOIN gobat g ON(ro.id_obat=g.id_obat) WHERE ro.id_detail_rincian=".$id_detail_rincian);
$obat = $sql_obat->fetch(PDO::FETCH_ASSOC);
//get data pegawai
$get_peg = $db->query("SELECT * FROM pegawai WHERE id_depart='3'");
$pegawai = $get_peg->fetchAll(PDO::FETCH_ASSOC);
$get_petugas = $db->query("SELECT * FROM petugas pet INNER JOIN pegawai peg ON(pet.id_pegawai=peg.id_pegawai) WHERE pet.instalasi='FARMASI'");
$petugas = $get_petugas->fetchAll(PDO::FETCH_ASSOC);

?>
<!DOCTYPE html>
<html>
  <head>
    <meta charset="UTF-8">
    <title>SIMRS <?php echo $version_depo; ?> | <?php echo $tipes; ?></title>
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
		<!-- select2 -->
		<link href="../plugins/select2/select2.min.css" rel="stylesheet" type="text/css" />
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
            Retur Obat Ranap
            <small><?php echo $obat['nama']; ?></small>
          </h1>
          <ol class="breadcrumb">
            <li><a href="index.php"><i class="fa fa-dashboard"></i> Home</a></li>
            <li>Retur</li><li class="active">Retur Obat Ranap</li>
          </ol>
        </section>

        <!-- Main content -->
        <section class="content">
          <div class="box">
                <div class="box-header">
                  <i class="fa fa-user"></i>
				  <h3 class="box-title">Retur <i><?php echo $obat['nama']; ?></i></h3>
                </div><!-- /.box-header -->
				<!-- form start -->
                <form role="form" action="retur_ranap_save.php" method="post">
                  <div class="box-body">
									<div class="row">
										<input type="hidden" name="p" value="<?php echo $id_parent; ?>">
				            <input type="hidden" name="i" value="<?php echo $id_rincian_obat; ?>">
				            <input type="hidden" name="rincian" value="<?php echo $id_detail_rincian; ?>">
				            <input type="hidden" name="t" value="<?php echo $id_transaksi; ?>">
										<input type="hidden" name="id_obat" value="<?php echo $obat['id_obat']; ?>">
										<!-- <input type="hidden" name="kartu" value="<?php echo $kartu; ?>"> -->
										<!-- <div class="col-xs-3">
											<div class="form-group">
											  <label for="">Petugas yg meretur barang</label>
											  <select name="petugas_retur" id="petugas_retur" class="form-control select2" style="width:100%;" required>
											  	<option value=""></option>
													<?php
														foreach ($pegawai as $peg) {
															echo '<option value="'.$peg['nama'].'">'.$peg['nama'].'</option>';
														}
													?>
											  </select>
											</div>
										</div> -->
										<!-- <div class="col-xs-3">
											<div class="form-group">
											  <label for="">Petugas yg penerima barang</label>
											  <select name="petugas_penerima" id="petugas_penerima" class="form-control select2" style="width:100%;" required>
											  	<option value=""></option>
													<?php
														foreach ($petugas as $pet) {
															echo '<option value="'.$pet['nama'].'">'.$pet['nama'].'</option>';
														}
													?>
											  </select>
											</div>
										</div> -->
										<div class="col-xs-3">
											<div class="form-group">
											  <label for="">Volume obat yang keluar</label>
											  <input type="text" class="form-control" name="volume_out" id="volume_out" value="<?php echo $obat['volume']; ?>" readonly>
											</div>
										</div>
										<div class="col-xs-2">
											<div class="form-group">
												<label for="jumlah_retur">Jumlah Retur</label>
						            <input type="number" id="jumlah_retur" class="form-control" name="jumlah_retur" placeholder="Masukan Angka" min="1" max="<?php echo $obat['volume']; ?>" required>
											</div>
					          </div>
									</div>
                  </div><!-- /.box-body -->

				  			<div class="box-footer">
                  <button type="submit" class="btn btn-primary"><i class="fa fa-save"></i> Simpan</button>
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
	<!-- select2 -->
    <script src="../plugins/select2/select2.full.min.js" type="text/javascript"></script>
    <!-- FastClick -->
    <script src='../plugins/fastclick/fastclick.min.js'></script>
    <!-- AdminLTE App -->
    <script src="../dist/js/app.min.js" type="text/javascript"></script>
    <!-- page script -->
    <script type="text/javascript">
	  $(function () {
			$('.select2').select2({
				placeholder : 'Masukan Nama Petugas',
				allowClear : true,
				width : 'resolve'
			});
        $("#example1").dataTable();
      });
    </script>

  </body>
</html>
