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
$id_rincian = isset($_GET['id']) ? $_GET['id'] : '';
$id_parent_retur = isset($_GET['p']) ? $_GET['p'] : '';
$id_transaksi = isset($_GET['t']) ? $_GET['t'] : '';
$task = isset($_GET['task']) ? $_GET['task'] : '';
$f_today = date("Y-m-d");
$setRuang = isset($_GET['r']) ? $_GET['r'] : '';
$transaksi = "id_trans/" . $id_transaksi;
$tipe_depo = $tipes[2];
$conf = json_decode(file_get_contents("config/env_depo.json"), true);
$id_depo = $conf[$tipe_depo]["id_depo"];
$get_pasien = $db->query("SELECT rp.nama,ro.dpjp FROM rincian_obat_pasien ro INNER JOIN registerpasien rp ON(ro.id_pasien=rp.id_pasien) WHERE ro.id_rincian_obat='" . $id_rincian . "'");
$pasien = $get_pasien->fetch(PDO::FETCH_ASSOC);
$get_parent = $db->query("SELECT * FROM parent_retur_obat WHERE id_parent_retur='" . $id_parent_retur . "'");
$head_parent = $get_parent->fetch(PDO::FETCH_ASSOC);
// get list obat
$list_obat = $db->query("SELECT wo.id_kartu_ruangan,ro.id_detail_rincian,wo.created_at,ro.id_obat,ro.ruang,ro.volume,g.nama,g.flag_single_id,ro.jenis,ro.merk,ro.pabrikan,rt.id_trans_obat FROM rincian_detail_obat ro INNER JOIN rincian_transaksi_obat rt ON(ro.id_trans_obat=rt.id_trans_obat) INNER JOIN gobat g ON(ro.id_obat=g.id_obat) LEFT JOIN warehouse_out wo ON(ro.id_detail_rincian=wo.id_detail_rincian) WHERE ro.id_rincian='" . $id_rincian . "' AND ro.volume>0 AND ro.ruang='" . $head_parent['ruangan'] . "'  AND rt.id_warehouse='" . $id_depo . "' ORDER BY ro.created_at ASC");
$obat_l = $list_obat->fetchAll(PDO::FETCH_ASSOC);
//function pembulatan 50 -> 100
// function pembulatan($rupiah){
// 	$puluhan = substr($rupiah,-2);
// 	if($puluhan < 50){
// 		$akhir = $rupiah - $puluhan;
// 	}else{
// 		$akhir = $rupiah + (100-$puluhan);
// 	}
// 	return $akhir;
// }
// include("save_detail_ranap.php");

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
  <!-- daterange picker -->
  <link href="../plugins/datepicker/datepicker3.css" rel="stylesheet" type="text/css" />
  <!-- BootsrapSelect -->
  <link href="../plugins/bootstrap-select/bootstrap-select.min.css" rel="stylesheet" type="text/css" />
  <!-- iCheck for checkboxes and radio inputs -->
  <link href="../plugins/iCheck/all.css" rel="stylesheet" type="text/css" />
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
            <h4><i class="icon fa fa-check"></i>Berhasil</h4>Data obat telah diinput
          </center>
        </div>
      <?php } else if (isset($_GET['status']) && ($_GET['status'] == "2")) { ?><div class="alert alert-danger alert-dismissable"><button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>
          <center>
            <h4><i class="icon fa fa-ban"></i>Peringatan!</h4>Data obat tidak ditemukan
          </center>
        </div>
      <?php } else if (isset($_GET['status']) && ($_GET['status'] == "3")) { ?><div class="alert alert-danger alert-dismissable"><button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>
          <center>
            <h4><i class="icon fa fa-ban"></i>Peringatan!</h4>Stok obat tidak mencukupi
          </center>
        </div>
      <?php } else if (isset($_GET['status']) && ($_GET['status'] == "4")) { ?><div class="alert alert-danger alert-dismissable"><button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>
          <center>
            <h4><i class="icon fa fa-ban"></i>Peringatan!</h4>Data Gagal diinput ! Silakan hubungi divisi IT.
          </center>
        </div>
      <?php } else if (isset($_GET['status']) && ($_GET['status'] == "5")) { ?><div class="alert alert-success alert-dismissable"><button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>
          <center>
            <h4><i class="icon fa fa-check"></i>Berhasil!</h4>Obat berhasil dihapus dari rincian.
          </center>
        </div>
      <?php } else if (isset($_GET['status']) && ($_GET['status'] == "6")) { ?><div class="alert alert-danger alert-dismissable"><button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>
          <center>
            <h4><i class="icon fa fa-ban"></i>Peringatan!</h4>Gagal melakukan penghapusan obat dari rincian ! Silakan hubungi divisi IT.
          </center>
        </div>
      <?php } else if (isset($_GET['status']) && ($_GET['status'] == "7")) { ?><div class="alert alert-success alert-dismissable"><button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>
          <center>
            <h4><i class="icon fa fa-check"></i>Berhasil!</h4>Retur Obat Berhasil dilakukan.
          </center>
        </div>
      <?php } else if (isset($_GET['status']) && ($_GET['status'] == "8")) { ?><div class="alert alert-danger alert-dismissable"><button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>
          <center>
            <h4><i class="icon fa fa-check"></i>Berhasil!</h4>Retur Obat Gagal.
          </center>
        </div>
      <?php } ?>
      <!-- end pesan -->
      <section class="content-header">
        <h1>
          Rincian Transaksi Obat Pasien
          <small><?php echo $pasien['nama']; ?></small>
        </h1>
        <ol class="breadcrumb">
          <li><a href="index.php"><i class="fa fa-dashboard"></i> Home</a></li>
          <li>Rincian Transaksi Obat Pasien</li>
          <li class="active"><?php echo $pasien['nama']; ?></li>
        </ol>
      </section>

      <!-- Main content -->
      <section class="content">
        <div class="alert bg-purple">Pilih Obat yang akan diretur.</div>
        <div class="row">
          <div class="col-md-12">
            <div class="box box-primary">
              <div class="box-header">
                <i class="fa fa-medkit"></i>
                <h3 class="box-title">Data Transaksi Retur</h3>
              </div>
              <div class="box-body">
                <div class="table-responsive">
                  <table class="table table-hover table-bordered">
                    <tr>
                      <th class="info" style="width:20%">Tanggal Retur</th>
                      <th><?php echo $head_parent['tanggal_retur']; ?></th>
                    </tr>
                    <tr>
                      <th class="info" style="width:20%">Petugas Retur</th>
                      <th><?php echo $head_parent['petugas_retur']; ?></th>
                    </tr>
                    <tr>
                      <th class="info" style="width:20%">Ruangan Retur</th>
                      <th><?php echo $head_parent['ruangan']; ?></th>
                    </tr>
                  </table>
                </div>
              </div>
            </div>
          </div>
          <!-- right column -->
          <div class="col-md-12">
            <div class="box box-primary">
              <div class="box-header">
                <i class="fa fa-medkit"></i>
                <h3 class="box-title">Data Obat pasien yang dapat diretur</h3>
              </div><!-- /.box-header -->
              <!-- form start -->
              <div class="box-body">
                <table id="example1" class="table table-striped">
                  <thead>
                    <tr class="info">
                      <th>Tanggal Input</th>
                      <th>Ruangan</th>
                      <th>Nama</th>
                      <th>Jenis</th>
                      <th>Merk/Pabrikan</th>
                      <th>Volume</th>
                      <th>aksi</th>
                    </tr>
                  </thead>
                  <tbody>
                    <?php
                    $nomor = 1;
                    foreach ($obat_l as $r) {
                      $new_data = date('Y-m-d', strtotime($r['created_at']));
                      $jenis = isset($r['jenis']) ? $r['jenis'] : '';
                      $merk = isset($r['merk']) ? $r['merk'] : '';
                      $pabrikan = isset($r['pabrikan']) ? $r['pabrikan'] : '';
                      if ($jenis == 'generik') {
                        $merk_pabrikan = $pabrikan;
                      } else if ($jenis == 'non generik') {
                        $merk_pabrikan = $merk;
                      } else if ($jenis == 'bmhp') {
                        if ($merk != '') {
                          $merk_pabrikan = $merk;
                        } else {
                          $merk_pabrikan = $pabrikan;
                        }
                      } else {
                        $merk_pabrikan = $pabrikan;
                      }
                      echo "<tr>
                                <td>" . $new_data . "</td>
																<td>" . $r['ruang'] . "</td>
                                <td>" . $r['nama'] . "</td>
																<td>" . $r['jenis'] . "</td>
																<td>" . $merk_pabrikan . "</td>
                                <td>" . $r['volume'] . "</td>
                                <td>
																	<a class='btn btn-warning' href='retur_ranap.php?p=" . $id_parent_retur . "&i=" . $id_rincian . "&t=" . $r['id_trans_obat'] . "&kartu=" . $r['id_kartu_ruangan'] . "&rincian=" . $r['id_detail_rincian'] . "'><i class='fa fa-undo'></i> Retur</a>
																</td>
                              </tr>";
                    }
                    // <li><a href='retur_ranap.php?i=".$id_rincian."&t=".$id_transaksi."&kartu=".$r['id_kartu_ruangan']."&rincian=".$r['id_detail_rincian']."'><i class='fa fa-undo'></i>Retur</a></li>
                    ?>
                  </tbody>
                </table>
              </div>
              <div class="box-footer">
                <?php
                //update total_harga
                // $update_total = $db->query("UPDATE rincian_transaksi_obat SET biaya_trans=".$total_biaya." WHERE id_trans_obat=".$id_transaksi);
                ?>
                <a href="obat_ranap_retur_save.php?p=<?php echo $id_parent_retur; ?>&id=<?php echo $id_rincian; ?>&t=<?php echo $id_transaksi; ?>" class="btn btn-app"><i class="fa fa-save"></i> Simpan </a>
              </div>
            </div><!-- /.right column -->
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
  <!-- BootsrapSelect -->
  <script src="../plugins/bootstrap-select/bootstrap-select.min.js" type="text/javascript"></script>
  <!-- DATA TABES SCRIPT -->
  <script src="../plugins/datatables/jquery.dataTables.js" type="text/javascript"></script>
  <script src="../plugins/datatables/dataTables.bootstrap.js" type="text/javascript"></script>
  <!-- SlimScroll -->
  <script src="../plugins/slimScroll/jquery.slimscroll.min.js" type="text/javascript"></script>
  <!-- typeahead -->
  <script src="../plugins/typeahead/typeahead.bundle.js" type="text/javascript"></script>
  <!-- iCheck 1.0.1 -->
  <script src="../plugins/iCheck/icheck.min.js" type="text/javascript"></script>
  <!-- FastClick -->
  <script src='../plugins/fastclick/fastclick.min.js'></script>
  <!-- AdminLTE App -->
  <script src="../dist/js/app.min.js" type="text/javascript"></script>
  <!-- page script -->
  <script type="text/javascript">
    //Flat red color scheme for iCheck
    $('input[type="radio"].flat-blue').iCheck({
      radioClass: 'iradio_flat-blue'
    });
    $(function() {
      $("#example1").dataTable();

      function my_alert(url_link) {
        var link = url_link;
        var r = confirm('apakah anda yakin akan kembali?');
        if (r == true) {
          //back
        } else {
          //fail
        }
      }
    });
    //Date range picker
    //   $('#tglkeluar').datepicker({
    //   format: 'dd/mm/yyyy',
    // todayHighlight: true,
    // autoclose: true
    // });

    function del_alert(url_link) {
      var x = confirm("Peringatan!\nData yang sudah dihapus tidak dapat dikembalikan\nApakah Anda Yakin?");
      if (x == true) {
        window.location = url_link;
      } else {
        return false;
      }
    }
  </script>

</body>

</html>