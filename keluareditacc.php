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
//get var
$id_resep=isset($_GET['id']) ? $_GET["id"] :'';
$tanggal=isset($_POST['tgglkeluar']) ? $_POST["tgglkeluar"] : '';
$id_warehouse_stok = isset($_POST['namaobat']) ? $_POST['namaobat'] : '';
$volume_input =isset($_POST['volume']) ? $_POST["volume"] : '';
$tuslah= isset($_POST['tuslah']) ? $_POST["tuslah"] : '';
$jenis_rawat = "rajal";
$new_date = explode('/',$tanggal);
$tanggal_out = $new_date[2]."-".$new_date[1]."-".$new_date[0];
$id_petugas = $r1['mem_id'];
//ambil value obat
$h3=$db->query("SELECT * FROM warehouse_stok ws INNER JOIN gobat g ON(g.id_obat=ws.id_obat) WHERE ws.id_warehouse_stok='".$id_warehouse_stok."'");
$r3=$h3->fetch(PDO::FETCH_ASSOC);
$stok_ruangan=$r3["stok"];
$id_obat = $r3['id_obat'];
$namaobat = $r3['nama'];
$sumber = $r3['sumber'];
$id_warehouse = $r3['id_warehouse'];

if ($volume_input > $stok_ruangan || $stok_ruangan <= 0){
	header("location:keluar.php?id=$id_resep&status=3");
	exit;
}
//getlist kartu_stok_ruangan
$kartu_list = $db->query("SELECT * FROM kartu_stok_ruangan WHERE volume_kartu_akhir<>0 AND id_obat='".$id_obat."' AND id_warehouse='".$id_warehouse."' and in_out='masuk' ORDER BY created_at ASC");
$kartu = $kartu_list->fetchAll(PDO::FETCH_ASSOC);
$total_data = $kartu_list->rowCount();
$finish = false;
$sisa_keluar =0;
$tujuan = "Pasien";
$keterangan = "Resep/".$id_resep;
$volume_in = 0;
$created_at = date('Y-m-d H:i:s');
$i=0;
$in_out="keluar";
// var_dump($kartu[$i]);
if($total_data>0){
	while($finish==false){
		//get tuslah yang aktif
		$tuslah_list = $db->query("SELECT * FROM tuslah WHERE id_tuslah='".$kartu[$i]['id_tuslah']."'");
		$tus = $tuslah_list->fetch(PDO::FETCH_ASSOC);
		$hargasatuan = $kartu[$i]['harga_beli'] * 1.2;
		$volume_kartu = $kartu[$i]['volume_kartu_akhir'];

		//cek apakah volume mencukupi
		if($volume_kartu>=$volume_input){
			//stok pengeluaran terpenuhi
			$sisa_keluar = 0;
			$volume_kartu_akhir = $volume_kartu - $volume_input;
			$volume_out = $volume_input;
			$stok_ruangan = $stok_ruangan - $volume_input;
		}else{
			$sisa_keluar = $volume_input - $volume_kartu;
			$stok_ruangan = $stok_ruangan - $volume_kartu;
			$volume_kartu_akhir = 0;
			$volume_out = $kartu[$i]['volume_kartu_akhir'];
		}
    $volume_akhir = 0;
		//logika tuslah
		if ($tuslah==1) {
			$hargatuslah=($hargasatuan*$volume_out)+$tus['rajal'];
		} else if ($tuslah==2) {
			$hargatuslah=($hargasatuan*$volume_out)+$tus['rajal_racik'];
		} else if ($tuslah==3) {
			$voltuslah=$volume_out*$tus['ranap'];
			$hargatuslah=($hargasatuan*$volume_out)+$voltuslah;
		} else if ($tuslah==4) {
			$voltuslah=$volume_out*$tus['ranap_racik'];
			$hargatuslah=($hargasatuan*$volume_out)+$voltuslah;
		} else {
			$hargatuslah=$hargasatuan*$volume_out;
		}
		$id_kartu_awal = $kartu[$i]['id_kartu_ruangan'];
		// echo $hargasatuan." | ".$volume_out." | ".$hargatuslah."<br>";
		//update volume_kartu_akhir berdasarkan data on point
		$update_vol = $db->query("UPDATE kartu_stok_ruangan SET volume_kartu_akhir='".$volume_kartu_akhir."' WHERE id_kartu_ruangan='".$id_kartu_awal."'");
		//insert into kartu_stok_ruangan in_out=keluar
		$ins_kartu = $db->prepare("INSERT INTO `kartu_stok_ruangan`(`id_kartu_gobat`, `id_obat`, `id_warehouse`, `sumber_dana`, `volume_kartu_awal`, `volume_kartu_akhir`, `volume_sisa`, `in_out`, `tujuan`, `volume_in`, `volume_out`, `expired`, `no_batch`, `harga_beli`, `harga_jual`, `id_tuslah`, `created_at`, `keterangan`,`ref`,`mem_id`) VALUES(:id_kartu_gobat,:id_obat,:id_warehouse,:sumber_dana,:volume_kartu_awal,:volume_kartu_akhir,:volume_sisa,:in_out,:tujuan,:volume_in,:volume_out,:expired,:no_batch,:harga_beli,:harga_jual,:id_tuslah,:created_at,:keterangan,:ref,:mem_id)");
		$ins_kartu->bindParam(":id_kartu_gobat",$kartu[$i]['id_kartu_gobat'],PDO::PARAM_INT);
		$ins_kartu->bindParam(":id_obat",$id_obat,PDO::PARAM_INT);
		$ins_kartu->bindParam(":id_warehouse",$id_warehouse,PDO::PARAM_INT);
		$ins_kartu->bindParam(":sumber_dana",$kartu[$i]['sumber_dana'],PDO::PARAM_STR);
		$ins_kartu->bindParam(":volume_kartu_awal",$kartu[$i]['volume_kartu_awal'],PDO::PARAM_INT);
		$ins_kartu->bindParam(":volume_kartu_akhir",$volume_akhir,PDO::PARAM_INT);
		$ins_kartu->bindParam(":volume_sisa",$volume_kartu_akhir,PDO::PARAM_INT);
		$ins_kartu->bindParam(":in_out",$in_out,PDO::PARAM_STR);
		$ins_kartu->bindParam(":tujuan",$tujuan,PDO::PARAM_STR);
		$ins_kartu->bindParam(":volume_in",$volume_in,PDO::PARAM_INT);
		$ins_kartu->bindParam(":volume_out",$volume_out,PDO::PARAM_INT);
		$ins_kartu->bindParam(":expired",$kartu[$i]['expired'],PDO::PARAM_STR);
		$ins_kartu->bindParam(":no_batch",$kartu[$i]['no_batch'],PDO::PARAM_STR);
		$ins_kartu->bindParam(":harga_beli",$kartu[$i]['harga_beli'],PDO::PARAM_INT);
		$ins_kartu->bindParam(":harga_jual",$hargatuslah,PDO::PARAM_INT);
		$ins_kartu->bindParam(":id_tuslah",$kartu[$i]['id_tuslah'],PDO::PARAM_INT);
		$ins_kartu->bindParam(":created_at",$created_at,PDO::PARAM_STR);
		$ins_kartu->bindParam(":keterangan",$keterangan,PDO::PARAM_STR);
		$ins_kartu->bindParam(":ref",$id_kartu_awal,PDO::PARAM_INT);
		$ins_kartu->bindParam(":mem_id",$id_petugas,PDO::PARAM_INT);
		$ins_kartu->execute();
		$id_kartu_ruangan = $db->lastInsertId();
		// insert into apotekkeluar
		$result2 = $db->query("INSERT INTO apotekkeluar(id_resep,tanggal,id_obat,namaobat,sumber,volume,harga,total,tuslah) VALUES ('".$id_resep."','".$tanggal."','".$id_obat."','".$namaobat."','".$sumber."','".$volume_out."','".$hargasatuan."','".$hargatuslah."','".$tuslah."')");
		$id_obatkeluar = $db->lastInsertId();
		// warehouse keluar
		$ins_out = $db->prepare("INSERT INTO `warehouse_out`(`tanggal_keluar`,`id_kartu_ruangan`,`id_obatkeluar`,`id_warehouse_stok`, `volume`, `harga_beli`, `harga_satuan`, `total_harga`,`jenis_rawat`,`id_resep`) VALUES
		(:tanggal_keluar,:id_kartu_ruangan,:id_obatkeluar,:id_warehouse_stok,:volume,:harga_beli,:harga_satuan,:total_harga,:jenis_rawat,:id_resep)");
		$ins_out->bindParam(":tanggal_keluar",$tanggal_out,PDO::PARAM_STR);
		$ins_out->bindParam(":id_kartu_ruangan",$id_kartu_ruangan,PDO::PARAM_INT);
		$ins_out->bindParam(":id_obatkeluar",$id_obatkeluar,PDO::PARAM_INT);
		$ins_out->bindParam(":id_warehouse_stok",$id_warehouse_stok,PDO::PARAM_INT);
		$ins_out->bindParam(":volume",$volume_out,PDO::PARAM_INT);
		$ins_out->bindParam(":harga_beli",$kartu[$i]['harga_beli'],PDO::PARAM_INT);
		$ins_out->bindParam(":harga_satuan",$hargasatuan,PDO::PARAM_INT);
		$ins_out->bindParam(":total_harga",$hargatuslah,PDO::PARAM_INT);
		$ins_out->bindParam(":jenis_rawat",$jenis_rawat,PDO::PARAM_STR);
		$ins_out->bindParam(":id_resep",$id_resep,PDO::PARAM_INT);
		$ins_out->execute();
		if($sisa_keluar!=0){
			//pointer goes to next data
			$i++;
			$volume_input = $sisa_keluar;
		}else{
			// exit loop
			$finish=true;
		}
		//exit from unlimited loop
		// $finish=true;
		//update stok di warehouse_stok
		$get_stok = $db->prepare("SELECT SUM(volume_kartu_akhir) as sisa_stok FROM kartu_stok_ruangan WHERE id_obat=:obat AND id_warehouse=:ware AND in_out='masuk'");
		$get_stok->bindParam(":obat",$id_obat,PDO::PARAM_INT);
		$get_stok->bindParam(":ware",$id_warehouse,PDO::PARAM_INT);
		$get_stok->execute();
		$stok = $get_stok->fetch(PDO::FETCH_ASSOC);
		$up_ware_stok = $db->prepare("UPDATE warehouse_stok SET stok=:stok WHERE id_obat=:obat AND id_warehouse=:ware");
		$up_ware_stok->bindParam(":stok",$stok['sisa_stok'],PDO::PARAM_INT);
		$up_ware_stok->bindParam(":obat",$id_obat,PDO::PARAM_INT);
		$up_ware_stok->bindParam(":ware",$id_warehouse,PDO::PARAM_INT);
		$up_ware_stok->execute();
	}
}else{
	header("location:keluar_edit.php?id=$id_resep&status=4");
}
echo "<script language=\"JavaScript\">window.location = \"keluar_edit.php?id=$id_resep\"</script>";
?>
