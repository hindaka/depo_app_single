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
$get_paket = $db->query("SELECT * FROM paket_obat_bhp ORDER BY nama_paket ASC");
$all_paket = $get_paket->fetchAll(PDO::FETCH_ASSOC);
$get_dokter = $db->query("SELECT * FROM nmdokter WHERE aktif='ya'");
$all_dokter = $get_dokter->fetchAll(PDO::FETCH_ASSOC);
$get_dokter_anak = $db->query("SELECT * FROM nmdokter WHERE aktif='ya' AND kelompok_jasa LIKE '%anak%'");
$all_dokter_anak = $get_dokter_anak->fetchAll(PDO::FETCH_ASSOC);

?>
<!DOCTYPE html>
<html>

<head>
    <meta charset="UTF-8">
    <title>SIMRS <?php echo $version_depo; ?> | <?php echo $tipes[0]; ?></title>
    <meta content='width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no' name='viewport'>
    <!-- Bootstrap 3.3.2 -->
    <link href="../bootstrap/css/bootstrap.min.css" rel="stylesheet" type="text/css" />
    <link href="../plugins/datetimepicker/css/bootstrap-datetimepicker.css" rel="stylesheet" type="text/css" />
    <!-- Font Awesome Icons -->
    <link href="../plugins/font-awesome/4.3.0/font-awesome.min.css" rel="stylesheet" type="text/css" />
    <!-- Ionicons -->
    <link href="../plugins/ionicons/2.0.0/ionicon.min.css" rel="stylesheet" type="text/css" />
    <!-- DATA TABLES -->
    <link href="../plugins/datatables/dataTables.bootstrap.css" rel="stylesheet" type="text/css" />
    <!-- select2 -->
    <link href="../plugins/select2/select2.min.css" rel="stylesheet" type="text/css" />
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
                        <h4><i class="icon fa fa-check"></i>Berhasil</h4>Data Berhasil dicetak
                    </center>
                </div>
            <?php } else if (isset($_GET['status']) && ($_GET['status'] == "2")) { ?><div class="alert alert-success alert-dismissable"><button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>
                    <center>
                        <h4><i class="icon fa fa-check"></i>Berhasil</h4>Data Penyimpanan berhasil diubah
                    </center>
                </div>
            <?php } else if (isset($_GET['status']) && ($_GET['status'] == "3")) { ?><div class="alert alert-warning alert-dismissable"><button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>
                    <center>
                        <h4><i class="icon fa fa-warning"></i>Peringatan!</h4>Nama Penyimpanan Obat sudah terdaftar, Silakan gunakan Nama lain.
                    </center>
                </div>
            <?php } else if (isset($_GET['status']) && ($_GET['status'] == "4")) { ?><div class="alert alert-success alert-dismissable"><button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>
                    <center>
                        <h4><i class="icon fa fa-check"></i>Berhasil!</h4>Data Penyimpanan Obat Berhasil dihapus
                    </center>
                </div>
            <?php } ?>
            <!-- end pesan -->
            <section class="content-header">
                <h1>
                    Cetak Form Paket Obat
                </h1>
                <ol class="breadcrumb">
                    <li><a href="index.php"><i class="fa fa-dashboard"></i> Home</a></li>
                    <li class="active">Cetak Form Paket Obat</li>
                </ol>
            </section>

            <!-- Main content -->
            <section class="content">
                <div class="row">
                    <div class="col-xs-12">
                        <div class="box box-primary">
                            <div class="box-header">
                                <i class="fa fa-list"></i>
                                <h3 class="box-title">Form Cetak Paket Obat</h3>
                            </div>
                            <form action="cetak_paket_obat_bhp.php" method="get">
                                <div class="box-body">
                                    <div class="row">
                                        <div class="col-sm-4">
                                            <div class="form-group">
                                                <label for="">Tanggal Permintaan <span style="color:red">*Wajib diisi</span></label>
                                                <div class='input-group date' id='datetimepicker1'>
                                                    <input type='text' name="tanggal_tindakan" class="form-control" required />
                                                    <span class="input-group-addon">
                                                        <span class="glyphicon glyphicon-calendar"></span>
                                                    </span>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-sm-4">
                                            <div class="form-group">
                                                <label for="">Nomor Rekam Medis <span style="color:red">*(Wajib diisi)</span></label>
                                                <select name="nomedrek" id="nomedrek" class="form-control select2" required>
                                                    <option value=""></option>
                                                </select>
                                            </div>
                                        </div>
                                        <div class="col-sm-4">
                                            <div class="form-group">
                                                <label for="">Jenis Paket <span style="color:red">*(Wajib diisi)</span></label>
                                                <select name="jenis_paket" class="form-control" id="jenis_paket" required>
                                                    <option value=""></option>
                                                    <?php
                                                    foreach ($all_paket as $ap) {
                                                        echo '<option value="' . $ap['id_paket_ob'] . '">' . $ap['nama_paket'] . '</option>';
                                                    }
                                                    ?>
                                                </select>
                                            </div>
                                        </div>
                                    </div>
                                    <fieldset>
                                        <legend>Tenaga Medis</legend>
                                        <div class="row">
                                            <div class="col-sm-4">
                                                <div class="form-group">
                                                    <label for="">Dokter Bedah</label>
                                                    <select name="dokter_bedah" id="dokter_bedah" class="form-control">
                                                        <option value=""></option>
                                                        <?php
                                                        foreach ($all_dokter as $ad) {
                                                            echo '<option value="' . $ad['nama'] . '">' . $ad['nama'] . '</option>';
                                                        }
                                                        ?>
                                                    </select>
                                                </div>
                                            </div>
                                            <div class="col-sm-4">
                                                <div class="form-group">
                                                    <label for="">Dokter Anestesi</label>
                                                    <select name="dokter_anestesi" id="dokter_anestesi" class="form-control">
                                                        <option value=""></option>
                                                        <?php
                                                        foreach ($all_dokter as $ad) {
                                                            echo '<option value="' . $ad['nama'] . '">' . $ad['nama'] . '</option>';
                                                        }
                                                        ?>
                                                    </select>
                                                </div>
                                            </div>
                                            <div class="col-sm-4">
                                                <div class="form-group">
                                                    <label for="">Perawat Anestesi</label>
                                                    <input type="text" name="perawat_anestesi" id="perawat_anestesi" class="form-control">
                                                </div>
                                            </div>
                                            <div class="col-sm-4">
                                                <div class="form-group">
                                                    <label for="">Dokter Anak</label>
                                                    <select name="dokter_anak" id="dokter_anak" class="form-control">
                                                        <option value=""></option>
                                                        <?php
                                                        foreach ($all_dokter_anak as $ada) {
                                                            echo '<option value="' . $ada['nama'] . '">' . $ada['nama'] . '</option>';
                                                        }
                                                        ?>
                                                    </select>
                                                </div>
                                            </div>
                                        </div>
                                    </fieldset>
                                    <fieldset>
                                        <legend>Data Tambahan</legend>
                                        <div class="row">
                                            <div class="col-sm-4">
                                                <div class="form-group">
                                                    <label for="">Diagnosa</label>
                                                    <input type="text" name="diagnosa" id="diagnosa" class="form-control">
                                                </div>
                                            </div>
                                            <div class="col-sm-4">
                                                <div class="form-group">
                                                    <label for="">Jenis Anestesi</label>
                                                    <input type="text" name="jenis_anestesi" id="jenis_anestesi" class="form-control">
                                                </div>
                                            </div>
                                            <div class="col-sm-4">
                                                <div class="form-group">
                                                    <label for="">Asisten/Instrumentor</label>
                                                    <input type="text" name="asisten" id="asisten" class="form-control">
                                                </div>
                                            </div>
                                        </div>
                                    </fieldset>
                                </div>
                                <div class="box-footer">
                                    <button id="submitPaket" type="submit" class="btn btn-success btn-md"><i class="fa fa-print"></i> Cetak</button>
                                </div>
                            </form>
                        </div>
                    </div>
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
    <script src="../plugins/datetimepicker/js/moment-with-locales.js" type="text/javascript"></script>
    <script src="../plugins/datetimepicker/js/bootstrap-datetimepicker.js" type="text/javascript"></script>
    <!-- DATA TABES SCRIPT -->
    <script src="../plugins/datatables/jquery.dataTables.min.js" type="text/javascript"></script>
    <script src="../plugins/datatables/dataTables.bootstrap.js" type="text/javascript"></script>
    <!-- SlimScroll -->
    <script src="../plugins/slimScroll/jquery.slimscroll.min.js" type="text/javascript"></script>
    <!-- select2 -->
    <script src="../plugins/select2/select2.full.min.js" type="text/javascript"></script>
    <!-- FastClick -->
    <script src='../plugins/fastclick/fastclick.min.js'></script>
    <!-- AdminLTE App -->
    <script src="../dist/js/app.min.js" type="text/javascript"></script>
    <script src="../plugins/sweetalert/sweetalert.min.js" type="text/javascript"></script>
    <!-- page script -->
    <script type="text/javascript">
        $(function() {
            $('#datetimepicker1').datetimepicker({
                format: "DD/MM/YYYY"
            });
            $("#dokter_bedah").select2({
                placeholder: "Pilih dokter",
                allowClear: true,
            });
            $("#dokter_anestesi").select2({
                placeholder: "Pilih dokter",
                allowClear: true,
            });
            $("#dokter_anak").select2({
                placeholder: "Pilih dokter",
                allowClear: true,
            });
            $("#nomedrek").select2({
                ajax: {
                    url: "ajax_data/get_identitas_pasien.php",
                    dataType: 'json',
                    delay: 100,
                    data: function(params) {
                        return {
                            q: params.term, // search term
                        };
                    },
                    processResults: function(data, params) {
                        console.log(data);
                        // parse the results into the format expected by Select2
                        // since we are using custom formatting functions we do not need to
                        // alter the remote JSON data, except to indicate that infinite
                        // scrolling can be used
                        // params.page = params.page || 1;

                        return {
                            results: data.items,
                        };
                    },
                    cache: true
                },
                placeholder: 'Masukan Nomor Rekam Medis/Nama Pasien',
                minimumInputLength: 4,
                minimumResultsForSearch: Infinity,
                templateResult: formatRepo,
                templateSelection: formatRepoSelection
            });

            function formatRepo(repo) {
                if (repo.loading) {
                    return "Sedang Melakukan Penarikan Data...";
                }
                var $state = repo.text;
                return $state;
            }

            function formatRepoSelection(repo) {
                return repo.text;
            }
        });
    </script>
</body>

</html>