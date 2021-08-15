<?php
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
$id_invoice_all = isset($_GET['inv']) ? $_GET['inv'] : '';
$id_register = isset($_GET['reg']) ? $_GET['reg'] : '';
$get_pasien = $db->prepare("SELECT nama,nomedrek FROM registerpasien WHERE id_pasien=:id");
$get_pasien->bindParam(":id",$id_register,PDO::PARAM_INT);
$get_pasien->execute();
$pasien = $get_pasien->fetch(PDO::FETCH_ASSOC);
$get_tindakan = $db->query("SELECT * FROM tarif WHERE aktif='y' AND aturan_tarif='PERWAL 2018' AND slug_kategori LIKE 'farmasi'");
$tindakan = $get_tindakan->fetchAll(PDO::FETCH_ASSOC);
$get_pelayanan =$db->query("SELECT fp.*,a.nama FROM farmasi_pelayanan fp INNER JOIN anggota a ON(fp.petugas=a.mem_id) WHERE id_register='".$id_register."'");
$pelayanan = $get_pelayanan->fetchAll(PDO::FETCH_ASSOC);
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
				<!-- pesan feedback -->
			    <?php if (isset($_GET['status']) && ($_GET['status'] == "1")) { ?><div class="alert alert-success alert-dismissable"><button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button><center><h4><i class="icon fa fa-check"></i>Berhasil</h4>Data Tindakan Pelayanan farmasi berhasil ditambahkan</center></div>
				<?php }else if (isset($_GET['status']) && ($_GET['status'] == "2")) { ?><div class="alert alert-success alert-dismissable"><button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button><center><h4><i class="icon fa fa-check"></i>Berhasil</h4>Data Tindakan Pelayanan farmasi berhasil dihapus</center></div>
			    <?php } ?>
        <!-- Content Header (Page header) -->
        <section class="content-header">
          <h1>
            Data Tindakan
            <small>Pelayanan Farmasi</small>
          </h1>
          <ol class="breadcrumb">
            <li><a href="index.php"><i class="fa fa-dashboard"></i> Home</a></li>
            <li>Pelayanan Farmasi</li>
          </ol>
        </section>

        <!-- Main content -->
        <section class="content">
          <div class="row">
            <div class="col-xs-12">
              <div class="box box-success">
                <div class="box-header with-border">
                  <h3 class="box-title">Form Input tindakan Pelayanan Farmasi</h3>
                </div>
                <form action="tindakan_pasien_tambah.php" method="post">
                  <div class="box-body">
										<div class="form-group">
											<label for="">Tanggal Pelayanan Farmasi <span style="color:red">*</span></label>
											<input type="datetime-local" name="tanggal_pelayanan" class="form-control" id="tanggal_pelayanan" required>
										</div>
										<div class="form-group">
											<label for="">Klasifikasi DRP</label>
											<input type="text" name="drp" class="form-control" id="drp" placeholder="Masukan Klasifikasi DRP jika Ada">
										</div>
										<div class="form-group">
										  <label for="">Rekomendasi</label>
										  <input type="text" name="rekomendasi" class="form-control" id="rekomendasi" placeholder="Masukan Rekomendasi jika Ada">
										</div>
										<div class="form-group">
											<label for="">Nama Tindakan / Pelayanan Farmasi <span style="color:red">*</span></label>
											<select name="tindakan" id="tindakan" class="form-control select2" required>
												<option value=""></option>
												<?php
												foreach ($tindakan as $t) {
													echo '<option value="'.$t['id_tarif'].'">'.$t['nama'].'</option>';
												}
												?>
											</select>
											<input type="hidden" name="id_invoice_all" id="id_invoice_all" value="<?php echo $id_invoice_all; ?>">
											<input type="hidden" name="id_register" id="id_register" value="<?php echo $id_register; ?>">
										</div>
                  </div><!-- /.box-body -->
                  <div class="box-footer">
                    <button type="submit" class="btn btn-md btn-primary"><i class="fa fa-plus"></i> Tambahkan</button>
                  </div>
                </form>
              </div><!-- /.box -->
            </div>
            <div class="col-xs-12">
              <div class="box box-success">
                <div class="box-header with-border">
                  <h3 class="box-title">Data Pelayanan Farmasi untuk Pasien (<?php echo $pasien['nomedrek'].", ".$pasien['nama']; ?>)</h3>
                </div>
                <div class="box-body">
                  <div class="table-responsive">
                    <table id="example1" class="table table-bordered table-striped">
                      <thead>
                        <tr class="bg-blue">
                          <th>Tanggal Input</th>
                          <th>Nama Tindakan / Pelayanan</th>
													<th>Klasifikasi DRP</th>
													<th>Rekomendasi</th>
                          <th>Petugas</th>
													<th>Masuk Ke Tagihan</th>
                          <th>Aksi</th>
                        </tr>
                      </thead>
                      <tbody>
                        <?php
                          foreach ($pelayanan as $pel) {
														if($pel['inv_in']=='y'){
															$inv = '<span class="label label-success"><i class="fa fa-check"></i></span>';
														}else{
															$inv = '<span class="label label-primary"><i class="fa fa-times"></i></span>';
														}
                            echo '<tr>
                                    <td>'.$pel['tanggal_pelayanan'].'</td>
                                    <td>'.$pel['nama_pelayanan'].'</td>
																		<td>'.$pel['drp'].'</td>
																		<td>'.$pel['rekomendasi'].'</td>
                                    <td>'.$pel['nama'].'</td>
																		<td>'.$inv.'</td>
                                    <td><a href="tindakan_pasien_hapus.php?f='.$pel['id_far_pelayanan'].'&inv='.$id_invoice_all.'&reg='.$id_register.'&log='.$pel['id_far_pelayanan'].'" class="btn btn-sm btn-danger"><i class="fa fa-trash"></i> hapus</a></td>
                                  </tr>';
                          }
                        ?>
                      </tbody>
                    </table>
                  </div>
                </div><!-- /.box-body -->
              </div><!-- /.box -->
            </div>
          </div>


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
      $(function () {
        $("#example1").dataTable();
      });
    </script>

  </body>
</html>
