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
$tipe_depo = $tipes[2];
$conf = json_decode(file_get_contents("config/env_depo.json"),true);
$id_depo = $conf[$tipe_depo]["id_depo"];
$get_data = $db->query("SELECT * FROM conf_penyimpanan_obat WHERE id_warehouse='".$id_depo."'");
$data_all = $get_data->fetchAll(PDO::FETCH_ASSOC);
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
			#loader{
				position: fixed;
				width: 100%;
				height: 100%;
				z-index: 9999;
				top: 0;
				left: 0;
				display: flex;
				align-items: center;
				justify-content: center;
				background-color: rgba(114, 106, 149, .5);
			}
			#loader.hidden{
				animation: fadeOut 1s;
				animation-fill-mode: forwards;
			}
			@keyframes fadeOut {
				100% {
					opacity: 0;
					visibility: hidden;
				}
			}
		</style>
  </head>
  <body class="<?php echo $skin_depo; ?>">
		<div id="loader">
			<img id="loading-image" src="../dist/img/refresh_loader.gif" alt="Loading..."/>
		</div>
    <div class="wrapper">

      <!-- static header -->
	  <?php include("header.php"); ?>
	  <?php include "menu_index.php"; ?>
      <!-- Content Wrapper. Contains page content -->
      <div class="content-wrapper">
        <!-- Content Header (Page header) -->
		<!-- pesan feedback -->
	    <?php if (isset($_GET['status']) && ($_GET['status'] == "1")) { ?><div class="alert alert-success alert-dismissable"><button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button><center><h4><i class="icon fa fa-check"></i>Berhasil</h4>Data Penyimpanan berhasil ditambahkan</center></div>
		<?php } else if (isset($_GET['status']) && ($_GET['status'] == "2")) { ?><div class="alert alert-success alert-dismissable"><button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button><center><h4><i class="icon fa fa-check"></i>Berhasil</h4>Data Penyimpanan berhasil diubah</center></div>
	<?php } else if (isset($_GET['status']) && ($_GET['status'] == "3")) { ?><div class="alert alert-warning alert-dismissable"><button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button><center><h4><i class="icon fa fa-warning"></i>Peringatan!</h4>Nama Penyimpanan Obat sudah terdaftar, Silakan gunakan Nama lain.</center></div>
<?php } else if (isset($_GET['status']) && ($_GET['status'] == "4")) { ?><div class="alert alert-success alert-dismissable"><button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button><center><h4><i class="icon fa fa-check"></i>Berhasil!</h4>Nama Penyimpanan Obat berhasil dihapus.</center></div>
<?php } else if (isset($_GET['status']) && ($_GET['status'] == "5")) { ?><div class="alert alert-danger alert-dismissable"><button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button><center><h4><i class="icon fa fa-warning"></i>Peringatan!</h4>Nama Penyimpanan Obat tidak dapat dihapus dikarenakan ada data obat yang terikat.</center></div>
	    <?php } ?>
	    <!-- end pesan -->
        <section class="content-header">
          <h1>
            Pengelolaan Penyimpanan Obat
          </h1>
          <ol class="breadcrumb">
            <li><a href="index.php"><i class="fa fa-dashboard"></i> Home</a></li>
            <li class="active">Pengelolaan Penyimpanan Obat</li>
          </ol>
        </section>

        <!-- Main content -->
        <section class="content">
          <div class="row">
            <div class="col-xs-12">
              <div class="box box-primary">
                <div class="box-header">
                  <i class="fa fa-user"></i>
								  <h3 class="box-title">Data Penyimpanan Obat <small>(sudah masuk dalam penyimpanan obat : <span id="inc_block">0</span>, belum masuk penyimpanan : <span id="ex_block">0</span> <a target="_blank" data-toggle="tooltip" title="klik disini untuk melihat data obat yang belum masuk" href="conf_ex_obat.php" class="btn btn-xs btn-danger"><i class="fa fa-search"></i></a>)</small></h3>
									<div class="pull-right">
										<a class="btn btn-primary" href="conf_penyimpanan_add.php?task=add"><i class="fa fa-plus"></i> Tambah Data</a>
									</div>
                </div><!-- /.box-header -->
                <div class="box-body">
									<div class="table-responsive">
										<table id="example1" class="table table-bordered table-striped" width="100%">
	                    <thead>
	                      <tr class="info">
													<th>Nama Penyimpanan</th>
													<th>Penanggungjawab</th>
													<th>Hapus</th>
	                      </tr>
	                    </thead>
											<tbody>
												<?php
													foreach ($data_all as $da) {
														echo '<tr>
																		<td>'.$da['nama_penyimpanan'].'</td>
																		<td>'.$da['pj'].'</td>
																		<td>
																			<a href="penyimpanan_obat.php?id='.$da['id_conf_obat'].'" class="btn btn-sm btn-primary"><i class="fa fa-plus"></i> Tambah Obat</a>
																			<a href="conf_penyimpanan_add.php?id='.$da['id_conf_obat'].'&task=edit" class="btn btn-sm btn-warning"><i class="fa fa-pencil"></i> Ubah Nama</a>
																			<a href="hapus_penyimpanan_obat.php?id='.$da['id_conf_obat'].'&task=edit" class="btn btn-sm btn-danger"><i class="fa fa-trash"></i> Hapus Nama</a>
																		</td>
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
		<script src="../plugins/sweetalert/sweetalert.min.js" type="text/javascript"></script>
    <!-- page script -->
		<script type="text/javascript">
      $(function () {
				$(window).on("load",function(){
					$.ajax({
	           type: "GET",
	           url: "ajax_data/get_total_penyimpanan_obat.php",
	           beforeSend: function() {
	              $("#loader").show();
								document.body.style.cursor="progress";
	           },
	           success: function(msg) {
							 var res = JSON.parse(msg)
							 console.log(res.inc)
							 $('#inc_block').text(res.inc);
							 $('#ex_block').text(res.ex);
							 document.body.style.cursor="default";
	              $("#loader").fadeOut();
	           }
					 })
				})
				$(document).on("click", ".opentpn", function () {
			     var tpnId = $(this).data('tpnid');
					 $(".modal-body #nomedrek").val('');
			     $(".modal-body #id_tpn").val(tpnId);
				});
				$('#kirimTpn').on('click',function(){
					var id_tpn = $('#id_tpn').val();
					var nomedrek = $('#nomedrek').val();
					var heparin = $('#heparin').val();
					window.location.href="cetak_tpn_barcode.php?tpn="+id_tpn+"&nomedrek="+nomedrek+"&heparin="+heparin;
				});
				$('#example1').DataTable();
      });
			function del_data(id_tpn){
				console.log(id_tpn);
				swal({
				  title: "Apakah anda yakin?",
				  text: "Data yang sudah dihapus tidak dapat dikembalikan!",
				  icon: "warning",
				  buttons: true,
				  dangerMode: true,
				})
				.then((willDelete) => {
				  if (willDelete) {
						var fd = new FormData();
						fd.append("id", id_tpn);
						$.ajax({
							type: 'POST',
							url: 'ajax_data/delete_tpn.php',
							data: fd,
							contentType: false,
							cache: false,
							processData:false,
							success: function(msg){
							 var response = JSON.parse(msg);
							 swal({
								  title: response.title,
								  text: response.text,
								  icon: response.icon,
								}).then((value)=>{
									window.location.href="tpn.php?status=2";
								})
							}
						});
				  }
				});
			}
    </script>

  </body>
</html>
