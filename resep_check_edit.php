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
$id_resep = isset($_GET['id']) ? $_GET['id'] : '';
$get_resep = $db->prepare("SELECT r.id_resep,r.tgl_resep,r.nomedrek,r.nama,r.ruang,r.dokter FROM resep r WHERE id_resep=:id");
$get_resep->bindParam(":id",$id_resep,PDO::PARAM_INT);
$get_resep->execute();
$data_resep = $get_resep->fetch(PDO::FETCH_ASSOC);
$get_perubahan = $db->prepare("SELECT COUNT(*) as total FROM perubahan_resep WHERE id_resep=:id");
$get_perubahan->bindParam(":id",$id_resep,PDO::PARAM_INT);
$get_perubahan->execute();
$total_perubahan = $get_perubahan->fetch(PDO::FETCH_ASSOC);
$perubahan_rec = isset($total_perubahan['total']) ? $total_perubahan['total'] : 0;
$get_telaah = $db->query("SELECT * FROM telaah_resep WHERE id_resep='".$id_resep."'");
$telaah = $get_telaah->fetch(PDO::FETCH_ASSOC);
//get petugas
$get_petugas = $db->query("SELECT p.id_petugas,peg.nama FROM petugas p INNER JOIN pegawai peg ON(p.id_pegawai=peg.id_pegawai) WHERE p.instalasi='FARMASI' ORDER BY peg.nama");
$data_petugas = $get_petugas->fetchAll(PDO::FETCH_ASSOC);
$check_resep = $db->query("SELECT * FROM telaah_resep WHERE id_resep='".$id_resep."'");
$tl = $check_resep->fetch(PDO::FETCH_ASSOC);
$total_telaah = $check_resep->rowCount();
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
		<link rel="stylesheet" href="../plugins/select2/select2.min.css">
		<!-- iCheck for checkboxes and radio inputs -->
	  <link rel="stylesheet" href="../plugins/iCheck/all.css">
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
      <div class="content-wrapper">
        <!-- Content Header (Page header) -->
        <section class="content-header">
          <h1>
            Pengecekan Resep Awal
            <small>Resep Farmasi</small>
          </h1>
          <ol class="breadcrumb">
            <li><a href="index.php"><i class="fa fa-dashboard"></i> Home</a></li>
          </ol>
        </section>

        <!-- Main content -->
        <section class="content">
					<div class="row">
						<div class="col-xs-12">
						<div class="box box-primary">
							<div class="box-header">
								<h3 class="box-title">Informasi Resep</h3>
							</div>
							<div class="box-body">
								<div class="table-responsive">
									<table class="table table-bordered">
										<tr>
											<th width="20%" class="bg-blue">ID RESEP</th>
											<td class="bg-gray">#<?php echo $id_resep; ?></td>
											<th width="20%" class="bg-blue">Tanggal Resep</th>
											<td class="bg-gray"><?php echo $data_resep['tgl_resep']; ?></td>
										</tr>
										<tr>
											<th width="20%" class="bg-blue">No.Rekam Medis</th>
											<td class="bg-gray"><?php echo $data_resep['nomedrek']; ?></td>
											<th width="20%" class="bg-blue">Nama Pasien</th>
											<td class="bg-gray"><?php echo $data_resep['nama']; ?></td>
										</tr>
										<tr>
											<th width="20%" class="bg-blue">Ruangan</th>
											<td class="bg-gray"><?php echo $data_resep['ruang']; ?></td>
											<th width="20%" class="bg-blue">Dokter</th>
											<td class="bg-gray"><?php echo $data_resep['dokter']; ?></td>
										</tr>
									</table>
								</div>
							</div>
						</div>
						</div>
					</div>
					<div class="row">
						<div class="col-xs-6">
							<div class="box box-success">
								<div class="box-header with-border">
										<h3 class="box-title">Data Telaah Resep Awal</h3>
								</div>
								<form action="resep_check_edit_acc.php" method="POST">
									<input type="hidden" name="id_resep" id="id_resep" value="<?php echo $id_resep; ?>">
								<div class="box-body">
									<div class="row">
										<div class="col-xs-6">
											<div class="form-group">
											  <label for="">Riwayat Alergi Obat <span style="color:red">*</span></label><br>
												<?php if($telaah['alergi']=='ya'){ ?>
													<input type="radio" class="flat-green" name="alergi" id="alergi1" value="tidak" required> Tidak <br>
													<input type="radio" class="flat-green" name="alergi" id="alergi2" value="ya" checked required> Ya, Nama Obat :
													<input type="text" class="form-control" name="alergi_text" id="alergi_text" placeholder="Masukan Alergi jika ada" value="<?php echo $telaah['alergi_text']; ?>">
												<?php	}else{ ?>
													<input type="radio" class="flat-green" name="alergi" id="alergi1" value="tidak" checked required> Tidak <br>
													<input type="radio" class="flat-green" name="alergi" id="alergi2" value="ya" required> Ya, Nama Obat :
													<input type="text" class="form-control" name="alergi_text" id="alergi_text" placeholder="Masukan Alergi jika ada">
												<?php	} ?>
											</div>
										</div>
										<div class="col-xs-6">
											<input type="hidden" name="perubahan_rec" id="perubahan_rec" value="<?php echo $perubahan_rec; ?>">
											<div class="form-group">
											  <label for="">Apakah Ada Perubahan Resep? <span style="color:red">*</span></label>
											  <select name="perubahan" id="perubahan" class="form-control" required>
											  	<option value="">--Pilih Salah Satu---</option>
													<?php
														if($perubahan_rec>0){
															echo '<option value="ya" selected>Ya</option>';
														}else{
															echo '<option value="ya">Ya</option>';
														}
													?>
													<option value="tidak">Tidak</option>
											  </select>
											</div>
										</div>
									</div>
									<br>
									<div class="table-responsive">
										<table class="table table-responsive table-bordered" width="100%">
											<thead>
												<tr class="info">
													<th><label class="label-control" for="">Aspek Telaah</label></th>
													<th>Ya</th>
													<th>Tidak</th>
													<th>Keterangan</th>
												</tr>
											</thead>
											<tbody>
												<?php if($total_telaah>0){ ?>
													<tr>
														<th>Tulisan Jelas <span style="color:red">*</span></th>
															<td><input type="radio" class="flat-green" name="tj" id="tj1" value="ya" <?php if($tl['tulisan_jelas']=='ya'){ echo "checked"; } ?> required></td>
															<td><input type="radio" class="flat-green" name="tj" id="tj2" value="tidak" <?php if($tl['tulisan_jelas']=='tidak'){ echo "checked"; } ?> required></td>
															<td><input type="text" class="form-control" name="tj_text" id="" value="<?php echo $telaah['tulisan_jelas_ket']; ?>"></td>
													</tr>
													<tr>
														<th>Benar Nama Pasien <span style="color:red">*</span></th>
															<td><input type="radio" class="flat-green" name="bn" id="bn1" value="ya" <?php if($tl['benar_nama_pasien']=='ya'){ echo "checked"; } ?> required></td>
															<td><input type="radio" class="flat-green" name="bn" id="bn2" value="tidak" <?php if($tl['benar_nama_pasien']=='tidak'){ echo "checked"; } ?> required></td>
															<td><input type="text" class="form-control" name="bn_text" id="" value="<?php echo $telaah['benar_nama_pasien_ket']; ?>"></td>
													</tr>
													<tr>
														<th>Benar Nama Obat <span style="color:red">*</span></th>
														<td><input type="radio" class="flat-green" name="bo" id="bo1" value="ya" <?php if($tl['benar_nama_obat']=='ya'){ echo "checked"; } ?> required></td>
															<td><input type="radio" class="flat-green" name="bo" id="bo2" value="tidak" <?php if($tl['benar_nama_obat']=='tidak'){ echo "checked"; } ?> required></td>
															<td><input type="text" class="form-control" name="bo_text" id=""  value="<?php echo $telaah['benar_nama_obat_ket']; ?>"></td>
													</tr>
													<tr>
														<th>Benar Kekuatan Obat <span style="color:red">*</span></th>
															<td><input type="radio" class="flat-green" name="bk" id="bk1" value="ya" <?php if($tl['benar_kekuatan_obat']=='ya'){ echo "checked"; } ?> required></td>
															<td><input type="radio" class="flat-green" name="bk" id="bk2" value="tidak" <?php if($tl['benar_kekuatan_obat']=='tidak'){ echo "checked"; } ?> required></td>
															<td><input type="text" class="form-control" name="bk_text" id=""  value="<?php echo $telaah['benar_kekuatan_obat_ket']; ?>"></td>
													</tr>
													<tr>
														<th>Benar Frekuensi Pemberian <span style="color:red">*</span></th>
															<td><input type="radio" class="flat-green" name="bp" id="bp1" value="ya" <?php if($tl['benar_frekuensi_pemberian']=='ya'){ echo "checked"; } ?> required></td>
															<td><input type="radio" class="flat-green" name="bp" id="bp2" value="tidak" <?php if($tl['benar_frekuensi_pemberian']=='tidak'){ echo "checked"; } ?> required></td>
															<td><input type="text" class="form-control" name="bp_text" id=""  value="<?php echo $telaah['benar_frekuensi_pemberian_ket']; ?>"></td>
													</tr>
													<tr>
														<th>Benar Dosis <span style="color:red">*</span></th>
															<td><input type="radio" class="flat-green" name="bd" id="bd1" value="ya" <?php if($tl['benar_dosis']=='ya'){ echo "checked"; } ?> required></td>
															<td><input type="radio" class="flat-green" name="bd" id="bd2" value="tidak" <?php if($tl['benar_dosis']=='tidak'){ echo "checked"; } ?> required></td>
															<td><input type="text" class="form-control" name="bd_text" id=""  value="<?php echo $telaah['benar_dosis_ket']; ?>"></td>
													</tr>
													<tr>
														<th>Ada duplikasi obat <span style="color:red">*</span></th>
															<td><input type="radio" class="flat-green" name="ad" id="ad1" value="ya" <?php if($tl['ada_duplikasi_obat']=='ya'){ echo "checked"; } ?> required></td>
															<td><input type="radio" class="flat-green" name="ad" id="ad2" value="tidak" <?php if($tl['ada_duplikasi_obat']=='tidak'){ echo "checked"; } ?> required></td>
															<td><input type="text" class="form-control" name="ad_text" id=""  value="<?php echo $telaah['ada_duplikasi_obat_ket']; ?>"></td>
													</tr>
													<tr>
														<th>Ada Interaksi Obat <span style="color:red">*</span></th>
															<td><input type="radio" class="flat-green" name="ai" id="ai1" value="ya"  <?php if($tl['ada_interaksi_obat']=='ya'){ echo "checked"; } ?> required></td>
															<td><input type="radio" class="flat-green" name="ai" id="ai2" value="tidak"  <?php if($tl['ada_interaksi_obat']=='tidak'){ echo "checked"; } ?> checked required></td>
															<td><input type="text" class="form-control" name="ai_text" id=""  value="<?php echo $telaah['ada_interaksi_obat_ket']; ?>"></td>
													</tr>
													<tr>
														<th>Ada Antibiotik Ganda <span style="color:red">*</span></th>
															<td><input type="radio" class="flat-green" name="ag" id="ag1" value="ya" <?php if($tl['antibiotik_ganda']=='ya'){ echo "checked"; } ?> required></td>
															<td><input type="radio" class="flat-green" name="ag" id="ag2" value="tidak" <?php if($tl['antibiotik_ganda']=='tidak'){ echo "checked"; } ?> required></td>
															<td><input type="text" class="form-control" name="ag_text" id=""  value="<?php echo $telaah['antibiotik_ganda_ket']; ?>"></td>
													</tr>
												<?php }else{ ?>
													<tr>
														<th>Tulisan Jelas <span style="color:red">*</span></th>
															<td><input type="radio" class="flat-green" name="tj" id="tj1" value="ya" checked required></td>
															<td><input type="radio" class="flat-green" name="tj" id="tj2" value="tidak" required></td>
															<td><input type="text" class="form-control" name="tj_text" id="" value="<?php echo $telaah['tulisan_jelas_ket']; ?>"></td>
													</tr>
													<tr>
														<th>Benar Nama Pasien <span style="color:red">*</span></th>
															<td><input type="radio" class="flat-green" name="bn" id="bn1" value="ya" checked required></td>
															<td><input type="radio" class="flat-green" name="bn" id="bn2" value="tidak" required></td>
															<td><input type="text" class="form-control" name="bn_text" id="" value="<?php echo $telaah['benar_nama_pasien_ket']; ?>"></td>
													</tr>
													<tr>
														<th>Benar Nama Obat <span style="color:red">*</span></th>
														<td><input type="radio" class="flat-green" name="bo" id="bo1" value="ya" checked required></td>
															<td><input type="radio" class="flat-green" name="bo" id="bo2" value="tidak" required></td>
															<td><input type="text" class="form-control" name="bo_text" id=""  value="<?php echo $telaah['benar_nama_obat_ket']; ?>"></td>
													</tr>
													<tr>
														<th>Benar Kekuatan Obat <span style="color:red">*</span></th>
															<td><input type="radio" class="flat-green" name="bk" id="bk1" value="ya" checked required></td>
															<td><input type="radio" class="flat-green" name="bk" id="bk2" value="tidak" required></td>
															<td><input type="text" class="form-control" name="bk_text" id=""  value="<?php echo $telaah['benar_kekuatan_obat_ket']; ?>"></td>
													</tr>
													<tr>
														<th>Benar Frekuensi Pemberian <span style="color:red">*</span></th>
															<td><input type="radio" class="flat-green" name="bp" id="bp1" value="ya" checked required></td>
															<td><input type="radio" class="flat-green" name="bp" id="bp2" value="tidak" required></td>
															<td><input type="text" class="form-control" name="bp_text" id=""  value="<?php echo $telaah['benar_frekuensi_pemberian_ket']; ?>"></td>
													</tr>
													<tr>
														<th>Benar Dosis <span style="color:red">*</span></th>
															<td><input type="radio" class="flat-green" name="bd" id="bd1" value="ya" checked required></td>
															<td><input type="radio" class="flat-green" name="bd" id="bd2" value="tidak" required></td>
															<td><input type="text" class="form-control" name="bd_text" id=""  value="<?php echo $telaah['benar_dosis_ket']; ?>"></td>
													</tr>
													<tr>
														<th>Ada duplikasi obat <span style="color:red">*</span></th>
															<td><input type="radio" class="flat-green" name="ad" id="ad1" value="ya" required></td>
															<td><input type="radio" class="flat-green" name="ad" id="ad2" value="tidak" checked required></td>
															<td><input type="text" class="form-control" name="ad_text" id=""  value="<?php echo $telaah['ada_duplikasi_obat_ket']; ?>"></td>
													</tr>
													<tr>
														<th>Ada Interaksi Obat <span style="color:red">*</span></th>
															<td><input type="radio" class="flat-green" name="ai" id="ai1" value="ya" required></td>
															<td><input type="radio" class="flat-green" name="ai" id="ai2" value="tidak" checked required></td>
															<td><input type="text" class="form-control" name="ai_text" id=""  value="<?php echo $telaah['ada_interaksi_obat_ket']; ?>"></td>
													</tr>
													<tr>
														<th>Ada Antibiotik Ganda <span style="color:red">*</span></th>
															<td><input type="radio" class="flat-green" name="ag" id="ag1" value="ya"  required></td>
															<td><input type="radio" class="flat-green" name="ag" id="ag2" value="tidak" checked required></td>
															<td><input type="text" class="form-control" name="ag_text" id=""  value="<?php echo $telaah['antibiotik_ganda_ket']; ?>"></td>
													</tr>
												<?php } ?>
											</tbody>
										</table>
									</div>
								</div>
								<div class="box-footer">
									<button type="submit" class="btn btn-primary"><i class="fa fa-save"></i> Simpan</button>
								</div>
								</form>
							</div>
						</div>
						<div id="perubahan_view" class="col-xs-6">
							<div class="row">
								<div class="col-xs-12">
									<div class="box box-warning">
										<div class="box-header">
											<h3 class="box-title">Data Perubahan Resep</h3>
											<div class="pull-right">
												<button class="btn btn-sm btn-primary" data-toggle="modal" data-target="#myModal"><i class="fa fa-plus"></i> Tambah Data</button>
											</div>
										</div>
										<div class="box-body">
											<div class="table-responsive">
												<table id="rubah" class="table table-striped table-bordered" width="100%">
													<thead>
														<tr>
															<th>#</th>
															<th>TERTULIS</th>
															<th>MENJADI</th>
															<th>Approval</th>
															<th>Aksi</th>
														</tr>
													</thead>
												</table>
											</div>
										</div>
									</div>
								</div>
							</div>
						</div>
						<!-- Modal -->
					  <div class="modal fade" id="myModal" role="dialog">
					    <div class="modal-dialog">

					      <!-- Modal content-->
					      <div class="modal-content">
					        <div class="modal-header bg-blue">
					          <button type="button" class="close" data-dismiss="modal">&times;</button>
					          <h4 class="modal-title">Data Perubahan Resep</h4>
					        </div>
									<form action="">
						        <div class="modal-body">
											<input type="hidden" name="id_resep" id="id_resep" value="<?php echo $id_resep; ?>">
						          <div class="form-group">
						            <label for="">Tertulis <span style="color:red">*</span></label>
						            <textarea name="tulis" id="tulis" class="form-control" rows="3" cols="3" required></textarea>
						          </div>
											<div class="form-group">
						            <label for="">Menjadi <span style="color:red">*</span></label>
						            <textarea name="jadi" id="jadi" class="form-control" rows="3" cols="3" required></textarea>
						          </div>
											<div class="form-group">
											  <label for="">Petugas Farmasi</label>
											  <select name="petugas_farmasi" id="petugas_farmasi" class="form-control select2" style="width:100%;">
											  	<option value=""></option>
													<?php
														foreach ($data_petugas as $dp) {
															echo '<option value="'.$dp['id_petugas'].'">'.$dp['nama'].'</option>';
														}
													?>
											  </select>
											</div>
						        </div>
						        <div class="modal-footer">
											<button id="rubahBtn" type="button" class="btn btn-primary"><i class="fa fa-save"></i> Simpan</button>
						          <button type="button" class="btn btn-danger" data-dismiss="modal"><i class="fa fa-times"></i> Close</button>
						        </div>
									</form>
					      </div>

					    </div>
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
		<!-- iCheck 1.0.1 -->
		<script src="../plugins/iCheck/icheck.min.js"></script>
		<!-- select2 -->
		<script src="../plugins/select2/select2.full.min.js" type="text/javascript"></script>
    <!-- SlimScroll -->
    <script src="../plugins/slimScroll/jquery.slimscroll.min.js" type="text/javascript"></script>
    <!-- FastClick -->
    <script src='../plugins/fastclick/fastclick.min.js'></script>
    <!-- AdminLTE App -->
    <script src="../dist/js/app.min.js" type="text/javascript"></script>
		<script src="../plugins/sweetalert/sweetalert.min.js" type="text/javascript">

		</script>
    <!-- page script -->
    <script type="text/javascript">
			var rincian_resep;
			var master_resep;
      $(function () {
				var perubahan_block = $('#perubahan_view');
				var perubahan_data = $('#perubahan').val();
				if(perubahan_data=='ya'){
					perubahan_block.show();
				}else{
					perubahan_block.hide();
				}
				$('#perubahan').on("change",function(){
					var ubah = $('#perubahan').val();
					if(ubah=='ya'){
						perubahan_block.show();
					}else{
						perubahan_block.hide();
					}
				});
				$('.modal').on('hidden.bs.modal', function(){
				    $(this).find('form')[0].reset();
						$('#petugas_farmasi').val("").trigger("change");
				});
				master_resep = $('#rubah').DataTable({
					"processing" : true,
					"serverSide" : true,
					"ajax": "ajax_data/data_perubahan_resep.php?r=<?php echo $id_resep; ?>",
					"columns" :[
						{ "data" : "id_perubahan_resep"	},
						{ "data" : "tertulis"	},
						{ "data" : "menjadi"	},
						{ "data" : "nama"	},
						{
							"data" : "id_perubahan_resep",
							"render" : function(data, type, row, meta ){
								var btn = '<button class="btn btn-sm btn-danger" onclick="hapus_perubahan('+data+')"><i class="fa fa-trash"></i> Hapus</button>';
								return btn;
							}
						},
					],
					"order": [[ 1, "asc" ]]
				});
				$('#rubahBtn').on("click",function(event){
					event.preventDefault();
					var id_resep = $('#id_resep').val();
					var tertulis = $('#tulis').val();
					var menjadi = $('#jadi').val();
					var petugas_farmasi = $('#petugas_farmasi').val();
					console.log(petugas_farmasi);
					if(tertulis==""){
						swal({
						  title: "Peringatan!",
						  text: "Field Tertulis Belum diisi!",
						  icon: "warning",
						  button: "Tutup",
						});
					}else if(menjadi==""){
						swal({
						  title: "Peringatan!",
						  text: "Field Menjadi Belum diisi!",
						  icon: "warning",
						  button: "Tutup",
						});
					}else{
						// post ajax
						var fd = new FormData();
						fd.append('task',"add");
						fd.append('id',id_resep);
						fd.append('tulis',tertulis);
						fd.append('jadi',menjadi);
						fd.append('petugas',petugas_farmasi);
						$.ajax({
								type: "POST",
								url: "ajax_data/perubahan_resep.php",
								data: fd,
								contentType: false,
								cache: false,
								processData:false,
								success: function (respon) {
									console.log(respon);
									swal({
									  title: "Berhasil!",
									  text: "Data Perubahan Berhasil ditambahkan",
									  icon: "success",
									  button: "OK!",
									}).then((value)=>{
										$('#myModal').modal('hide');
										master_resep.ajax.reload();
									});
								},
								error: function (e) {
									alert(e);
										// console.log("ERROR : ", e.responseText);
										master_resep.ajax.reload();
								}
						});
					}
				});
				//Flat red color scheme for iCheck
		    $('input[type="checkbox"].flat-green, input[type="radio"].flat-green').iCheck({
		      checkboxClass: 'icheckbox_flat-green',
		      radioClass: 'iradio_flat-green'
		    });
				$('.select2').select2({
					placeholder : 'Masukan / Pilih Nama Petugas',
					allowClear : true,
					width : 'resolve'
				});
      });
			function hapus_perubahan(id){
				// post ajax
				var fd = new FormData();
				fd.append('task',"delete");
				fd.append('id_perubahan_resep',id);
				$.ajax({
						type: "POST",
						url: "ajax_data/perubahan_resep.php",
						data: fd,
						contentType: false,
						cache: false,
						processData:false,
						success: function (respon) {
							console.log(respon);
							swal({
								title: "Berhasil!",
								text: "Data Perubahan Berhasil dihapus",
								icon: "success",
								button: "OK!",
							}).then((value)=>{
								$('#myModal').modal('hide');
								master_resep.ajax.reload();
							});
						},
						error: function (e) {
							alert(e);
								master_resep.ajax.reload();
						}
				});
			}
    </script>

  </body>
</html>
