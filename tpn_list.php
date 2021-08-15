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
$id_rincian = isset($_GET['rincian']) ? $_GET['rincian'] : '';
$id_register = isset($_GET['reg']) ? $_GET['reg'] : '';
$get_pasien = $db->prepare("SELECT nama,nomedrek FROM registerpasien WHERE id_pasien=:id");
$get_pasien->bindParam(":id",$id_register,PDO::PARAM_INT);
$get_pasien->execute();
$pasien = $get_pasien->fetch(PDO::FETCH_ASSOC);
$get_tpn = $db->query("SELECT * FROM tpn_apotek ORDER BY tipe_tpn ASC");
$data_tpn = $get_tpn->fetchAll(PDO::FETCH_ASSOC);
$get_all_tpn = $db->query("SELECT * FROM apotek_tpn_list WHERE id_register='".$id_register."'");
$all_tpn = $get_all_tpn->fetchAll(PDO::FETCH_ASSOC);
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
            Data TPN
            <small>Pelayanan Farmasi</small>
          </h1>
          <ol class="breadcrumb">
            <li><a href="index.php"><i class="fa fa-dashboard"></i> Home</a></li>
            <li>Data TPN Pelayanan Farmasi</li>
          </ol>
        </section>

        <!-- Main content -->
        <section class="content">
          <div class="row">
            <div class="col-xs-12">
              <div class="box box-primary">
                <div class="box-header with-border">
                  <h3 class="box-title">Form Input TPN</h3>
									<a onclick="window.location='obat_ranap_trans.php?id=<?php echo $id_rincian; ?>'" class="btn btn-sm btn-success pull-right"><i class="fa fa-check"></i> Selesai</a>
                </div>
                <div class="box-body">
									<div class="nav-tabs-custom">
				            <ul class="nav nav-tabs">
				              <li class="active"><a href="#tab_1" data-toggle="tab">INPUT TPN</a></li>
				              <li><a href="#tab_2" data-toggle="tab">CUSTOM TPN</a></li>
				            </ul>
				            <div class="tab-content">
				              <div class="tab-pane active" id="tab_1">
												<form action="tpn_list_add.php" method="post">
													<input type="hidden" name="tpn_input" id="tpn_input" value="list">
													<input type="hidden" name="rincian" id="rincian" value="<?php echo $id_rincian; ?>">
													<input type="hidden" name="reg" id="reg" value="<?php echo $id_register; ?>">
													<div class="row">
														<div class="col-sm-6">
															<div class="form-group">
															  <label for="">Daftar TPN <span style="color:red">*</span></label>
															  <select name="list_tpn" id="list_tpn" class="form-control" required>
															  	<option value=""></option>
																	<?php
																		foreach ($data_tpn as $t) {
																			echo '<option value="'.$t['id_tpn_apotek'].'">'.$t['tipe_tpn'].' 1:'.$t['gr'].' ml Dx '.$t['konsentrasi'].'%</option>';
																		}
																	?>
															  </select>
															</div>
														</div>
														<div class="col-sm-6">
															<div class="form-group">
																<label for="ns3">Heparin <span style="color:red">*</span></label>
																<div class="input-group">
																	<input type="text" class="form-control" id="heparin" name="heparin" placeholder="Heparin dalam IU" required>
																	<span class="input-group-addon">IU</span>
																</div>
															</div>
														</div>
														<div class="col-sm-12">
															<button type="submit" id="simpan_input_tpn" class="btn btn-md btn-primary"><i class="fa fa-plus"></i> Simpan</button>
														</div>
													</div>
												</form>
				              </div>
				              <div class="tab-pane" id="tab_2">
												<form class="" action="tpn_list_add.php" method="post">
												<input type="hidden" name="tpn_input" id="tpn_input" value="custom">
												<input type="hidden" name="rincian" id="rincian" value="<?php echo $id_rincian; ?>">
												<input type="hidden" name="reg" id="reg" value="<?php echo $id_register; ?>">
												<div class="form-group">
												  <label for="">No.Rekam Medis <span style="color:red">*</span></label>
												  <input type="text" class="form-control" id="nomedrek" name="nomedrek" placeholder="Masukan Nomor Rekam Medis Pasien" value="<?php echo $pasien['nomedrek']; ?>" readonly>
												</div>
												<div class="row">
													<div class="col-sm-3">
														<div class="form-group">
														  <label for="amino_acid">Amino Acid 6% <span style="color:red;">*</span></label>
															<div class="input-group">
															  <input type="text" class="form-control" id="amino_acid" name="amino_acid" placeholder="Amino Acid 6% dalam cc" required>
																<span class="input-group-addon">cc</span>
															</div>
														</div>
													</div>
													<div class="col-sm-3">
														<div class="form-group">
														  <label for="dex40">Dextrose 40% <span style="color:red;">*</span></label>
															<div class="input-group">
															  <input type="text" class="form-control" id="dex40" name="dex40" placeholder="Dextrose 40% dalam cc" required>
																<span class="input-group-addon">cc</span>
															</div>
														</div>
													</div>
													<div class="col-sm-3">
														<div class="form-group">
														  <label for="dex10">Dextrose 10% <span style="color:red;">*</span></label>
															<div class="input-group">
															  <input type="text" class="form-control" id="dex10" name="dex10" placeholder="Dextrose 10% dalam cc" required>
																<span class="input-group-addon">cc</span>
															</div>
														</div>
													</div>
													<div class="col-sm-3">
														<div class="form-group">
															<label for="kcl">KCL<span style="color:red;">*</span></label>
															<div class="input-group">
																<input type="text" class="form-control" id="kcl" name="kcl" placeholder="KCL dalam cc" required>
																<span class="input-group-addon">cc</span>
															</div>
														</div>
													</div>
												</div>
												<div class="row">
													<div class="col-sm-3">
														<div class="form-group">
															<label for="ca_glu_10">Ca glukonas 10% <span style="color:red">*</span></label>
															<div class="input-group">
																<input type="text" class="form-control" id="ca_glu_10" name="ca_glu_10" placeholder="Ca Glukonas 10% dalam cc" required>
																<span class="input-group-addon">cc</span>
															</div>
														</div>
													</div>
													<div class="col-sm-3">
														<div class="form-group">
															<label for="mgso4">MgSO4 40% <span style="color:red">*</span></label>
															<div class="input-group">
																<input type="text" class="form-control" id="mgso4" name="mgso4" placeholder="MgSO4 40% dalam cc" required>
																<span class="input-group-addon">cc</span>
															</div>
														</div>
													</div>
													<div class="col-sm-3">
														<div class="form-group">
															<label for="ns3">NS 3% <span style="color:red">*</span></label>
															<div class="input-group">
																<input type="text" class="form-control" id="ns3" name="ns3" placeholder="NS 3% dalam cc" required>
																<span class="input-group-addon">cc</span>
															</div>
														</div>
													</div>
													<div class="col-sm-3">
														<div class="form-group">
															<label for="ns3">Heparin <span style="color:red">*</span></label>
															<div class="input-group">
																<input type="text" class="form-control" id="heparin" name="heparin" placeholder="Heparin dalam IU" required>
																<span class="input-group-addon">IU</span>
															</div>
														</div>
													</div>
												</div>
												<div class="row">
													<div class="col-md-12">
														<button type="submit" id="simpan_tpn" class="btn btn-md btn-primary"><i class="fa fa-plus"></i> Simpan</button>
													</div>
												</div>
				              </div> <!-- end tabs 2-->
				            </div>
				          </div>
                </div><!-- /.box-body -->
							</form>
              </div><!-- /.box -->
            </div>
            <div class="col-xs-12">
              <div class="box box-success">
                <div class="box-header with-border">
                  <h3 class="box-title">Data TPN Pelayanan Farmasi untuk Pasien (<?php echo $pasien['nomedrek'].", ".$pasien['nama']; ?>)</h3>
                </div>
                <div class="box-body">
                  <div class="table-responsive">
                    <table id="example1" class="table table-bordered table-striped">
                      <thead>
                        <tr class="bg-blue">
                          <th>Tanggal Input</th>
                          <th>Tpn Type</th>
													<th>judul</th>
													<th>AS 6%</th>
                          <th>Ca Gluko</th>
													<th>Dx 40%</th>
													<th>Mgso4 40%</th>
													<th>Dx 10%</th>
													<th>Nacl 3%</th>
													<th>Kcl 7,46%</th>
													<th>Heparin</th>
                          <th>Aksi</th>
                        </tr>
                      </thead>
                      <tbody>
                        <?php
                          foreach ($all_tpn as $tp) {
														if($tp['type_list']=='list'){
															$type = '<span class="label label-success">LIST</span>';
															$cetak='<a title="Cetak TPN LIST" target="_blank" class="btn btn-sm btn-success" href="cetak_tpn_barcode.php?id_list='.$tp['id_tpn_list'].'"><i class="fa fa-print"></i></a>';
															$judul = $tp['tipe_tpn'].' 1:'.$tp['gr'].' ml Dx '.$tp['konsentrasi'].'%';
														}else{
															$type = '<span class="label label-primary">CUSTOM</span>';
															$cetak='<a title="Cetak TPN CUSTOM" target="_blank" class="btn btn-sm btn-success" href="cetak_tpn_custom.php?id_list='.$tp['id_tpn_list'].'"><i class="fa fa-print"></i></a>';
															$judul = '-';
														}
														$hapus = '<a title="klik untuk menghapus data ini" class="btn btn-sm btn-danger" onclick="hapus_tpn('.$id_rincian.','.$tp['id_tpn_list'].','.$id_register.')" ><i class="fa fa-trash"></i></a>';
                            echo '<tr>
                                    <td>'.$tp['created_at'].'</td>
                                    <td>'.$type.'</td>
																		<td>'.$judul.'</td>
																		<td>'.$tp['amino_acid'].'</td>
																		<td>'.$tp['ca_glu_10'].'</td>
																		<td>'.$tp['dex40'].'</td>
																		<td>'.$tp['mgso4_40'].'</td>
																		<td>'.$tp['dex10'].'</td>
																		<td>'.$tp['ns_3'].'</td>
																		<td>'.$tp['kcl'].'</td>
																		<td>'.$tp['heparin'].'</td>
                                    <td>'.$cetak.' '.$hapus.'</td>
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
		<!-- sweetalert -->
		<script src='../plugins/sweetalert/sweetalert.min.js'></script>
    <!-- AdminLTE App -->
    <script src="../dist/js/app.min.js" type="text/javascript"></script>
    <!-- page script -->
    <script type="text/javascript">
			function hapus_tpn(rincian,id,reg){
				swal({
				  title: "Apakah Anda Yakin?",
				  text: "Data yang sudah dihapus tidak dapat dikembalikan!!",
				  icon: "warning",
				  buttons: true,
				  dangerMode: true,
				})
				.then((willDelete) => {
				  if (willDelete) {
						var fd = new FormData();
						fd.append("id", id);
						$.ajax({
							type: 'POST',
							url: 'ajax_data/tpn_list_delete.php',
							data: fd,
							contentType: false,
							cache: false,
							processData:false,
							success: function(msg){
								 var res = JSON.parse(msg);
								 swal({
		 							title: res.title,
		 						  text: res.text,
		 						  icon: res.icon,
		 						  button: "Tutup",
		 						}).then((next)=>{
		 							window.location='tpn_list.php?rincian='+rincian+'&reg='+reg;
		 						})
							}
						});
				  }
				});
			}
      $(function () {
        $("#example1").dataTable();
      });
    </script>

  </body>
</html>
