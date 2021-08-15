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
$id_transaksi = isset($_GET['t']) ? $_GET['t'] : '';
$task = isset($_GET['task']) ? $_GET['task'] : '';
$f_today = date("Y-m-d");
$submit = isset($_POST['tambah']) ? $_POST['tambah'] : '';
$setRuang = isset($_GET['r']) ? $_GET['r'] : '';
$transaksi = "id_trans/" . $id_transaksi;
$tipe_depo = $tipes[2];
$conf = json_decode(file_get_contents("config/env_depo.json"), true);
$id_depo = $conf[$tipe_depo]["id_depo"];
$nama_depo = $conf[$tipe_depo]["nama_depo"];
//get data pasien
$sql_pasien = "SELECT rp.nama,rp.nomedrek,ro.dpjp,rp.jpasien FROM registerpasien rp INNER JOIN rincian_obat_pasien ro ON(rp.id_pasien=ro.id_pasien) WHERE ro.id_rincian_obat=" . $id_rincian . " LIMIT 1";
$get_pasien = $db->query($sql_pasien);
$pasien = $get_pasien->fetch(PDO::FETCH_ASSOC);

$sql_rincian = "SELECT rd.*,g.nama,g.expired,w.nama_ruang FROM rincian_detail_obat rd INNER JOIN rincian_transaksi_obat ro ON(rd.id_trans_obat=ro.id_trans_obat) INNER JOIN warehouse w ON(ro.id_warehouse=w.id_warehouse) INNER JOIN gobat g ON(rd.id_obat=g.id_obat) WHERE rd.id_rincian='" . $id_rincian . "' AND rd.id_trans_obat='" . $id_transaksi . "'";
$e_rincian = $db->query($sql_rincian);
if ($pasien['jpasien'] == 'Umum') {
  $list_obat = $db->query("SELECT ws.id_warehouse_stok,ws.id_warehouse,ws.id_obat,ws.stok,g.nama,g.sumber FROM warehouse_stok ws INNER JOIN warehouse w ON(w.id_warehouse=ws.id_warehouse) INNER JOIN gobat g ON(g.id_obat=ws.id_obat) WHERE w.id_warehouse='" . $id_depo . "' AND ws.stok>0 AND g.flag_single_id='new'");
  // $list_obat = $db->query("SELECT ws.id_warehouse_stok,ws.id_warehouse,ws.id_obat,ws.stok,g.nama,g.sumber FROM warehouse_stok ws INNER JOIN warehouse w ON(w.id_warehouse=ws.id_warehouse) INNER JOIN gobat g ON(g.id_obat=ws.id_obat) WHERE w.nama_ruang='Farmasi'  AND g.fornas_app='tidak'");
} else {
  $list_obat = $db->query("SELECT ws.id_warehouse_stok,ws.id_warehouse,ws.id_obat,ws.stok,g.nama,g.sumber FROM warehouse_stok ws INNER JOIN warehouse w ON(w.id_warehouse=ws.id_warehouse) INNER JOIN gobat g ON(g.id_obat=ws.id_obat) WHERE w.id_warehouse='" . $id_depo . "' AND ws.stok>0  AND g.flag_single_id='new'");
  // $list_obat = $db->query("SELECT ws.id_warehouse_stok,ws.id_warehouse,ws.id_obat,ws.stok,g.nama,g.sumber FROM warehouse_stok ws INNER JOIN warehouse w ON(w.id_warehouse=ws.id_warehouse) INNER JOIN gobat g ON(g.id_obat=ws.id_obat) WHERE w.nama_ruang='Farmasi' AND g.fornas_app='ya'");
}
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
        <div class="alert bg-purple">Jangan Lupa Klik Tombol Simpan jika sudah selesai menginputkan data transaksi obat.</div>
        <div class="row">
          <!-- left column -->
          <div class="col-md-4">
            <div class="box box-primary">
              <div class="box-header">
                <i class="fa fa-medkit"></i>
                <h3 class="box-title">Data Pasien & Obat</h3>
              </div><!-- /.box-header -->
              <!-- form start -->
              <form role="form" action="save_detail_ranap.php?id=<?php echo $id_rincian; ?>&t=<?php echo $id_transaksi; ?>&task=item_added" method="post">
                <div class="box-body">
                  <div class="form-group">
                    <label for="id_transaksi">ID Transaksi</label>
                    <input type="text" class="form-control" id="id_transaksi" name="id_transaksi" value="<?php echo $id_transaksi; ?>" readonly>
                  </div>
                  <div class="form-group">
                    <label for="nama">No. Rekam Medis</b></label>
                    <input type="text" class="form-control" id="nomedrek" name="nomedrek" value="<?php echo $pasien['nomedrek']; ?>" disabled>
                  </div>
                  <div class="form-group">
                    <label for="today">Tanggal Hari ini</label>
                    <input type="text" class="form-control" id="today" name="today" value="<?php echo date('d/m/Y'); ?>" readonly>
                  </div>
                  <div class="form-group">
                    <label for="ruang">Ruangan</label>
                    <input type="text" class="form-control" name="ruang" value="<?php echo $setRuang; ?>" readonly>
                  </div>
                  <div class="form-group">
                    <label for="namaobat">Nama Obat <span style="color:red">*</span></label>
                    <select class="form-control selectpicker" data-live-search="true" name="namaobat" id="namaobat" required>
                      <option value="">---Pilih Obat---</option>
                      <?php
                      foreach ($obat_l as $o) {
                        echo "<option value='" . $o['id_warehouse_stok'] . "'>" . $o['nama'] . " - " . $o['stok'] . "</option>";
                      }
                      ?>
                    </select>
                  </div>
                  <div class="form-group">
                    <label for="volume">Volume <span style="color:red">*</span></label>
                    <input type="number" class="form-control" id="volume" name="volume" placeholder="Volume" min="1" autocomplete="off" required>
                  </div>
                  <div class="form-group">
                    <label for="tuslah">Tuslah <span style="color:red">*</span></label><br />
                    <!--<select class="form-control" name="tuslah" required>
													<option value="">--Pilih Tuslah--</option>
													<option value="1">Rajal</option>
													<option value="2">Rajal Racik</option>
													<option value="3">Ranap</option>
													<option value="4">Ranap Racik</option>
													<option value="5">Non Tuslah</option>
												</select> -->

                    <input type="radio" name="tuslah" class="flat-blue" value="1" required>
                    Rajal&nbsp;&nbsp;&nbsp;
                    <input type="radio" name="tuslah" class="flat-blue" value="2" required>
                    Rajal racik&nbsp;&nbsp;&nbsp;
                    <input type="radio" name="tuslah" class="flat-blue" value="3" required>
                    Ranap&nbsp;&nbsp;&nbsp;
                    <input type="radio" name="tuslah" class="flat-blue" value="4" required>
                    Ranap racik&nbsp;&nbsp;&nbsp;
                    <input type="radio" name="tuslah" class="flat-blue" value="5" required>
                    Non tuslah
                  </div>
                  <div class="form-group">
                    <label for="tambah">&nbsp;</label>
                    <input type="hidden" name="tambah" value="ok">
                    <?php
                    if ($setRuang == 'IGD') {
                      echo '<button type="submit" class="btn btn-primary"><i class="fa fa-plus"></i> Tambah Obat</button>';
                    } else if ($setRuang == 'OK') {
                      echo '<button type="submit" class="btn btn-primary"><i class="fa fa-plus"></i> Tambah Obat</button>';
                    } else {
                    }
                    ?>
                    <!-- <a href="obat_ranap_trans.php?id=<?php echo $id_rincian ?>" class="btn btn-info">Kembali <i class="fa fa-undo"></i></a> -->

                  </div>
                </div><!-- /.box-body -->
            </div>
          </div><!-- /.left column -->
          <!-- right column -->
          <div class="col-md-8">
            <div class="box box-primary">
              <div class="box-header">
                <i class="fa fa-medkit"></i>
                <h3 class="box-title">Data Obat pasien</h3>
              </div><!-- /.box-header -->
              <!-- form start -->
              <div class="box-body">
                <table id="example1" class="table table-striped">
                  <thead>
                    <tr class="bg-blue">
                      <th>No.</th>
                      <th>#</th>
                      <th>Tanggal Input</th>
                      <th>Nama</th>
                      <th>Expired</th>
                      <th>Volume</th>
                      <th>Total</th>
                      <th>aksi</th>
                    </tr>
                  </thead>
                  <tbody>
                    <?php
                    $nomor = 1;
                    $total_biaya = 0;
                    foreach ($e_rincian as $r) {
                      $new_data = date('d-m-Y', strtotime($r['created_at']));
                      $check_retur = $db->query("SELECT COUNT(*) as retur_total FROM rincian_retur_obat WHERE id_detail_rincian='" . $r['id_detail_rincian'] . "'");
                      $c_retur = $check_retur->fetch(PDO::FETCH_ASSOC);
                      if ($c_retur['retur_total'] > 0) {
                        $code = "<span class='label label-primary'>R</span>";
                        if ($r['nama_ruang'] == 'Depo Farmasi IGD') {
                          $list_block = "<ul class=\"dropdown-menu\"><li><a href='etiket_ranap.php?i=" . $id_rincian . "&t=" . $id_transaksi . "&rincian=" . $r['id_detail_rincian'] . "' target='_blank'><i class='fa fa-book'></i>Etiket</a></li></ul>";
                        } else if ($r['nama_ruang'] == 'Depo Farmasi OK') {
                          $list_block = "<ul class=\"dropdown-menu\"><li><a href='etiket_ranap.php?i=" . $id_rincian . "&t=" . $id_transaksi . "&rincian=" . $r['id_detail_rincian'] . "' target='_blank'><i class='fa fa-book'></i>Etiket</a></li></ul>";
                        } else {
                          $list_block = "";
                        }
                      } else {
                        $code = "<span class='label label-default'>NR</span>";
                        if ($r['nama_ruang'] == 'Depo Farmasi IGD') {
                          $list_block = "<ul class=\"dropdown-menu\"><li><a style='cursor:pointer' onclick=\" return del_alert('obat_ranap_hapus.php?r=" . $id_rincian . "&t=" . $id_transaksi . "&rincian=" . $r['id_detail_rincian'] . "')\"><i class='fa fa-trash'></i> Hapus</a></li>
                            <li><a href='etiket_ranap.php?i=" . $id_rincian . "&t=" . $id_transaksi . "&rincian=" . $r['id_detail_rincian'] . "' target='_blank'><i class='fa fa-book'></i>Etiket</a></li></ul>";
                        } else if ($r['nama_ruang'] == 'Depo Farmasi OK') {
                          $list_block = "<ul class=\"dropdown-menu\"><li><a style='cursor:pointer' onclick=\" return del_alert('obat_ranap_hapus.php?r=" . $id_rincian . "&t=" . $id_transaksi . "&rincian=" . $r['id_detail_rincian'] . "')\"><i class='fa fa-trash'></i> Hapus</a></li>
                          <li><a href='etiket_ranap.php?i=" . $id_rincian . "&t=" . $id_transaksi . "&rincian=" . $r['id_detail_rincian'] . "' target='_blank'><i class='fa fa-book'></i>Etiket</a></li></ul>";
                        } else {
                          $list_block = "";
                        }
                      }
                      echo "<tr>
                                <td>" . $nomor . "</td>
																<td>" . $code . "</td>
                                <td>" . $new_data . "</td>
                                <td>" . $r['nama'] . "</td>
																<td>" . $r['expired'] . "</td>
                                <td>" . $r['volume'] . "</td>
                                <td style='text-align:right'>" . number_format($r['sub_total'], 0, ',', '.') . "</td>
                                <td>
																	<div class=\"btn-group\">
																		<button type=\"button\" class=\"btn btn-sm dropdown-toggle\" data-toggle=\"dropdown\" aria-haspopup=\"true\" aria-expanded=\"false\">
																			Action <span class=\"caret\"></span>
																		</button>
																			" . $list_block . "																		
																		</div>
																</td>
                              </tr>";
                      $nomor++;
                      $total_biaya += $r['sub_total'];
                    }
                    // <li><a href='retur_ranap.php?i=".$id_rincian."&t=".$id_transaksi."&kartu=".$r['id_kartu_ruangan']."&rincian=".$r['id_detail_rincian']."'><i class='fa fa-undo'></i>Retur</a></li>
                    ?>
                  </tbody>
                  <tfoot>
                    <tr>
                      <th colspan="5" style='text-align:right'>Total Biaya transaksi</th>
                      <td colspan="2" style="text-align:right"><b>Rp.<?php echo number_format($total_biaya, 0, ',', '.'); ?></b></td>
                      <td>&nbsp;</td>
                    </tr>
                  </tfoot>
                </table>
              </div>
              <div class="box-footer">
                <?php
                //update total_harga
                $update_total = $db->query("UPDATE rincian_transaksi_obat SET biaya_trans=" . $total_biaya . " WHERE id_trans_obat=" . $id_transaksi);
                ?>
                <a href="obat_ranap_save.php?id=<?php echo $id_rincian; ?>&t=<?php echo $id_transaksi; ?>" class="btn btn-app"><i class="fa fa-save"></i> Simpan </a>
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
  <script src="../plugins/datatables/jquery.dataTables.min.js" type="text/javascript"></script>
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
      $("#example1").DataTable({
        "pageLength": 25
      });

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