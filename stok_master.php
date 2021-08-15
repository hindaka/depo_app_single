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
$tipe_depo = $tipes[2];
$conf = json_decode(file_get_contents("config/env_depo.json"), true);
$id_depo = $conf[$tipe_depo]["id_depo"];
//mysql data obat
$h2 = $db->query("SELECT k.id_obat,k.id_warehouse,g.nama,g.sumber,g.jenis,g.satuan,SUM(k.volume_kartu_akhir) as stok FROM kartu_stok_ruangan k INNER JOIN gobat g ON(k.id_obat=g.id_obat) WHERE g.flag_single_id='new' AND k.id_warehouse='" . $id_depo . "' GROUP BY k.id_obat");
$data2 = $h2->fetchAll(PDO::FETCH_ASSOC);
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
    <style>
        .swal-overlay {
            background-color: rgba(96, 92, 168, 0.45);
        }
    </style>
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
            <?php } else if (isset($_GET['status']) && ($_GET['status'] == "4")) { ?><div class="alert alert-success alert-dismissable"><button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>
                    <center>
                        <h4><i class="icon fa fa-check"></i>Berhasil</h4>Stok Obat Berhasil disinkronisasi
                    </center>
                </div>
            <?php } else if (isset($_GET['status']) && ($_GET['status'] == "5")) { ?><div class="alert alert-warning alert-dismissable"><button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>
                    <center>
                        <h4><i class="icon fa fa-ban"></i>Peringatan!</h4>Data Kartu Persediaan tidak ditemukan, Sinkronisasi Gagal
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
                                <h3 class="box-title">Data Stok Apotek Obat APBD</h3>
                                <button onclick="window.location.href='export_stok.php?sumber=APBD'" class="btn btn-success pull-right"><i class="fa fa-download"></i> Export Data</button>
                            </div><!-- /.box-header -->
                            <div class="box-body">
                                <div class="table-responsive">
                                    <table id="example1" class="table table-bordered">
                                        <thead>
                                            <tr class="info">
                                                <th>No. ID</th>
                                                <th>Nama Obat</th>
                                                <th>Jenis/Kategori</th>
                                                <th>Volume (Stok)</th>
                                                <th>Satuan</th>
                                                <th>Action</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <?php
                                            foreach ($data2 as $r2) {
                                                if ($r2['stok'] <= 0) {
                                                    $color_block = 'danger';
                                                    $text_block = 'color:red';
                                                } else {
                                                    $color_block = 'success';
                                                    $text_block = 'color:blue';
                                                }
                                                $idBtn = "btnSync" . $r2['id_obat'];
                                                echo "<tr class='" . $color_block . "' style='" . $text_block . "'>
																	<td>" . $r2['id_obat'] . "</td>
																	<td>" . $r2['nama'] . "</td>
																	<td>" . $r2['jenis'] . "</td>
																	<td>" . $r2['stok'] . "</td>
																	<td>" . $r2['satuan'] . "</td>
                                  <td>
                                    <button id='" . $idBtn . "' onclick='syncData(this)' class='btn btn-block btn-sm bg-purple' data-id_obat='" . $r2['id_obat'] . "' data-ware='" . $r2['id_warehouse'] . "' data-sumber='APBD'><i class='fa fa-gears'></i> Sync Stok</button>
																		<a class='btn btn-block btn-sm btn-success' href='kartu_persediaan.php?id=" . $r2['id_obat'] . "&ware=" . $r2['id_warehouse'] . "'><i class='fa fa-book'></i> Kartu Persediaan</a>
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
    <!-- notifikasi -->
    <script src="../plugins/sweetalert/sweetalert.min.js"></script>
    <!-- page script -->
    <script type="text/javascript">
        function syncData(ele) {
            var id_btn = ele.id
            var id_obat = $("#" + id_btn).data('id_obat')
            var id_warehouse = $("#" + id_btn).data('ware')
            var sumber = $("#" + id_btn).data('sumber')
            // ajax goes here
            var fd = new FormData();
            fd.append("id_obat", id_obat);
            fd.append("id_warehouse", id_warehouse);
            fd.append("sumber", sumber);
            $.ajax({
                type: 'POST',
                url: 'ajax_data/sync_stok_depo.php',
                data: fd,
                contentType: false,
                cache: false,
                processData: false,
                success: function(msg) {
                    // console.log(msg)
                    var res = JSON.parse(msg);
                    swal({
                        title: res.title,
                        text: res.msg,
                        icon: res.icon,
                        button: "Reload Page",
                    }).then((value) => {
                        window.location.href = 'stok_apbd.php';
                    });
                }
            });
        }
    </script>
    <script type="text/javascript">
        $(function() {
            $("#example1").dataTable();
        });
    </script>

</body>

</html>