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
//ambil data filter
if(file_exists('config/waktu_filter.txt')){
	$myData = file_get_contents('config/waktu_filter.txt');
	$rangeDate = "-".(int)$myData." days";
}else{
	$rangeDate = 0;
}
$tipe_depo = $tipes[2];
$conf = json_decode(file_get_contents("config/env_depo.json"),true);
$id_depo = $conf[$tipe_depo]["id_depo"];
$today = date('Ymd');
$hari_ini = date('d/m/Y');
$h7 = date('Ymd', strtotime($rangeDate));
$get_keluar = $db->query("SELECT b.*,w.nama_ruang,a.nama as nama_petugas,peg.nama as nama_pegawai FROM `barangkeluar_depo` b LEFT JOIN warehouse w ON(b.id_warehouse=w.id_warehouse) LEFT JOIN anggota a ON(b.mem_id=a.mem_id) LEFT JOIN pegawai peg ON(b.permintaan=peg.id_pegawai) WHERE b.status_keluar='draft' AND b.asal_warehouse='".$id_depo."'");
$data_keluar = $get_keluar->fetchAll(PDO::FETCH_ASSOC);
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
	    <?php if (isset($_GET['status']) && ($_GET['status'] == "1")) { ?><div class="alert alert-success alert-dismissable"><button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button><center><h4><i class="icon fa fa-check"></i>Berhasil</h4>Data obat telah diupdate</center></div>
		<?php } else if (isset($_GET['status']) && ($_GET['status'] == "2")) { ?><div class="alert alert-success alert-dismissable"><button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button><center><h4><i class="icon fa fa-check"></i>Berhasil</h4>Data Transaksi Antar Depo Berhasil dibatalkan</center></div>
	    <?php } ?>
	    <!-- end pesan -->
        <section class="content-header">
          <h1>
            Data Transaksi Antar Depo
            <!-- <small></small> -->
          </h1>
          <ol class="breadcrumb">
            <li><a href="index.php"><i class="fa fa-dashboard"></i> Home</a></li>
            <li class="active">Data Transaksi Antar Depo</li>
          </ol>
        </section>

        <!-- Main content -->
        <section class="content">
          <div class="row">
            <div class="col-xs-12">
              <div class="box box-primary">
                <div class="box-header">
                  <i class="fa fa-user"></i>
								  <h3 class="box-title">Daftar Transaksi Antar Depo yang belum diselesaikan</h3>
									<div class="pull-right">
										<button onclick="window.location.href='transaksi_depo_input.php'" class="btn btn-sm btn-primary"><i class="fa fa-plus"></i> Tambah Transaksi</button>
									</div>
                </div><!-- /.box-header -->
                <div class="box-body">
									<div class="table-responsive">
										<table id="example2" class="table table-bordered table-striped" width="100%">
	                    <thead>
	                      <tr class="info">
													<th>Tanggal Permintaan</th>
													<th>Permintaan</th>
													<th>Pemesan</th>
													<th>Petugas Depo</th>
													<th>Aksi</th>
	                      </tr>
	                    </thead>
											<tbody>
												<?php
													foreach ($data_keluar as $row) {
														echo '<tr>
																		<td>'.$row['tanggal_permintaan'].'</td>
																		<td>'.$row['nama_ruang'].'</td>
																		<td>'.$row['nama_pegawai'].'</td>
																		<td>'.$row['nama_petugas'].'</td>
																		<td><a class="btn btn-sm btn-warning" href="input_permintaan_depo.php?parent='.$row['id_barangkeluar_depo'].'"><i class="fa fa-pencil"></i> Lanjutkan</a></td>
																	</tr>';
													}
												?>
											</tbody>
	                  </table>
									</div>
                </div><!-- /.box-body -->
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
    <!-- page script -->
		<script type="text/javascript">
		var master_resep;
      $(function () {
				$('#example2').DataTable();
      });
    </script>

  </body>
</html>
