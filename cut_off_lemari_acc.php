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
$awal = isset($_GET['awal']) ? $_GET['awal'] : '';
$akhir = isset($_GET['akhir']) ? $_GET['akhir'] : '';
$id_conf_obat = isset($_GET['nama_penyimpanan']) ? $_GET['nama_penyimpanan'] : '';
$tipe_depo = $tipes[2];
$conf = json_decode(file_get_contents("config/env_depo.json"),true);
$id_depo = $conf[$tipe_depo]["id_depo"];
$get_data = $db->query("SELECT * FROM conf_penyimpanan_obat WHERE id_warehouse='".$id_depo."'");
$data_all = $get_data->fetch(PDO::FETCH_ASSOC);

$get_data = $db->query("SELECT g.no_urut_depo_igd,ks.id_obat,g.nama,g.jenis,gs.nama_bentuk,SUM(ks.volume_out) as jumlah,ks.expired,ks.no_batch,ks.sumber_dana FROM kartu_stok_ruangan ks INNER JOIN gobat g ON(ks.id_obat=g.id_obat) LEFT JOIN gobat_bentuk_sediaan gs ON(g.id_bentuk=gs.id_bentuk_sediaan) WHERE ks.id_warehouse='".$id_depo."' AND g.lemari_depo_igd='".$id_conf_obat."' AND ks.in_out='keluar' AND ks.created_at BETWEEN '".$awal."' AND '".$akhir."' GROUP BY ks.id_obat,ks.no_batch ORDER BY g.no_urut_depo_igd,g.id_obat ASC");
$data = $get_data->fetchAll(PDO::FETCH_ASSOC);
$db=NULL;
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
    <!-- DATA TABLES -->
    <link href="../plugins/datatables/dataTables.bootstrap.css" rel="stylesheet" type="text/css" />
		<link rel="stylesheet" href="../plugins/select2/select2.min.css">
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
	    <?php if (isset($_GET['status']) && ($_GET['status'] == "1")) { ?><div class="alert alert-success alert-dismissable"><button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button><center><h4><i class="icon fa fa-check"></i>Berhasil</h4>Data TPN berhasil ditambahkan</center></div>
			<?php } else if (isset($_GET['status']) && ($_GET['status'] == "2")) { ?><div class="alert alert-success alert-dismissable"><button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button><center><h4><i class="icon fa fa-check"></i>Berhasil</h4>Data pasien telah diproses</center></div>
			<?php } else if (isset($_GET['status']) && ($_GET['status'] == "3")) { ?><div class="alert alert-danger alert-dismissable"><button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button><center><h4><i class="icon fa fa-ban"></i>Peringatan!</h4>Data pasien gagal diubah</center></div>
	    <?php } ?>
	    <!-- end pesan -->
        <section class="content-header">
          <h1>
            CUT OFF
          </h1>
          <ol class="breadcrumb">
            <li><a href="index.php"><i class="fa fa-dashboard"></i> Home</a></li>
            <li class="active">CUT OFF BY LEMARI</li>
          </ol>
        </section>

        <!-- Main content -->
        <section class="content">
          <div class="row">
            <div class="col-xs-12">
              <div class="box box-primary">
                <div class="box-header">
                  <i class="fa fa-user"></i>
								  <h3 class="box-title">Data CUT OFF (<?php echo $data_all['nama_penyimpanan']; ?>)</h3>
									<div class="pull-right">
										<a class="btn btn-sm btn-primary" href="cut_off_lemari_export.php?awal=<?php echo $awal; ?>&akhir=<?php echo $akhir; ?>&penyimpanan=<?php echo $id_conf_obat; ?>"><i class="fa fa-download"></i> Export Data</a>
									</div>
                </div><!-- /.box-header -->
                <div class="box-body">
									<div class="table-responsive">
										<table id="example1" class="table table-bordered table-striped" width="100%">
	                    <thead>
	                      <tr class="info">
													<th>No Urut Lemari</th>
													<th>ID obat</th>
													<th>Nama Obat</th>
													<th>Jenis</th>
													<th>Nama Bentuk</th>
													<th>Jumlah</th>
													<th>Expired</th>
													<th>No Batch</th>
													<th>Sumber Dana</th>
	                      </tr>
	                    </thead>
											<tbody>
												<?php
													foreach ($data as $row) {
														echo  "<tr>
																		<td>".$row['no_urut_depo_igd']."</td>
																		<td>".$row['id_obat']."</td>
																		<td>".$row['nama']."</td>
																		<td>".$row['jenis']."</td>
																		<td>".$row['nama_bentuk']."</td>
																		<td>".$row['jumlah']."</td>
																		<td>".$row['expired']."</td>
																		<td>".$row['no_batch']."</td>
																		<td>".$row['sumber_dana']."</td>
																	</tr>";
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
		<script src="../plugins/select2/select2.full.min.js" type="text/javascript"></script>
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
				$('#example1').DataTable();
      });
    </script>

  </body>
</html>
