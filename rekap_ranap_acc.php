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
$bulan=isset($_GET['bulan']) ? $_GET['bulan'] : date('m');
$tahun=isset($_GET['tahun']) ? $_GET['tahun'] : date('Y');
$tipe_depo = $tipes[2];
$conf = json_decode(file_get_contents("config/env_depo.json"),true);
$id_depo = $conf[$tipe_depo]["id_depo"];
//mysql data obat
$result = $db->query("SELECT ag.nama as petugas_input,r.id_detail_rincian,SUBSTRING(r.created_at,1,10) as tgl,g.id_obat,g.nama,g.jenis,g.sumber,r.volume,ri.dpjp,r.ruang,ri.id_pasien,IF(rp.jpasien='Umum','Umum','BPJS') as cara_bayar,rp.nama as namapasien
FROM `rincian_detail_obat` r INNER JOIN gobat g ON(g.id_obat=r.id_obat)
INNER JOIN rincian_transaksi_obat ro ON(r.id_trans_obat=ro.id_trans_obat)
INNER JOIN rincian_obat_pasien ri ON(ri.id_rincian_obat=ro.id_rincian_obat)
INNER JOIN registerpasien rp ON(rp.id_pasien=ri.id_pasien)
LEFT JOIN anggota ag ON(ag.mem_id=r.mem_id)
WHERE ro.id_warehouse='".$id_depo."' AND MONTH(r.created_at)='".$bulan."' AND YEAR(r.created_at)='".$tahun."'");
$data = $result->fetchAll(PDO::FETCH_ASSOC);
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
				  <h3 class="box-title">Data Rekapitulasi Obat Keluar Depo Farmasi <?php echo $tipes[2]; ?> (<?php echo $bulan."/".$tahun; ?>)</h3>
					<a href="export_keluar_ranap.php?bulan=<?php echo $bulan; ?>&tahun=<?php echo $tahun; ?>" class="btn btn-md btn-success pull-right" target="_blank"><i class='fa fa-download'></i> Export to excel</a>
                </div><!-- /.box-header -->
                <div class="box-body">
									<div class="table-responsive">
										<table id="example1" class="table table-bordered table-striped">
	                    <thead>
	                      <tr class="info">
													<th>Tanggal Keluar</th>
													<th>Nama Obat</th>
													<th>Jenis</th>
													<th>Sumber</th>
													<th>Volume</th>
													<th>Dokter</th>
													<th>Ruang</th>
													<th>Cara Bayar</th>
													<th>Pasien</th>
													<th>Petugas Input</th>
	                      </tr>
	                    </thead>
											<tbody>
												<?php
												foreach ($data as $row) {
													echo "<tr>
															<td>".$row['tgl']."</td>
															<td>".$row['nama']."</td>
															<td>".$row['jenis']."</td>
															<td>".$row['sumber']."</td>
															<td>".$row['volume']."</td>
															<td>".$row['dpjp']."</td>
															<td>".$row['ruang']."</td>
															<td>".$row['cara_bayar']."</td>
															<td>".$row['namapasien']."</td>
															<td>".$row['petugas_input']."</td>
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
		$(function(){
			$("#example1").dataTable();
		});

    </script>

  </body>
</html>
