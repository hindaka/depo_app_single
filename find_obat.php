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
$keysearch = isset($_GET['keysearch']) ? trim($_GET['keysearch']) : '';
$crypt = isset($_GET['crypt']) ? base64_decode(trim($_GET['crypt'])) : 'false';
if($crypt=="true"){
  $in_out='masuk';
  $key = $keysearch."%";
  $get_data_depo = $db->prepare("SELECT ks.id_obat,ks.id_warehouse,SUM(ks.volume_kartu_akhir) as jumlah_akhir,w.nama_ruang,ks.sumber_dana,g.nama FROM kartu_stok_ruangan ks INNER JOIN gobat g ON(ks.id_obat=g.id_obat) INNER JOIN warehouse w ON(ks.id_warehouse=w.id_warehouse) WHERE (g.nama LIKE :key OR ks.merk LIKE :merk OR ks.pabrikan LIKE :pabrikan) AND in_out=:in_out GROUP BY id_obat,id_warehouse ASC");
  $get_data_depo->bindParam(":key",$key,PDO::PARAM_STR);
  $get_data_depo->bindParam(":merk",$keysearch,PDO::PARAM_STR);
  $get_data_depo->bindParam(":pabrikan",$keysearch,PDO::PARAM_STR);
  $get_data_depo->bindParam(":in_out",$in_out,PDO::PARAM_STR);
  $get_data_depo->execute();
  $data_depo = $get_data_depo->fetchAll(PDO::FETCH_ASSOC);
  $total_data_depo = $get_data_depo->rowCount();

  $get_stok_gudang = $db->prepare("SELECT kg.id_obat,kg.sumber_dana,SUM(kg.volume_kartu_akhir) as jumlah_akhir,g.nama FROM kartu_stok_gobat kg INNER JOIN gobat g ON(kg.id_obat=g.id_obat) WHERE (g.nama LIKE :key OR kg.merk=:merk OR kg.pabrikan=:pabrikan) AND kg.in_out=:in_out GROUP BY id_obat");
  $get_stok_gudang->bindParam(":key",$key,PDO::PARAM_STR);
  $get_stok_gudang->bindParam(":merk",$keysearch,PDO::PARAM_STR);
  $get_stok_gudang->bindParam(":pabrikan",$keysearch,PDO::PARAM_STR);
  $get_stok_gudang->bindParam(":in_out",$in_out,PDO::PARAM_STR);
  $get_stok_gudang->execute();
  $data_gudang = $get_stok_gudang->fetchAll(PDO::FETCH_ASSOC);
  $total_data_gudang= $get_stok_gudang->rowCount();

}else{

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
	  <?php include("menu_index.php"); ?>
      <div class="content-wrapper">
        <!-- Content Header (Page header) -->
        <section class="content-header">
          <h1>
            Pencarian Data Obat
          </h1>
          <ol class="breadcrumb">
            <li><a href="index.php"><i class="fa fa-dashboard"></i> Home</a></li>
          </ol>
        </section>

        <!-- Main content -->
        <section class="content">
          <div class="row">
            <div class="col-sm-12">
              <!-- Default box -->
              <div class="box box-success">
                <div class="box-header with-border">
                  <h3 class="box-title">Pencarian Data Obat</h3>
                </div>
                <div class="box-body">
                  <form class="form-inline" action="<?php echo $_SERVER['PHP_SELF']; ?>" method="get">
                    <div class="form-group">
                      <label for="">Masukan Nama Obat</label>
                      <div class="input-group input-group-md">
                        <input type="text" class="form-control" id="keysearch" name="keysearch" required>
                        <input type="hidden" name="crypt" value="<?php echo base64_encode("true"); ?>">
                        <span class="input-group-btn">
                          <button type="submit" class="btn btn-info btn-flat">CARI!</button>
                        </span>
                      </div>
                    </div>
                  </form>
                </div><!-- /.box-body -->
              </div><!-- /.box -->
            </div>
          </div>
          <?php
            if($crypt=='true'){ ?>

          <div class="row">
            <!-- left block -->
            <div class="col-sm-12 col-md-6">
              <div class="box box-success">
                <div class="box-header with-border">
                  <i class="fa fa-book"></i>
                  <h3 class="box-title">Data Obat di Depo</h3>
                </div>
                <div class="box-body">
                  <div class="table-responsive">
                    <table id="example1" class="table table-striped table-hover">
                      <thead>
                        <tr class="success">
													<th>Id Obat Farmasi</th>
                          <th>Nama Depo</th>
                          <th>Nama Obat</th>
                          <th>Jumlah</th>
                          <th>Sumber Dana</th>
                        </tr>
                      </thead>
                      <tbody>
                        <?php
                          if($total_data_depo>0){
                            foreach ($data_depo as $dd) {
                              echo '<tr>
															<td>'.$dd['id_obat'].'</td>
                              <td>'.$dd['nama_ruang'].'</td>
                              <td>'.$dd['nama'].'</td>
                              <td>'.$dd['jumlah_akhir'].'</td>
                              <td>'.$dd['sumber_dana'].'</td>
                              </tr>';
                            }
                          }
                        ?>
                      </tbody>
                    </table>
                  </div>
                </div>
              </div>
            </div>
            <!-- right block -->
            <div class="col-sm-12 col-md-6">
              <div class="box box-success">
                <div class="box-header with-border">
                  <i class="fa fa-book"></i>
                  <h3 class="box-title">Data Obat di Gudang Farmasi</h3>
                </div>
                <div class="box-body">
                  <div class="table-responsive">
                    <table id="example2" class="table table-striped table-hover">
                      <thead>
                        <tr class="success">
													<th>ID obat Gudang</th>
                          <th>Nama Obat</th>
                          <th>Jumlah</th>
                          <th>Sumber Dana</th>
                        </tr>
                      </thead>
                      <tbody>
                        <?php
                        if($total_data_gudang>0){
                          foreach ($data_gudang as $dd) {
                            echo '<tr>
														<td>'.$dd['id_obat'].'</td>
                            <td>'.$dd['nama'].'</td>
                            <td>'.$dd['jumlah_akhir'].'</td>
                            <td>'.$dd['sumber_dana'].'</td>
                            </tr>';
                          }
                        } ?>
                      </tbody>
                    </table>
                  </div>
                </div>
              </div>
            </div>
          </div>
          <?php } ?>
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
    <script src="../plugins/datatables/jquery.dataTables.js" type="text/javascript"></script>
    <script src="../plugins/datatables/dataTables.bootstrap.js" type="text/javascript"></script>
    <!-- SlimScroll -->
    <script src="../plugins/slimScroll/jquery.slimscroll.min.js" type="text/javascript"></script>
    <!-- FastClick -->
    <script src='../plugins/fastclick/fastclick.min.js'></script>
    <!-- AdminLTE App -->
    <script src="../dist/js/app.min.js" type="text/javascript"></script>
    <!-- page script -->
    <script type="text/javascript">
      $(function () {
        $("#example1").dataTable();
        $("#example2").dataTable();
      });
    </script>

  </body>
</html>
