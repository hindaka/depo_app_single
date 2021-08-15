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
$id_conf_obat = isset($_GET['id']) ? $_GET['id'] : '0';
$task = isset($_GET['task']) ? $_GET['task'] : 'add';
if($task=='edit'){
	$get_conf = $db->query("SELECT * FROM conf_penyimpanan_obat WHERE id_conf_obat='".$id_conf_obat."'");
	$conf = $get_conf->fetch(PDO::FETCH_ASSOC);
	$nama_peny = $conf['nama_penyimpanan'];
	$pj = $conf['pj'];
	$get_petugas = $db->query("SELECT p.id_pegawai,peg.nama FROM petugas p INNER JOIN pegawai peg ON(p.id_pegawai=peg.id_pegawai) ORDER BY peg.nama ASC");
	$data_petugas = $get_petugas->fetchAll(PDO::FETCH_ASSOC);
}else{
	$get_petugas = $db->query("SELECT p.id_pegawai,peg.nama FROM petugas p INNER JOIN pegawai peg ON(p.id_pegawai=peg.id_pegawai) ORDER BY peg.nama ASC");
	$data_petugas = $get_petugas->fetchAll(PDO::FETCH_ASSOC);
	$nama_peny = "";
	$pj = "";
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
	    <?php if (isset($_GET['status']) && ($_GET['status'] == "1")) { ?><div class="alert alert-success alert-dismissable"><button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button><center><h4><i class="icon fa fa-check"></i>Berhasil</h4>Data TPN Berhasil ditambahkan</center></div>
			<?php }else if (isset($_GET['status']) && ($_GET['status'] == "2")) { ?><div class="alert alert-warning alert-dismissable"><button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button><center><h4><i class="icon fa fa-warning"></i>Peringatan</h4>TIPE TPN dengan Konsentrasi yang anda inputkan tersebut sudah terdaftar</center></div>
	    <?php } ?>
	    <!-- end pesan -->
        <section class="content-header">
          <h1>
            Pengaturan Penyimpanan Obat
          </h1>
          <ol class="breadcrumb">
            <li><a href="index.php"><i class="fa fa-dashboard"></i> Home</a></li>
            <li class="active">Pengaturan Penyimpanan Obat</li>
          </ol>
        </section>

        <!-- Main content -->
        <section class="content">
          <div class="row">
            <div class="col-xs-12">
              <div class="box box-primary">
                <div class="box-header">
                  <i class="fa fa-user"></i>
								  <h3 class="box-title">Form Input Penyimpanan Obat</h3>
                </div><!-- /.box-header -->
								<form class="" action="conf_penyimpanan_add_acc.php" id="lemari_form" method="post">
                <div class="box-body">
									<div class="form-group">
									  <label for="">Nama Penyimpanan <span style="color:red">*</span></label>
									  <input type="text" class="form-control" id="nama_penyimpanan" name="nama_penyimpanan" value="<?php echo $nama_peny; ?>" required>
										<input type="hidden" name="id" id="id" value="<?php echo $id_conf_obat; ?>">
										<input type="hidden" name="task" id="task" value="<?php echo $task; ?>">
									</div>
									<div class="form-group">
									  <label for="">Nama Petugas <span style="color:red">*</span></label>
									  <select name="nama_petugas" id="nama_petugas" class="form-control select2" required>
									  	<option value=""></option>
											<?php
												foreach ($data_petugas as $dp) {
													if($dp['nama']==$pj){
														echo '<option value="'.$dp['nama'].'" selected>'.$dp['nama'].'</option>';
													}else{
														echo '<option value="'.$dp['nama'].'">'.$dp['nama'].'</option>';
													}

												}
											?>
									  </select>
									</div>
                </div><!-- /.box-body -->
								<div class="box-footer">
									<button type="submit" id="simpan_tpn" class="btn btn-md btn-success"><i class="fa fa-save"></i> Simpan</button>
								</div>
							</form>
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
		<script src="../plugins/select2/select2.full.min.js" type="text/javascript"></script>
    <!-- SlimScroll -->
    <script src="../plugins/slimScroll/jquery.slimscroll.min.js" type="text/javascript"></script>
    <!-- FastClick -->
    <script src='../plugins/fastclick/fastclick.min.js'></script>
    <!-- AdminLTE App -->
    <script src="../dist/js/app.min.js" type="text/javascript"></script>
		<script type="text/javascript">
			$(function(){
				$('.select2').select2({
					placeholder : 'Pilih / Masukan Nama Pegawai',
					allowClear : true,
					width : 'resolve'
				})
			})
		</script>
  </body>
</html>
