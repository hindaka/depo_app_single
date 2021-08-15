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
$bulan = isset($_GET['bulan']) ? $_GET['bulan'] : date('m');
$tahun = isset($_GET['tahun']) ? $_GET['tahun'] : date('Y');
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
            <small> Bulan : <?php echo $bulan; ?>, Tahun : <?php echo $tahun; ?></small>
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
								  <h3 class="box-title">Daftar Stok Opnam</h3> <small> Bulan : <?php echo $bulan; ?>, Tahun : <?php echo $tahun; ?></small>
                </div><!-- /.box-header -->
                <div class="box-body">
									<div class="table-responsive">
										<table class="table" id="example2">
											<thead>
												<tr class="bg-purple">
													<th>TANGGAL SO</th>
													<th>PENYIMPANAN</th>
													<th>PETUGAS SO</th>
													<th>STATUS</th>
													<th>AKSI</th>
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
						"ajax": "ajax_data/stok_opnam_depo_list.php?bulan=<?php echo $bulan; ?>&tahun=<?php echo $tahun; ?>",
						"pageLength": 10,
						"columns":[
							{"data":"tanggal_so"},
							{"data":"penyimpanan"},
							{"data":"nama"},
							{
								"data":"koreksi_status",
								"render": function(data,type,row){
									var check;
									if(data=='y'){
										check='<span style="font-size:20px;"><i class="fa fa-check"></i></span>';
									}else{
										check='<span style="font-size:20px;"><i class="fa fa-times"></i></span>';
									}
									return check;
								}
							},
							{
								"data":null,
								"render": function(data,type,row){
									var btn;
									if(data.koreksi_status=='y'){
										btn = '<button class="btn btn-block btn-sm btn-success disabled"><i class="fa fa-pencil"></i> Koreksi SO</button>';
										btn +='<a href="stok_opnam_depo_cetak.php?ctx='+data.id_so+'" target="_blank" class="btn btn-block btn-sm bg-purple"><i class="fa fa-print"></i> Cetak Form SO</a>';
									}else{
										btn = '<a href="stok_opnam_depo_koreksi_edit.php?koreksi='+data.id_so+'" class="btn btn-block btn-sm btn-primary"><i class="fa fa-check"></i> Koreksi SO</a>';
										btn +='<a href="stok_opnam_depo_cetak.php?ctx='+data.id_so+'" target="_blank" class="btn btn-block btn-sm bg-purple"><i class="fa fa-print"></i> Cetak Form SO</a>';
									}
									return btn;
								}
							},
						],
						"language": {
		            "lengthMenu": "Menampilkan _MENU_ records per halaman",
		            "zeroRecords": "Maaf Data Tidak ditemukan",
		            "info": "Halaman _PAGE_ dari _PAGES_",
		            "infoEmpty": "No records available",
		            "infoFiltered": "(filtered from _MAX_ total records)",
								"search": "Pencarian : ",
								"loadingRecords": "Loading...",
						    "processing": '<i class="fa fa-spinner fa-spin fa-3x fa-fw"></i><span class="sr-only">Loading...</span> ',
		        },
					});
					// "order": [[ 0, "asc" ],[2,"asc"]]
      });
    </script>

  </body>
</html>
