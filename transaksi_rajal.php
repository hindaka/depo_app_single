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
$today = date('Ymd');
$hari_ini = date('d/m/Y');
$h7 = date('Ymd', strtotime($rangeDate));
$get_resep = $db->query("SELECT r.*,a.nama as petugas,r.id_order,r.jenis_transaksi,rp.jpasien FROM resep r INNER JOIN anggota a ON(a.mem_id=r.mem_id) LEFT JOIN apotek_order ap ON(r.id_order=ap.id_apotek_order) LEFT JOIN registerpasien rp ON(r.id_register=rp.id_pasien) WHERE r.ruang LIKE 'IGD' AND (r.statusbayar='Belum dibayar' OR r.statusbayar='') AND CAST( CONCAT(SUBSTRING(r.tgl_resep,7,4),SUBSTRING(r.tgl_resep,4,2),SUBSTRING(r.tgl_resep,1,2)) AS UNSIGNED) > '".$h7."' AND CAST( CONCAT(SUBSTRING(r.tgl_resep,7,4),SUBSTRING(r.tgl_resep,4,2),SUBSTRING(r.tgl_resep,1,2)) AS UNSIGNED)<='".$today."'");
$resep = $get_resep->fetchAll(PDO::FETCH_ASSOC);
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
		<?php } else if (isset($_GET['status']) && ($_GET['status'] == "3")) { ?><div class="alert alert-success alert-dismissable"><button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button><center><h4><i class="icon fa fa-check"></i>Berhasil!</h4>Transaksi Resep Rawat Jalan Berhasil dibatalkan</center></div>
	<?php } else if (isset($_GET['status']) && ($_GET['status'] == "4")) { ?><div class="alert alert-success alert-dismissable"><button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button><center><h4><i class="icon fa fa-check"></i>Berhasil!</h4>Transaksi Resep Rawat Jalan Berhasil diselesaikan</center></div>
	    <?php } ?>
	    <!-- end pesan -->
        <section class="content-header">
          <h1>
            Data Transaksi
            <small>Resep Rajal IGD</small>
          </h1>
          <ol class="breadcrumb">
            <li><a href="index.php"><i class="fa fa-dashboard"></i> Home</a></li>
            <li class="active">Data Transaksi Resep Rajal</li>
          </ol>
        </section>

        <!-- Main content -->
        <section class="content">
          <div class="row">
            <div class="col-xs-12">
              <div class="box box-primary">
                <div class="box-header">
                  <i class="fa fa-user"></i>
								  <h3 class="box-title">Daftar Transaksi Rajal IGD yang belum diselesaikan</h3>
									<div class="pull-right">
										<button onclick="window.location.href='resep.php'" class="btn btn-sm btn-primary"><i class="fa fa-plus"></i> Tambah Transaksi</button>
									</div>
                </div><!-- /.box-header -->
                <div class="box-body">
									<div class="table-responsive">
										<table id="example2" class="table table-bordered table-striped" width="100%">
	                    <thead>
	                      <tr class="info">
													<th>ID Transaksi</th>
													<th>Kategori</th>
													<th>Jenis Transaksi</th>
													<th>Tanggal Resep</th>
													<th>Ruangan</th>
													<th>No.Rekam Medis</th>
													<th>Nama Pasien</th>
													<th>Jenis Pasien</th>
													<th>Dokter</th>
													<th>Cara Bayar</th>
													<th>Status Pembayaran</th>
													<th>Petugas Farmasi</th>
													<th>Rincian</th>
	                      </tr>
	                    </thead>
											<tbody>
												<?php
													foreach ($resep as $row) {
														if($row['bayar']=='Non Tunai'){
															$task = 'proses_klaim';
														}else{
															$task = 'lunas';
														}
														if($row['tgl_resep']==$hari_ini){
															$links = '';
														}else{
															$links = '<a href="transaksi_selesai.php?id='.$row['id_resep'].'&task='.$task.'" class="btn btn-block btn-xs btn-primary"><i class="fa fa-money"></i> Selesai</a>';
														}
														if($row['jenis_transaksi']=='Resep'){
															$telaah_button = '<a href="resep_check_edit.php?id='.$row['id_resep'].'&trans=Resep" class="btn btn-block btn-xs bg-purple"><i class="fa fa-book"></i> Edit telaah</a>';
														}else{
															$telaah_button = "";
														}
														if($row['id_order']=='0'){
															$cat = '<label class="label label-primary">Resep Manual</label>';
														}else{
															$cat = '<label class="label bg-maroon">Resep Elektronik</label>';
														}
														if($row['jenis_transaksi']=='Bon'){
															$jenis = '<label class="label label-default">Bon</label>';
														}else{
															$jenis = '<label class="label label-success">Resep</label>';
														}
														if($row['jpasien']=='Umum'){
															$jp = '<span class="label label-success">Umum</span>';
														}else if($row['jpasien']=='karyawan'){
															$jp = '<span class="label label-info">Karyawan</span>';
														}else{
															$jp = '<span class="label label-info">'.$row['jpasien'].'</span>';
														}
														echo "<tr>
																		<td>#".$row['id_resep']."</td>
																		<td>".$cat."</td>
																		<td>".$jenis."</td>
																		<td>".$row['tgl_resep']."</td>
																		<td>".$row['ruang']."</td>
																		<td>".$row['nomedrek']."</td>
																		<td>".$row['nama']."</td>
																		<td>".$jp."</td>
																		<td>".$row['dokter']."</td>
																		<td>".$row['bayar']."</td>
																		<td><span class='label label-default'>".$row['statusbayar']."</span></td>
																		<td>".$row['petugas']."</td>
																		<td>
																			".$links."
																			<a class='btn btn-block btn-xs btn-warning' href='keluar_edit.php?id=".$row['id_resep']."'><i class=\"fa fa-pencil\"></i> Edit Transaksi</a>
																			".$telaah_button."
																			<a target='_blank' class='btn btn-block btn-xs btn-info' href='transaksi_rajal_lihat.php?id=".base64_encode($row['id_resep'])."'><i class=\"fa fa-search\"></i> Lihat Rincian</a>
																			<a class='btn btn-block btn-xs btn-danger' href='cancel_resep.php?id=".base64_encode($row['id_resep'])."'><i class=\"fa fa-trash\"></i> Batalkan</a>
																		</td>
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
