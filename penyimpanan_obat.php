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
$id_conf_obat = isset($_GET['id']) ? $_GET['id'] : '';
$tipe_depo = $tipes[2];
$conf = json_decode(file_get_contents("config/env_depo.json"),true);
$id_depo = $conf[$tipe_depo]["id_depo"];
$get_conf = $db->query("SELECT nama_penyimpanan FROM conf_penyimpanan_obat WHERE id_conf_obat='".$id_conf_obat."'");
$header = $get_conf->fetch(PDO::FETCH_ASSOC);
$content_data = $db->query("SELECT cp.*,g.nama,g.sumber FROM conf_detail_penyimpanan cp INNER JOIN conf_penyimpanan_obat cf ON(cp.id_conf_obat=cf.id_conf_obat) INNER JOIN gobat g ON(cp.id_obat=g.id_obat) WHERE cf.id_conf_obat='".$id_conf_obat."'");
$content_all = $content_data->fetchAll(PDO::FETCH_ASSOC);
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
	    <?php if (isset($_GET['status']) && ($_GET['status'] == "1")) { ?><div class="alert alert-success alert-dismissable"><button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button><center><h4><i class="icon fa fa-check"></i>Berhasil</h4>Data Penyimpanan berhasil ditambahkan</center></div>
		<?php } else if (isset($_GET['status']) && ($_GET['status'] == "2")) { ?><div class="alert alert-success alert-dismissable"><button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button><center><h4><i class="icon fa fa-check"></i>Berhasil</h4>Data Penyimpanan berhasil diubah</center></div>
	<?php } else if (isset($_GET['status']) && ($_GET['status'] == "3")) { ?><div class="alert alert-warning alert-dismissable"><button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button><center><h4><i class="icon fa fa-warning"></i>Peringatan!</h4>Nama Penyimpanan Obat sudah terdaftar, Silakan gunakan Nama lain.</center></div>
<?php } else if (isset($_GET['status']) && ($_GET['status'] == "4")) { ?><div class="alert alert-success alert-dismissable"><button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button><center><h4><i class="icon fa fa-check"></i>Berhasil!</h4>Data Penyimpanan Obat Berhasil dihapus</center></div>
	    <?php } ?>
	    <!-- end pesan -->
        <section class="content-header">
          <h1>
            Pengelolaan Obat <?php echo $header['nama_penyimpanan']; ?>
          </h1>
          <ol class="breadcrumb">
            <li><a href="index.php"><i class="fa fa-dashboard"></i> Home</a></li>
            <li class="active">Pengelolaan Obat <?php echo $header['nama_penyimpanan']; ?></li>
          </ol>
        </section>

        <!-- Main content -->
        <section class="content">
          <div class="row">
						<div class="col-xs-12">
							<div class="box box-primary">
								<div class="box-header">
									<i class="fa fa-list"></i>
									<h3 class="box-title">Form Input Obat</h3>
								</div>
								<div class="box-body">
									<input type="hidden" name="id_conf_obat" id="id_conf_obat" value="<?php echo $id_conf_obat; ?>">
									<div class="form-group">
									  <label for="">Nama Obat</label>
									  <select name="id_obat" id="id_obat" class="form-control select2">
									  	<option value=""></option>
									  </select>
									</div>
								</div>
								<div class="box-footer">
									<button id="submitObat" type="button" class="btn btn-success btn-md"><i class="fa fa-plus"></i> Tambah Obat</button>
								</div>
							</div>
						</div>
            <div class="col-xs-12">
              <div class="box box-primary">
                <div class="box-header">
                  <i class="fa fa-user"></i>
								  <h3 class="box-title">Data Obat di <?php echo $header['nama_penyimpanan']; ?></h3>
									<div class="pull-right">
										<a class="btn btn-sm bg-purple" target="_blank" href="export_config_lemari.php?conf=<?php echo $id_conf_obat; ?>"><i class="fa fa-download"></i> Export To Excel</a>
									</div>
                </div><!-- /.box-header -->
                <div class="box-body">
									<div class="table-responsive">
										<table id="example1" class="table table-bordered table-striped" width="100%">
	                    <thead>
	                      <tr class="info">
													<th>Id Obat</th>
													<th>Nama Obat</th>
													<th>Sumber</th>
													<th>Hapus</th>
	                      </tr>
	                    </thead>
											<tbody>
												<?php
													foreach ($content_all as $c) {
														echo '<tr>
																		<td>'.$c['id_obat'].'</td>
																		<td>'.$c['nama'].'</td>
																		<td>'.$c['sumber'].'</td>
																		<td><a href="hapus_detail_penyimpanan_obat.php?master='.$c['id_conf_obat'].'&id='.$c['id_conf_detail'].'" class="btn btn-sm btn-danger"><i class="fa fa-trash"></i></a></td>
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
		<!-- select2 -->
    <script src="../plugins/select2/select2.full.min.js" type="text/javascript"></script>
    <!-- FastClick -->
    <script src='../plugins/fastclick/fastclick.min.js'></script>
    <!-- AdminLTE App -->
    <script src="../dist/js/app.min.js" type="text/javascript"></script>
		<script src="../plugins/sweetalert/sweetalert.min.js" type="text/javascript"></script>
    <!-- page script -->
		<script type="text/javascript">
      $(function () {
				$(".select2").select2({
				  ajax: {
				    url: "ajax_data/get_data_obat.php",
				    dataType: 'json',
				    delay: 250,
				    data: function (params) {
							// console.log(params);
				      return {
				        q: params.term, // search term
				        page: params.page
				      };
				    },
				    processResults: function (data, params) {
							// console.log(params);
							// console.log(data);
				      // parse the results into the format expected by Select2
				      // since we are using custom formatting functions we do not need to
				      // alter the remote JSON data, except to indicate that infinite
				      // scrolling can be used
				      params.page = params.page || 1;

				      return {
				        results: data.items,
				        pagination: {
				          more: (params.page * 30) < data.total_count
				        }
				      };
				    },
				    cache: true
				  },
				  placeholder: 'Masukan Nama Obat',
				  minimumInputLength: 2,
				  templateResult: formatRepo,
				  templateSelection: formatRepoSelection
				});

				function formatRepo (repo) {
				  if (repo.loading) {
				    return repo.text;
				  }
					var $state = repo.text;
					return $state;
				}

				function formatRepoSelection (repo) {
				  return repo.text;
				}
				$('#submitObat').on("click",function(e){
					e.preventDefault();
					var id_obat = $('#id_obat').val();
					var id_conf_obat = $('#id_conf_obat').val();
					if(id_obat==''){
						swal({
							 title: 'Peringatan!!',
							 text: 'Data Obat Belum dipilih!!',
							 icon: 'warning',
						 });
					}else{
						var fd = new FormData();
						fd.append("id", id_obat);
						fd.append("id_conf_obat", id_conf_obat);
						$.ajax({
							type: 'POST',
							url: 'ajax_data/add_penyimpanan_obat.php',
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
									window.location.href="penyimpanan_obat.php?id="+id_conf_obat;
								})
							}
						});
					}
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
