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
require_once('../inc/anggota_check.php');
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

      <?php include("header.php"); ?>
	  <?php include "menu_index.php"; ?>
      <div class="content-wrapper">
        <!-- Content Header (Page header) -->
        <section class="content-header">
          <h1>
            Ubah Kata Sandi
            <small>-</small>
          </h1>
          <ol class="breadcrumb">
            <li><a href="index.php"><i class="fa fa-dashboard"></i> Home</a></li>
          </ol>
        </section>

        <!-- Main content -->
        <section class="content">
					<div class="row">
						<div class="col-md-6">
							<div class="box box-primary">
		            <div class="box-header with-border">
		              <h3 class="box-title"><i class="fa fa-pencil"></i> Ubah Kata Sandi</h3>
		            </div>
		            <div class="box-body">
									<input type="hidden" name="mem_id" id="mem_id" value="<?php echo $r1['mem_id']; ?>">
									<div class="form-group">
									  <label for="">Sandi Lama <span style="color:red">*</span></label>
									  <input type="password" class="form-control" name="sandi_lama" id="sandi_lama">
									</div>
									<div class="form-group">
									  <label for="">Sandi Baru <span style="color:red">*</span></label>
									  <input type="password" class="form-control" name="sandi_baru" id="sandi_baru">
									</div>
									<div class="form-group">
									  <label for="">Konfirmasi Sandi Baru <span style="color:red">*</span></label>
									  <input type="password" class="form-control" name="sandi_baru_konf" id="sandi_baru_konf">
									</div>
		            </div><!-- /.box-body -->
								<div class="box-footer">
									<button id="btnSimpanSandi" class="btn btn-md btn-success"><i class="fa fa-save"></i> Simpan</button>
								</div>
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
    <script src="../plugins/datatables/jquery.dataTables.js" type="text/javascript"></script>
    <script src="../plugins/datatables/dataTables.bootstrap.js" type="text/javascript"></script>
    <!-- SlimScroll -->
    <script src="../plugins/slimScroll/jquery.slimscroll.min.js" type="text/javascript"></script>
    <!-- FastClick -->
    <script src='../plugins/fastclick/fastclick.min.js'></script>
    <!-- AdminLTE App -->
    <script src="../dist/js/app.min.js" type="text/javascript"></script>
		<!-- sweetalert -->
		<script src="../plugins/sweetalert/sweetalert.min.js"></script>
    <!-- page script -->
    <script type="text/javascript">
	  $(function () {
    	$("#btnSimpanSandi").on("click",function(){
				var mem_id = $('#mem_id').val();
				var sandi_lama = $('#sandi_lama').val();
				var sandi_baru = $('#sandi_baru').val();
				var sandi_baru_konf = $('#sandi_baru_konf').val();
				if(sandi_baru_konf!=sandi_baru){
					swal('Peringatan!!','Sandi Baru Konfirmasi tidak sesuai dengan sandi baru',`warning`);
				}else{
					var fd = new FormData();
					fd.append("mem_id",mem_id);
					fd.append("sandi_lama",sandi_lama);
					fd.append("sandi_baru",sandi_baru);
					fd.append("sandi_baru_konf",sandi_baru_konf);
					$.ajax({
						type: 'POST',
						url: 'ajax_data/edit_password.php',
						data: fd,
						contentType: false,
						cache: false,
						processData:false,
						success: function(msg){
							console.log(msg);
						let res = JSON.parse(msg);
							if(res.status=='sukses'){
								swal({
								  title: res.title,
								  text: res.text,
								  icon: res.icon,
								  button: "Tutup!",
								}).then((value)=>{
									window.location="../logout.php";
								});
							}else if(res.status=='gagal'){
								swal({
								  title: res.title,
								  text: res.text,
								  icon: res.icon,
								  button: "Tutup!",
								});
							}else{
								// swal({
								//   title: res.title,
								//   text: res.text,
								//   icon: res.status,
								//   button: "Tutup!",
								// });
							}
						}
					});
				}
			})
		});
    </script>

  </body>
</html>
