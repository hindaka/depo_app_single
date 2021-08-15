<?php
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
$get_ranap = $db->query("SELECT ro.*,rp.nomedrek,rp.nama FROM rincian_obat_pasien ro INNER JOIN registerpasien rp ON(ro.id_pasien=rp.id_pasien) WHERE ro.status='apotek' AND ro.approval='n'");
$data_ranap = $get_ranap->fetchAll(PDO::FETCH_ASSOC)
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
      <?php } else if (isset($_GET['status']) && ($_GET['status'] == "3")) { ?><div class="alert alert-success alert-dismissable"><button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>
          <center>
            <h4><i class="icon fa fa-check"></i>Berhasil!</h4>Transaksi Resep Rawat Jalan Berhasil dibatalkan
          </center>
        </div>
      <?php } else if (isset($_GET['status']) && ($_GET['status'] == "4")) { ?><div class="alert alert-success alert-dismissable"><button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>
          <center>
            <h4><i class="icon fa fa-check"></i>Berhasil!</h4>Transaksi Resep Rawat Jalan Berhasil diselesaikan
          </center>
        </div>
      <?php } ?>
      <!-- end pesan -->
      <section class="content-header">
        <h1>
          Data Transaksi Depo Farmasi <?php echo $tipes[2]; ?>
          <small>Depo Farmasi <?php echo $tipes[2]; ?></small>
        </h1>
        <ol class="breadcrumb">
          <li><a href="index.php"><i class="fa fa-dashboard"></i> Home</a></li>
          <li class="active">Data Transaksi Depo Farmasi <?php echo $tipes[2]; ?></li>
        </ol>
      </section>

      <!-- Main content -->
      <section class="content">
        <div class="row">
          <div class="col-xs-12">
            <div class="box box-primary">
              <div class="box-header">
                <i class="fa fa-user"></i>
                <h3 class="box-title">Daftar Transaksi Depo Farmasi <?php echo $tipes[2]; ?> yang belum diselesaikan / Validasi</h3>
                <?php if ($tipes[2] == 'IGD') { ?>
                  <div class="pull-right">
                    <button onclick="window.location.href='obat_ranap.php'" class="btn btn-sm btn-primary"><i class="fa fa-plus"></i> Tambah Transaksi</button>
                  </div>
                <?php } ?>

              </div><!-- /.box-header -->
              <div class="box-body">
                <div class="table-responsive">
                  <table id="example1" class="table table-bordered table-striped" width="100%">
                    <thead>
                      <tr class="info">
                        <th>ID Transaksi</th>
                        <th>Tanggal Transaksi</th>
                        <th>Tanggal Daftar</th>
                        <th>Nomedrek</th>
                        <th>Nama Pasien</th>
                        <th>DPJP</th>
                        <th>Rincian</th>
                      </tr>
                    </thead>
                    <tbody>
                      <!-- <?php
                            foreach ($data_ranap as $rp) {
                              echo '<tr>
																	<td>' . $rp['id_rincian_obat'] . '</td>
																	<td>' . $rp['created_at'] . '</td>
																	<td>' . $rp['nomedrek'] . '</td>
																	<td>' . $rp['nama'] . '</td>
																	<td>' . $rp['dpjp'] . '</td>
																	<td>
																		<a href="obat_ranap_trans.php?id=' . $rp['id_rincian_obat'] . '" class="btn btn-primary btn-sm"><i class="fa fa-search"></i> Check</a>
																	</td>
																</tr>';
                            }
                            ?> -->
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
    <?php include "footer.php"; ?>
    <!-- /.static footer -->
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
    $(function() {
      var t = $('#example1').DataTable({
        "processing": true,
        "serverSide": true,
        "ajax": 'ajax_data/data_transaksi_ranap.php',
        "columns": [{
            "data": 'id_rincian_obat',
            "searchable": true,
            "render": function(data, type, row) {
              return data;
            }
          },
          {
            "data": "created_at",
            "searchable": true
          },
          {
            "data": "tanggaldaftar",
            "searchable": true
          },
          {
            "data": "nomedrek",
            "searchable": true
          },
          {
            "data": "nama",
            "searchable": true
          },
          {
            "data": "dpjp",
            "searchable": false
          },
          {
            "data": null,
            "searchable": false,
            "render": function(data, type, full, meta) {
              var btn = '<a target="_blank" href="obat_ranap_trans.php?id=' + data.id_rincian_obat + '" class="btn btn-block btn-sm btn-primary"><i class="fa fa-search"></i> Check</a>';
              return btn;
            }
          }
        ],
        "order": [
          [0, 'desc']
        ]
      });
    });
  </script>

</body>

</html>