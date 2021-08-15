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
$id_conf_obat = isset($_GET['penyimpanan']) ? $_GET['penyimpanan'] : '';
$tanggal_so = isset($_GET['tanggal_so']) ? $_GET['tanggal_so'] : date("Y-m-d");
$split = explode("/",$tanggal_so);
$tahun = $split[2];
$get_penyimpanan = $db->query("SELECT * FROM conf_penyimpanan_obat WHERE id_conf_obat='".$id_conf_obat."'");
$header = $get_penyimpanan->fetch(PDO::FETCH_ASSOC);
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
		<style media="screen">
		.dataTables_wrapper .dataTables_processing {
				position: absolute;
				top: 30%;
				left: 50%;
				width: 30%;
				height: 40px;
				margin-left: -20%;
				margin-top: -25px;
				padding-top: 20px;
				text-align: center;
				font-size: 1.2em;
				background:none;
			}
		</style>
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
			<?php } else if (isset($_GET['status']) && ($_GET['status'] == "2")) { ?><div class="alert alert-success alert-dismissable"><button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button><center><h4><i class="icon fa fa-check"></i>Berhasil</h4>Data pasien telah diproses</center></div>
			<?php } else if (isset($_GET['status']) && ($_GET['status'] == "3")) { ?><div class="alert alert-success alert-dismissable"><button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button><center><h4><i class="icon fa fa-check"></i>Berhasil!</h4>Transaksi Resep Rawat Jalan Berhasil dibatalkan</center></div>
			<?php } else if (isset($_GET['status']) && ($_GET['status'] == "4")) { ?><div class="alert alert-success alert-dismissable"><button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button><center><h4><i class="icon fa fa-check"></i>Berhasil!</h4>Transaksi Resep Rawat Jalan Berhasil diselesaikan</center></div>
	    <?php } ?>
	    <!-- end pesan -->
        <section class="content-header">
          <h1>
            Data Stok Opnam
            <small><?php echo $header['nama_penyimpanan']; ?> ; Tanggal SO : <?php echo $tanggal_so; ?></small>
          </h1>
          <ol class="breadcrumb">
            <li><a href="index.php"><i class="fa fa-dashboard"></i> Home</a></li>
            <li class="active">Data Stok Opnam</li>
          </ol>
        </section>

        <!-- Main content -->
        <section class="content">
          <div class="row">
            <div class="col-xs-12">
              <div class="box box-primary">
                <div class="box-header">
                  <i class="fa fa-user"></i>
								  <h3 class="box-title">Daftar Stok Opnam <?php echo $header['nama_penyimpanan']; ?></h3>
									<div class="pull-right">
										<a href="stok_opnam_depo_proses.php?tanggal_so=<?php echo $tanggal_so; ?>&penyimpanan=<?php echo $id_conf_obat ?>" class="btn btn-sm btn-success"><i class="fa fa-send"></i> Proses & Cetak</a>
									</div>
                </div><!-- /.box-header -->
                <div class="box-body">
									<div class="table-responsive">
										<table class="table" id="example2">
											<thead>
												<tr class="bg-purple">
													<th>NO PENYIMPANAN</th>
													<th>ID OBAT</th>
													<th>NAMA OBAT</th>
													<th>SUMBER DANA</th>
													<th>NO BATCH</th>
													<th>EXPIRED</th>
													<th>HARGA BELI</th>
													<th>STOK SISTEM</th>
												</tr>
											</thead>
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
		var master_so;
      $(function () {
					master_so = $('#example2').DataTable({
						"processing" : true,
						"serverSide" : true,
						"ajax": "ajax_data/stok_opnam_depo_table.php?tahun=<?php echo $tahun; ?>&c=<?php echo $id_conf_obat; ?>",
						"pageLength": 100,
						"columns":[
							{"data":"no_urut_depo_igd"},
							{"data":"id_obat"},
							{"data":"nama"},
							{"data":"sumber_dana"},
							{"data":"no_batch"},
							{"data":"expired"},
							{"data":"harga_beli"},
							{"data":"sisa_stok"},
						],
						"language": {
		            "lengthMenu": "Menampilkan _MENU_ records per halaman",
		            "zeroRecords": "Data Tidak ditemukan - sorry",
		            "info": "Halaman _PAGE_ dari _PAGES_",
		            "infoEmpty": "No records available",
		            "infoFiltered": "(filtered from _MAX_ total records)",
								"search": "Pencarian : ",
								"loadingRecords": "Loading...",
						    "processing": '<i class="fa fa-spinner fa-spin fa-3x fa-fw"></i><span class="sr-only">Loading...</span> ',
		        },
						"order": [[ 0, "asc" ],[2,"asc"]]
					});
      });
    </script>

  </body>
</html>
