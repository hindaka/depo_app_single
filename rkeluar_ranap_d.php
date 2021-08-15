<?php
//conn
session_start();
include("../inc/pdo.conf.php");
include("../inc/version.php");
ini_set('display_errors',1);
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
$id_rincian_obat= isset($_GET['d']) ? $_GET['d'] : '';
$bulan = isset($_GET['bulan']) ? $_GET['bulan'] : '';
$tahun = isset($_GET['tahun']) ? $_GET['tahun'] : '';
$tipe_depo = $tipes[2];
$conf = json_decode(file_get_contents("config/env_depo.json"),true);
$id_depo = $conf[$tipe_depo]["id_depo"];
//mysql data obat
$h2=$db->query("SELECT io.id_rincian_obat,io.created_at,rp.nama,rp.nomedrek,rp.alamat,io.status FROM rincian_obat_pasien io INNER JOIN registerpasien rp ON(io.id_pasien=rp.id_pasien) WHERE io.id_rincian_obat=".$id_rincian_obat);
$head = $h2->fetch(PDO::FETCH_ASSOC);
$detail = $db->query("SELECT ro.id_trans_obat,od.id_detail_rincian,a.nama,a.sumber,od.sub_total,od.volume,od.created_at FROM rincian_detail_obat od INNER JOIN rincian_transaksi_obat ro ON(od.id_trans_obat=ro.id_trans_obat) INNER JOIN gobat a ON(od.id_obat=a.id_obat) WHERE ro.id_warehouse='".$id_depo."' AND ro.id_rincian_obat=".$id_rincian_obat." AND a.jenis<>'BHP'");
$list_bhp = $db->query("SELECT ro.id_trans_obat,od.id_detail_rincian,a.nama,a.sumber,od.sub_total,od.volume,od.created_at FROM rincian_detail_obat od INNER JOIN rincian_transaksi_obat ro ON(od.id_trans_obat=ro.id_trans_obat) INNER JOIN gobat a ON(od.id_obat=a.id_obat) WHERE ro.id_warehouse='".$id_depo."' AND ro.id_rincian_obat=".$id_rincian_obat." AND a.jenis='BHP'");
$retur  = $db->query("SELECT ro.*,rd.id_trans_obat FROM rincian_retur_obat ro INNER JOIN rincian_detail_obat rd ON(rd.id_detail_rincian=ro.id_detail_rincian) WHERE rd.id_rincian=".$id_rincian_obat);
//function pembulatan 50 -> 100
function pembulatan($rupiah){
	$puluhan = substr($rupiah,-2);
	if($puluhan < 50){
		$akhir = $rupiah - $puluhan;
	}else{
		$akhir = $rupiah + (100-$puluhan);
	}
	return $akhir;
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
		<?php } else if (isset($_GET['status']) && ($_GET['status'] == "2")) { ?><div class="alert alert-success alert-dismissable"><button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button><center><h4><i class="icon fa fa-check"></i>Berhasil</h4>Data pasien telah diproses</center></div>
	<?php } else if (isset($_GET['status']) && ($_GET['status'] == "3")) { ?><div class="alert alert-danger alert-dismissable"><button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button><center><h4><i class="icon fa fa-ban"></i>Peringatan!</h4>Data pasien gagal diubah</center></div>
	    <?php } ?>
	    <!-- end pesan -->
        <section class="content-header">
          <h1>
            Daftar Rekapitulasi
            <small>Obat Keluar Depo Farmasi <?php echo $tipes[2]; ?></small>
          </h1>
          <ol class="breadcrumb">
            <li><a href="index.php"><i class="fa fa-dashboard"></i> Home</a></li>
            <li class="active">Daftar rekapitulasi Obat Keluar Depo Farmasi <?php echo $tipes[2]; ?></li>
          </ol>
        </section>

        <!-- Main content -->
        <section class="content">
          <div class="row">
            <div class="col-xs-12">
              <div class="box box-primary">
                <div class="box-header">
                  <i class="fa fa-user"></i>
				  <h3 class="box-title">Rincian Obat Apotek Keluar Depo Farmasi <?php echo $tipes[2]; ?></h3>
                </div><!-- /.box-header -->
                <div class="box-body">
                  <table class="table table-bordered">
                    <tr>
                      <th class="info">No Rekam Medis</th>
                      <td><?php echo $head['nomedrek']; ?></td>
                    </tr>
                    <tr>
                      <th class="info">Nama Pasien</th>
                      <td><?php echo $head['nama']; ?></td>
                    </tr>
                    <tr>
                      <th class="info">Alamat</th>
                      <td><?php echo $head['alamat']; ?></td>
                    </tr>
                    <tr>
                      <?php
                      if($head['status']=='lunas'){
                        echo "<tr class='success'>
                                <th>Status</th>
                                <td>".$head['status']."</td>
                              </tr>";
                      }else{
                        echo "<tr class='warning'>
                                <th>Status</th>
                                <td>".$head['status']."</td>
                              </tr>";
                      }
                       ?>
                    </tr>
                  </table>
                  <br>
                  <hr>
									<div class="row">
										<div class="col-md-12">
						          <!-- Custom Tabs -->
						          <div class="nav-tabs-custom">
						            <ul class="nav nav-tabs">
						              <li class="active"><a href="#tab_1" data-toggle="tab"><b>Rincian Obat Keluar</b></a></li>
													<li><a href="#tab_2" data-toggle="tab"><b>Rincian BMHP Keluar</b></a></li>
						              <li><a href="#tab_3" data-toggle="tab"><b>Rincian Obat Retur</b></a></li>
						              <!-- <li><a href="#tab_3" data-toggle="tab">Tab 3</a></li> -->
						              <!-- <li class="dropdown">
						                <a class="dropdown-toggle" data-toggle="dropdown" href="#">
						                  Dropdown <span class="caret"></span>
						                </a>
						                <ul class="dropdown-menu">
						                  <li role="presentation"><a role="menuitem" tabindex="-1" href="#">Action</a></li>
						                  <li role="presentation"><a role="menuitem" tabindex="-1" href="#">Another action</a></li>
						                  <li role="presentation"><a role="menuitem" tabindex="-1" href="#">Something else here</a></li>
						                  <li role="presentation" class="divider"></li>
						                  <li role="presentation"><a role="menuitem" tabindex="-1" href="#">Separated link</a></li>
						                </ul>
						              </li> -->
						              <!-- <li class="pull-right"><a href="#" class="text-muted"><i class="fa fa-gear"></i></a></li> -->
						            </ul>
						            <div class="tab-content">
						              <div class="tab-pane active" id="tab_1">
														<table id="example2" class="table table-bordered table-striped">
					                    <thead>
					                      <tr class="info">
					                        <th>No</th>
					            						<th>Tanggal Keluar</th>
																	<th>ID Transaksi</th>
					            						<th>Nama Obat</th>
					            						<th>Volume</th>
					            						<th>Sumber</th>
																	<th>Sub Total</th>
																	<th>Etiket Obat</th>
					                      </tr>
					                    </thead>
					                    <tbody>
					                      <?php
					                      $no=1;
																$total_obat = 0;
																$t_uang = 0;
																$details = $detail->fetchAll(PDO::FETCH_ASSOC);
																foreach ($details as $data) {
																	$new_date = date('d-m-Y H:i:s',strtotime($data['created_at']));
																	$total_obat += $data['volume'];
																	$t_uang += $data['sub_total'];
					                        echo "<tr>
					                            <td>".$no++."</td>
					                            <td>".$new_date."</td>
																			<td>".$data['id_trans_obat']."</td>
					                            <td>".$data['nama']."</td>
					                            <td>".$data['volume']."</td>
					                            <td>".$data['sumber']."</td>
																			<td>Rp.".number_format($data['sub_total'],0,',','.')."</td>
																			<td><a class='btn btn-warning btn-sm' target='_blank' href='etiket_ranap.php?i=".$id_rincian_obat."&rincian=".$data['id_detail_rincian']."&b=".$bulan."&t=".$tahun."'>Etiket</a></td>
					                        </tr>";
																}
					                      ?>
					                    </tbody>
															<tfoot>
																<tr class="info">
																	<th colspan="4">Total Volume Obat Keluar</th>
																	<th colspan="2"><?php echo $total_obat; ?></th>
																	<th colspan="2">Rp.<?php echo number_format($t_uang,0,',','.'); ?></th>
																</tr>
															</tfoot>
					                  </table>
						              </div>
						              <!-- /.tab-pane -->
													<!-- block 3 -->
													<div class="tab-pane" id="tab_2">
														<table id="example3" class="table table-bordered table-striped">
					                    <thead>
					                      <tr class="info">
					                        <th>No</th>
					            						<th>Tanggal Keluar</th>
																	<th>ID Transaksi</th>
					            						<th>Nama</th>
					            						<th>Volume</th>
					            						<th>Sumber</th>
																	<th>Sub Total</th>
																	<th>Etiket Obat</th>
					                      </tr>
					                    </thead>
					                    <tbody>
					                      <?php
					                      $no=1;
																$total_obat = 0;
																$t_uang = 0;
																$list_bhps = $list_bhp->fetchAll(PDO::FETCH_ASSOC);
																foreach ($list_bhps as $b) {
																	$new_date = date('d-m-Y H:i:s',strtotime($b['created_at']));
																	$total_obat += $b['volume'];
																	$t_uang += $b['sub_total'];
					                        echo "<tr>
					                            <td>".$no++."</td>
					                            <td>".$new_date."</td>
																			<td>".$b['id_trans_obat']."</td>
					                            <td>".$b['nama']."</td>
					                            <td>".$b['volume']."</td>
					                            <td>".$b['sumber']."</td>
																			<td>Rp.".number_format($b['sub_total'],0,',','.')."</td>
																			<td><a class='btn btn-warning btn-sm' target='_blank' href='etiket_ranap.php?i=".$id_rincian_obat."&d=".$b['id_detail_rincian']."&b=".$bulan."&t=".$tahun."'>Etiket</a></td>
					                        </tr>";
																}
					                      ?>
					                    </tbody>
															<tfoot>
																<tr class="info">
																	<th colspan="4">Total Volume Obat Keluar</th>
																	<th colspan="2"><?php echo $total_obat; ?></th>
																	<th colspan="2">Rp.<?php echo number_format($t_uang,0,',','.'); ?></th>
																</tr>
															</tfoot>
					                  </table>
						              </div>
						              <div class="tab-pane" id="tab_3">
														<table id="example2" class="table table-bordered table-striped">
					                    <thead>
					                      <tr class="info">
					                        <th>No</th>
					            						<th>Tanggal Retur</th>
																	<th>ID Transaksi</th>
					            						<th>Nama Obat</th>
					            						<th>Volume</th>
																	<th>Sub Total</th>
					                      </tr>
					                    </thead>
					                    <tbody>
					                      <?php
					                      $no=1;
																$total_obat = 0;
																$total_ret = 0;
																$ret_uang = 0;
																$t_uang = 0;
																$returs = $retur->fetchAll(PDO::FETCH_ASSOC);
																foreach ($returs as $ret) {
																	$ret_date = date('d-m-Y H:i:s',strtotime($ret['created_at']));
																	$total_ret += $ret['jumlah_retur'];
																	$ret_uang += $ret['harga_tuslah'];
					                        echo "<tr>
					                            <td>".$no++."</td>
					                            <td>".$ret_date."</td>
																			<td>".$ret['id_trans_obat']."</td>
					                            <td>".$ret['nama_obat']."</td>
					                            <td>".$ret['jumlah_retur']."</td>
																			<td>Rp.".number_format($ret['harga_tuslah'],0,',','.')."</td>
					                        </tr>";
																}
					                      ?>
					                    </tbody>
															<tfoot>
																<tr class="info">
																	<th colspan="4">Total Volume Obat Retur</th>
																	<th><?php echo $total_ret; ?></th>
																	<th>Rp.<?php echo number_format($ret_uang,0,',','.'); ?></th>
																</tr>
															</tfoot>
					                  </table>
						              </div>
						            </div>
						            <!-- /.tab-content -->
						          </div>
						          <!-- nav-tabs-custom -->
						        </div> <!-- end tabs -->
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
        $('#example2').dataTable();
				$('#example3').dataTable();
        // var t = $('#example1').DataTable({
        //             "processing": true,
        //             "serverSide": true,
        //             "ajax": 'teskeluar.php?gabung=<?php echo $gabung; ?>',
        //             "columns": [
        //                 {"data": "tanggal"},
        //                 {"data": "namaobat"},
        //                 {"data": "sumber"},
        //                 {"data": "volume"}
        //             ],
        //             "order": [[0, 'asc']]
        //         });
      });
    </script>

  </body>
</html>
