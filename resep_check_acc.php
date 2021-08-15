<?php
session_start();
include("../inc/pdo.conf.php");
include("../inc/version.php");
ini_set('display_errors','1');
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
$id_petugas = $r1['mem_id'];
$id_resep = isset($_POST['id_resep']) ? $_POST['id_resep'] : '';
$alergi = isset($_POST['alergi']) ? $_POST['alergi'] : '';
$alergi_text = isset($_POST['alergi_text']) ? $_POST['alergi_text'] : '';
$perubahan_rec = isset($_POST['perubahan_rec']) ? $_POST['perubahan_rec'] : '';
$perubahan = isset($_POST['perubahan']) ? $_POST['perubahan'] : '';
$tulisan_jelas = isset($_POST['tj']) ? $_POST['tj'] : '';
$tulisan_jelas_ket = isset($_POST['tj_text']) ? $_POST['tj_text'] : '';
$benar_nama = isset($_POST['bn']) ? $_POST['bn'] : '';
$benar_nama_ket = isset($_POST['bn_text']) ? $_POST['bn_text'] : '';
$benar_obat = isset($_POST['bo']) ? $_POST['bo'] : '';
$benar_obat_ket = isset($_POST['bo_text']) ? $_POST['bo_text'] : '';
$benar_kekuatan = isset($_POST['bk']) ? $_POST['bk'] : '';
$benar_kekuatan_ket = isset($_POST['bk_text']) ? $_POST['bk_text'] : '';
$benar_frekuensi = isset($_POST['bp']) ? $_POST['bp'] : '';
$benar_frekuensi_ket = isset($_POST['bp_text']) ? $_POST['bp_text'] : '';
$benar_dosis = isset($_POST['bd']) ? $_POST['bd'] : '';
$benar_dosis_ket = isset($_POST['bd_text']) ? $_POST['bd_text'] : '';
$ada_duplikasi = isset($_POST['ad']) ? $_POST['ad'] : '';
$ada_duplikasi_ket = isset($_POST['ad_text']) ? $_POST['ad_text'] : '';
$ada_interaksi= isset($_POST['ai']) ? $_POST['ai'] : '';
$ada_interaksi_ket= isset($_POST['ai_text']) ? $_POST['ai_text'] : '';
$antibiotik_ganda = isset($_POST['ag']) ? $_POST['ag'] : '';
$antibiotik_ganda_ket = isset($_POST['ag_text']) ? $_POST['ag_text'] : '';
var_dump($_POST);
try {
	// check duplicate
	$check_resep = $db->query("SELECT COUNT(*) as dup FROM telaah_resep WHERE id_resep='".$id_resep."'");
	$cr = $check_resep->fetch(PDO::FETCH_ASSOC);
	if($cr['dup']>0){
		echo 'duplikat';
		$stmt = $db->prepare("UPDATE `telaah_resep` SET
			`alergi`=:alergi,`alergi_text`=:alergi_text,
			`perubahan_resep`=:perubahan,
			`tulisan_jelas`=:tj, `tulisan_jelas_ket`=:tj_text,
			`benar_nama_pasien`=:bn, `benar_nama_pasien_ket`=:bn_text,
			`benar_nama_obat`=:bo, `benar_nama_obat_ket`=:bo_text,
			`benar_kekuatan_obat`=:bk, `benar_kekuatan_obat_ket`=:bk_text,
			`benar_frekuensi_pemberian`=:bp, `benar_frekuensi_pemberian_ket`=:bp_text,
			`benar_dosis`=:bd, `benar_dosis_ket`=:bd_text,
			`ada_duplikasi_obat`=:ad, `ada_duplikasi_obat_ket`=:ad_text,
			`ada_interaksi_obat`=:ai, `ada_interaksi_obat_ket`=:ai_text,
			`antibiotik_ganda`=:ag,`antibiotik_ganda_ket`=:ag_text,
			`mem_id`=:mem_id WHERE `id_resep`=:id_resep");
		$stmt->bindParam(":alergi",$alergi,PDO::PARAM_STR);
		$stmt->bindParam(":alergi_text",$alergi_text,PDO::PARAM_STR);
		$stmt->bindParam(":perubahan",$perubahan,PDO::PARAM_STR);
		$stmt->bindParam(":tj",$tulisan_jelas,PDO::PARAM_STR);
		$stmt->bindParam(":tj_text",$tulisan_jelas_ket,PDO::PARAM_STR);
		$stmt->bindParam(":bn",$benar_nama,PDO::PARAM_STR);
		$stmt->bindParam(":bn_text",$benar_nama_ket,PDO::PARAM_STR);
		$stmt->bindParam(":bo",$benar_obat,PDO::PARAM_STR);
		$stmt->bindParam(":bo_text",$benar_obat_ket,PDO::PARAM_STR);
		$stmt->bindParam(":bk",$benar_kekuatan,PDO::PARAM_STR);
		$stmt->bindParam(":bk_text",$benar_kekuatan_ket,PDO::PARAM_STR);
		$stmt->bindParam(":bp",$benar_frekuensi,PDO::PARAM_STR);
		$stmt->bindParam(":bp_text",$benar_frekuensi_ket,PDO::PARAM_STR);
		$stmt->bindParam(":bd",$benar_dosis,PDO::PARAM_STR);
		$stmt->bindParam(":bd_text",$benar_dosis_ket,PDO::PARAM_STR);
		$stmt->bindParam(":ad",$ada_duplikasi,PDO::PARAM_STR);
		$stmt->bindParam(":ad_text",$ada_duplikasi_ket,PDO::PARAM_STR);
		$stmt->bindParam(":ai",$ada_interaksi,PDO::PARAM_STR);
		$stmt->bindParam(":ai_text",$ada_interaksi_ket,PDO::PARAM_STR);
		$stmt->bindParam(":ag",$antibiotik_ganda,PDO::PARAM_STR);
		$stmt->bindParam(":ag_text",$antibiotik_ganda_ket,PDO::PARAM_STR);
		$stmt->bindParam(":mem_id",$id_petugas,PDO::PARAM_INT);
		$stmt->bindParam(":id_resep",$id_resep,PDO::PARAM_INT);
		$stmt->execute();
	}else{
		$stmt = $db->prepare("INSERT INTO `telaah_resep`(`alergi`,`alergi_text`,`perubahan_resep`,`tulisan_jelas`, `tulisan_jelas_ket`, `benar_nama_pasien`, `benar_nama_pasien_ket`, `benar_nama_obat`, `benar_nama_obat_ket`, `benar_kekuatan_obat`, `benar_kekuatan_obat_ket`, `benar_frekuensi_pemberian`, `benar_frekuensi_pemberian_ket`, `benar_dosis`, `benar_dosis_ket`, `ada_duplikasi_obat`, `ada_duplikasi_obat_ket`, `ada_interaksi_obat`, `ada_interaksi_obat_ket`, `antibiotik_ganda`,`antibiotik_ganda_ket`,`id_resep`,`mem_id`)VALUES (:alergi,:alergi_text,:perubahan,:tj,:tj_text,:bn,:bn_text,:bo,:bo_text,:bk,:bk_text,:bp,:bp_text,:bd,:bd_text,:ad,:ad_text,:ai,:ai_text,:ag,:ag_text,:id_resep,:mem_id)");
		$stmt->bindParam(":alergi",$alergi,PDO::PARAM_STR);
		$stmt->bindParam(":alergi_text",$alergi_text,PDO::PARAM_STR);
		$stmt->bindParam(":perubahan",$perubahan,PDO::PARAM_STR);
		$stmt->bindParam(":tj",$tulisan_jelas,PDO::PARAM_STR);
		$stmt->bindParam(":tj_text",$tulisan_jelas_ket,PDO::PARAM_STR);
		$stmt->bindParam(":bn",$benar_nama,PDO::PARAM_STR);
		$stmt->bindParam(":bn_text",$benar_nama_ket,PDO::PARAM_STR);
		$stmt->bindParam(":bo",$benar_obat,PDO::PARAM_STR);
		$stmt->bindParam(":bo_text",$benar_obat_ket,PDO::PARAM_STR);
		$stmt->bindParam(":bk",$benar_kekuatan,PDO::PARAM_STR);
		$stmt->bindParam(":bk_text",$benar_kekuatan_ket,PDO::PARAM_STR);
		$stmt->bindParam(":bp",$benar_frekuensi,PDO::PARAM_STR);
		$stmt->bindParam(":bp_text",$benar_frekuensi_ket,PDO::PARAM_STR);
		$stmt->bindParam(":bd",$benar_dosis,PDO::PARAM_STR);
		$stmt->bindParam(":bd_text",$benar_dosis_ket,PDO::PARAM_STR);
		$stmt->bindParam(":ad",$ada_duplikasi,PDO::PARAM_STR);
		$stmt->bindParam(":ad_text",$ada_duplikasi_ket,PDO::PARAM_STR);
		$stmt->bindParam(":ai",$ada_interaksi,PDO::PARAM_STR);
		$stmt->bindParam(":ai_text",$ada_interaksi_ket,PDO::PARAM_STR);
		$stmt->bindParam(":ag",$antibiotik_ganda,PDO::PARAM_STR);
		$stmt->bindParam(":ag_text",$antibiotik_ganda_ket,PDO::PARAM_STR);
		$stmt->bindParam(":id_resep",$id_resep,PDO::PARAM_INT);
		$stmt->bindParam(":mem_id",$id_petugas,PDO::PARAM_INT);
		$stmt->execute();
	}
	header('location:keluar.php?id='.$id_resep.'&trans=Resep');
} catch (PDOException $e) {
	echo $e->getMessage();
}
