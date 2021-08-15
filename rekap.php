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
//mysql data resep

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
	    <?php if (isset($_GET['status']) && ($_GET['status'] == "1")) { ?><div class="alert alert-success alert-dismissable"><button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button><center><h4><i class="icon fa fa-check"></i>Berhasil</h4>Data resep telah diupdate</center></div>
			<?php } else if (isset($_GET['status']) && ($_GET['status'] == "2")) { ?><div class="alert alert-success alert-dismissable"><button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button><center><h4><i class="icon fa fa-check"></i>Berhasil</h4>Data pasien telah diproses</center></div>
			<?php } else if (isset($_GET['status']) && ($_GET['status'] == "3")) { ?><div class="alert alert-danger alert-dismissable"><button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button><center><h4><i class="icon fa fa-ban"></i>Peringatan!</h4>Data pasien gagal diubah</center></div>
	    <?php } ?>
	    <!-- end pesan -->
        <section class="content-header">
          <h1>
            Daftar
            <small>rekapitulasi</small>
          </h1>
          <ol class="breadcrumb">
            <li><a href="index.php"><i class="fa fa-dashboard"></i> Home</a></li>
            <li class="active">Daftar rekapitulasi</li>
          </ol>
        </section>

        <!-- Main content -->
        <section class="content">
          <div class="row">
            <div class="col-xs-12">
              <div class="box box-primary">
                <div class="box-header">
                  <i class="fa fa-user"></i>
				  <h3 class="box-title">Data Rekapitulasi Resep </h3>
                </div><!-- /.box-header -->
                <div class="box-body">
									<div class="table-responsive">
										<table id="example1" class="table table-bordered table-striped table-hover" width="100%">
	                    <thead>
	                      <tr class="info">
            							<th>No. Transaksi</th>
            							<th>Tanggal Resep</th>
            							<th>No. Medrek</th>
            							<th>Nama</th>
            							<th>Dokter</th>
            							<th>Ruang</th>
            							<th>Pembayaran</th>
            							<th>Petugas Farmasi</th>
            							<th>Status</th>
            							<th>Etiket</th>
            							<th>Edit</th>
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
    <script src="../plugins/datatables2/jquery.dataTables.js" type="text/javascript"></script>
    <script src="../plugins/datatables2/dataTables.bootstrap.js" type="text/javascript"></script>
    <!-- SlimScroll -->
    <script src="../plugins/slimScroll/jquery.slimscroll.min.js" type="text/javascript"></script>
    <!-- FastClick -->
    <script src='../plugins/fastclick/fastclick.min.js'></script>
    <!-- AdminLTE App -->
    <script src="../dist/js/app.min.js" type="text/javascript"></script>
    <!-- page script -->
    <script type="text/javascript">
      $(function () {
        var t = $('#example1').DataTable({
                    "processing": true,
                    "serverSide": true,
                    "ajax": 'ajax_data/rekap_resep_rajal.php',
                    "columns": [
											{
												"data" : 'id_resep',
												"searchable" : true,
												"render" : function(data,type,row){
													return data;
												}
											},
                        {"data": "tgl_resep", "searchable": false},
                        {"data": "nomedrek", "searchable": false},
                        {"data": "nama_pasien", "searchable": true},
                        {"data": "dokter", "searchable": false},
                        {"data": "ruang", "searchable": false},
                        {"data": "bayar", "searchable": false},
												{"data": "nama_petugas", "searchable": true},
                        {
													"data": "statusbayar",
													"searchable": true,
													"render" : function(data, type, full, meta){
														var txt;
														if(data=='Belum dibayar'){
															txt='<span class="label label-warning"><i class="fa fa-warning"></i> Belum Dibayar</span>';
														}else if(data=='Sudah dibayar'){
															txt='<span class="label label-success"><i class="fa fa-check"></i> Sudah Dibayar</span>';
														}else if(data=='Batal'){
															txt='<span class="label label-danger"><i class="fa fa-times"></i> Batal</span>';
														}else if(data=='Proses Klaim'){
															txt='<span class="label label-primary"><i class="fa fa-check"></i> Proses Klaim</span>';
														}else{
															txt='<span class="label label-warning">-</span>';
														}
														return txt;
													}
												},
												{
													"data": "id_resep",
													"searchable": false,
													"render": function ( data, type, full, meta ) {
														return '<a class=\"btn btn-block btn-primary btn-xs\" href="lihat.php?id='+btoa(data)+'"><i class="fa fa-search"></i> Cetak</a>';
														}
												},
												{
													"data": null,
													"searchable": false,
													"render": function ( data, type, full, meta ) {
														var btn;
														if(data.statusbayar=='Belum dibayar'){
															btn  = '<a class=\"btn btn-block btn-warning btn-xs\" href="keluar_edit.php?id='+data.id_resep+'&trans='+data.jenis_transaksi+'"><i class="fa fa-pencil"></i> Edit Transaksi</a>';
															btn +='<a class=\"btn btn-block btn-xs bg-purple\" href="resep_check.php?id='+data.id_resep+'&trans='+data.jenis_transaksi+'"><i class="fa fa-book"></i> Edit Telaah</a>';
														}else if(data.statusbayar=='Batal'){
															btn  = '<a class=\"btn btn-block btn-danger btn-xs\"><i class="fa fa-lock"></i> Dikunci</a>';
														}else{
															btn  = '<a class=\"btn btn-block btn-success btn-xs\"><i class="fa fa-lock"></i> Dikunci</a>';
														}
														return btn;
													}
												}
                    ],
                    "order": [[0, 'desc']]
                });
      });
    </script>

  </body>
</html>
