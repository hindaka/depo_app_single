<?php
//conn
session_start();
include("../inc/pdo.conf.php");
include("../inc/version.php");
date_default_timezone_set("Asia/Jakarta");
$namauser = $_SESSION['namauser'];
$password = $_SESSION['password'];
$tipe = $_SESSION['tipe'];
$tipes = explode('-', $tipe);
if ($tipes[0] != 'DepoApp') {
  unset($_SESSION['tipe']);
  unset($_SESSION['namauser']);
  unset($_SESSION['password']);
  header("location:../index.php?status=2");
  exit;
}
include "../inc/anggota_check.php";
$id_obat = isset($_GET['id']) ? $_GET['id'] : '';
$id_warehouse = isset($_GET['ware']) ? $_GET['ware'] : '';
//mysql data obat
$h2 = $db->query("SELECT * FROM gobat WHERE id_obat='" . $id_obat . "'");
$head = $h2->fetch(PDO::FETCH_ASSOC);
$bulan = date('m');
$tahun = date('Y');
//get list kartu Persediaan
$kartu_list = $db->query("SELECT * FROM kartu_stok_ruangan WHERE id_obat='" . $id_obat . "' AND id_warehouse='" . $id_warehouse . "' ORDER BY created_at DESC");
$kartu = $kartu_list->fetchAll(PDO::FETCH_ASSOC);
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
      <?php if (isset($_GET['status']) && ($_GET['status'] == "1")) { ?><div class="alert alert-success alert-dismissable"><button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>
          <center>
            <h4><i class="icon fa fa-check"></i>Berhasil</h4>Data obat telah diupdate
          </center>
        </div>
      <?php } else if (isset($_GET['status']) && ($_GET['status'] == "2")) { ?><div class="alert alert-success alert-dismissable"><button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>
          <center>
            <h4><i class="icon fa fa-check"></i>Berhasil</h4>Data pasien telah diproses
          </center>
        </div>
      <?php } else if (isset($_GET['status']) && ($_GET['status'] == "3")) { ?><div class="alert alert-danger alert-dismissable"><button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>
          <center>
            <h4><i class="icon fa fa-ban"></i>Peringatan!</h4>Data pasien gagal diubah
          </center>
        </div>
      <?php } ?>
      <!-- end pesan -->
      <section class="content-header">
        <h1>
          Daftar
          <small>stok obat</small>
        </h1>
        <ol class="breadcrumb">
          <li><a href="index.php"><i class="fa fa-dashboard"></i> Home</a></li>
          <li class="active">Daftar obat</li>
        </ol>
      </section>

      <!-- Main content -->
      <section class="content">
        <div class="row">
          <div class="col-xs-12">
            <div class="box box-primary">
              <div class="box-header">
                <i class="fa fa-tasks"></i>
                <h3 class="box-title">Kartu Persediaan <?php echo $head['nama']; ?></h3>
                <!-- <a target="_blank" onclick="window.location.href='export_kartu_persediaan.php?id=<?php echo $id_obat; ?>&ware=<?php echo $id_warehouse; ?>'" class="btn btn-success pull-right"><i class="fa fa-download"></i> Export Data</a> -->
              </div><!-- /.box-header -->
              <div class="box-body">
                <table id="example1" class="table table-bordered">
                  <thead>
                    <tr class="info">
                      <th>Tanggal Pencatatan</th>
                      <th>Sumber Dana</th>
                      <th>Proses</th>
                      <th>Tujuan</th>
                      <th>Volume Masuk</th>
                      <th>Volume Keluar</th>
                      <th>Expiry Date</th>
                      <th>No Batch</th>
                      <th>Merk</th>
                      <th>Jenis</th>
                      <th>Pabrikan</th>
                      <th>Harga Satuan</th>
                      <th>Harga Jual</th>
                      <th>Total Penjualan</th>
                      <th>Keterangan</th>
                    </tr>
                  </thead>
                  <tbody>
                    <?php
                    foreach ($kartu as $row) {
                      if ($row['in_out'] == 'masuk') {
                        $bgcolor = "success";
                        $total_penjualan = 0;
                      } else {
                        $bgcolor = "warning";
                        $total_penjualan = $row['harga_jual'];
                      }
                      $split = explode('/', $row['keterangan']);
                      $total = count($split);
                      if ($total > 2) {
                        if ($split[2] == 'netto') {
                          $harga_jual = $row['harga_beli'];
                        } else {
                          $harga_jual = $row['harga_jual'];
                        }
                      } else {
                        $harga_jual = $row['harga_jual'];
                      }
                      if($row['sumber_dana']=='APBD'){
                        $sd ='<span class="label bg-maroon">APBD</span>';
                      }else if($row['sumber_dana']=='BLUD'){
                        $sd ='<span class="label bg-primary">BLUD</span>';
                      }else{

                      }
                      echo "<tr class='" . $bgcolor . "'>
                              <td>" . $row['created_at'] . "</td>
                              <td>".$sd."</td>  
                              <td><b>" . ucwords($row['in_out']) . "</b></td>
                              <td>" . $row['tujuan'] . "</td>
                              <td>" . $row['volume_in'] . "</td>
                              <td>" . $row['volume_out'] . "</td>
                              <td>" . substr($row['expired'], 0, 10) . "</td>
                              <td>" . $row['no_batch'] . "</td>
                              <td>" . $row['merk'] . "</td>
                              <td>" . $row['jenis'] . "</td>
                              <td>" . $row['pabrikan'] . "</td>
                              <td>Rp " . number_format($row['harga_beli'], 4, ',', '.') . "</td>
                              <td>Rp " . number_format($harga_jual, 4, ',', '.') . "</td>
                              <td>Rp " . number_format($total_penjualan, 4, ',', '.') . "</td>
                              <td>" . $row['keterangan'] . "</td>
                            </tr>";
                    }
                    ?>
                  </tbody>
                </table>
              </div><!-- /.box-body -->
            </div><!-- /.box -->
          </div><!-- /.col -->
        </div><!-- /.row -->
      </section><!-- /.content -->
    </div><!-- /.content-wrapper -->
    <!-- static footer -->
    <?php include "footer.php"; ?>
    <!-- /.static footer -->
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
    $(function() {
      $("#example1").dataTable();
    });
  </script>

</body>

</html>