<?php
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
$id_parent = isset($_POST['parent']) ? $_POST['parent'] : '';
$tanggal_permintaan = isset($_POST['tanggal_permintaan']) ? $_POST['tanggal_permintaan'] : '';
$warehouse = isset($_POST['warehouse']) ? $_POST['warehouse'] : '';
$id_warehouse = isset($_POST['$id_warehouse']) ? $_POST['id_warehouse'] : '';
$pemesan = isset($_POST['pemesan']) ? $_POST['pemesan'] : '';
$mem_id = $r1['mem_id'];
$volume_input = isset($_POST['volume']) ? $_POST['volume'] : 0;
$obat = isset($_POST['id_obat']) ? $_POST['id_obat'] : '';
$split = explode("|", $obat);
$id_kartu_ruangan = $split[0];
$id_obat = $split[1];
$id_petugas = $r1['mem_id'];
$sisa_keluar = 0;
$tujuan = $warehouse;
$keterangan = "Permintaan dari " . $warehouse . " oleh " . $pemesan;
$volume_in = 0;
$created_at = date('Y-m-d H:i:s');
$i = 0;
$in_out = "booked";
//get list stok sisa
$get_list_stok = $db->query("SELECT k.*,g.nama FROM kartu_stok_ruangan k INNER JOIN gobat g ON(k.id_obat=g.id_obat) WHERE k.id_warehouse='" . $id_depo . "' AND k.id_obat='" . $id_obat . "' AND k.in_out='masuk' AND k.volume_kartu_akhir>0 AND k.id_kartu_ruangan='" . $id_kartu_ruangan . "'");
$kartu = $get_list_stok->fetch(PDO::FETCH_ASSOC);
$id_kartu_gobat = isset($kartu['id_kartu_gobat']) ? $kartu['id_kartu_gobat'] : 0;
$harga_beli = $kartu['harga_beli'];
$volume_kartu = $kartu['volume_kartu_akhir'];
$volume_kartu_akhir= $volume_kartu-$volume_input;
$sumber = $kartu['sumber_dana'];
$jenis = isset($kartu['jenis']) ? $kartu['jenis'] : '';
$merk = isset($kartu['merk']) ? $kartu['merk'] : '';
$pabrikan = isset($kartu['pabrikan']) ? $kartu['pabrikan'] : '';
$volume_akhir = 0;
$volume_out= $volume_input;
//update volume_kartu_akhir berdasarkan data on point
$update_vol = $db->query("UPDATE kartu_stok_ruangan SET volume_kartu_akhir='" . $volume_kartu_akhir . "' WHERE id_kartu_ruangan='" . $id_kartu_ruangan . "'");
//check warehouse_stok
$get_check = $db->query("SELECT COUNT(*) as total FROM warehouse_stok WHERE id_warehouse='" . $id_depo . "' AND id_obat='" . $id_obat . "'");
$check = $get_check->fetch(PDO::FETCH_ASSOC);
if ($check['total'] == 1) {
	//sum
	$get_sum = $db->query("SELECT IFNULL(SUM(volume_kartu_akhir),0) as sisa FROM kartu_stok_ruangan WHERE id_warehouse='" . $id_depo . "' AND id_obat='" . $id_obat . "' AND in_out='masuk' AND volume_kartu_akhir>0");
	$sum = $get_sum->fetch(PDO::FETCH_ASSOC);
	$sisa_stok = $sum['sisa'];
	$stmt = $db->query("UPDATE warehouse_stok SET stok=" . $sisa_stok . " WHERE id_warehouse='" . $id_depo . "' AND id_obat='" . $id_obat . "'");
} else {
	$stmt = $db->prepare("INSERT INTO `warehouse_stok`(`id_warehouse`, `id_obat`, `stok`, `expired`, `no_batch`, `created_at`)VALUES (:id_warehouse,:id_obat,:stok,:expired,:no_batch,:created_at)");
	$stmt->bindParam(":id_warehouse", $id_depo, PDO::PARAM_INT);
	$stmt->bindParam(":id_obat", $id_obat, PDO::PARAM_INT);
	$stmt->bindParam(":stok", $volume_in, PDO::PARAM_INT);
	$stmt->bindParam(":expired", $kartu['expired'], PDO::PARAM_STR);
	$stmt->bindParam(":no_batch", $kartu['no_batch'], PDO::PARAM_STR);
	$stmt->bindParam(":created_at", $created_at, PDO::PARAM_STR);
	$stmt->execute();
}
//insert into kartu_stok_ruangan in_out=keluar
$ins_kartu = $db->prepare("INSERT INTO `kartu_stok_ruangan`(`id_kartu_gobat`,`id_obat`,`id_warehouse`,`sumber_dana`,`jenis`,`merk`,`pabrikan`,`volume_kartu_awal`, `volume_kartu_akhir`, `in_out`, `tujuan`, `volume_in`, `volume_out`, `harga_beli`,`harga_jual`,`id_tuslah`,`ket_tuslah`,`expired`,`no_batch`,`created_at`, `keterangan`,`ref`,`mem_id`) VALUES(:id_kartu_gobat,:id_barang,:id_warehouse,:sumber_dana,:jenis,:merk,:pabrikan,:volume_kartu_awal,:volume_kartu_akhir,:in_out,:tujuan,:volume_in,:volume_out,:harga_beli,:harga_jual,:id_tuslah,:ket_tuslah,:expired,:no_batch,:created_at,:keterangan,:ref,:mem_id)");
$ins_kartu->bindParam(":id_kartu_gobat", $id_kartu_gobat, PDO::PARAM_INT);
$ins_kartu->bindParam(":id_barang", $id_obat, PDO::PARAM_INT);
$ins_kartu->bindParam(":id_warehouse", $id_depo, PDO::PARAM_INT);
$ins_kartu->bindParam(":sumber_dana", $sumber, PDO::PARAM_STR);
$ins_kartu->bindParam(":jenis", $jenis, PDO::PARAM_STR);
$ins_kartu->bindParam(":merk", $merk, PDO::PARAM_STR);
$ins_kartu->bindParam(":pabrikan", $pabrikan, PDO::PARAM_STR);
$ins_kartu->bindParam(":volume_kartu_awal", $volume_akhir, PDO::PARAM_INT);
$ins_kartu->bindParam(":volume_kartu_akhir", $volume_akhir, PDO::PARAM_INT);
$ins_kartu->bindParam(":in_out", $in_out, PDO::PARAM_STR);
$ins_kartu->bindParam(":tujuan", $tujuan, PDO::PARAM_STR);
$ins_kartu->bindParam(":volume_in", $volume_in, PDO::PARAM_INT);
$ins_kartu->bindParam(":volume_out", $volume_out, PDO::PARAM_INT);
$ins_kartu->bindParam(":harga_beli", $harga_beli);
$ins_kartu->bindParam(":harga_jual", $kartu['harga_jual']);
$ins_kartu->bindParam(":id_tuslah", $kartu['id_tuslah'], PDO::PARAM_INT);
$ins_kartu->bindParam(":ket_tuslah", $kartu['ket_tuslah'], PDO::PARAM_INT);
$ins_kartu->bindParam(":expired", $kartu['expired'], PDO::PARAM_STR);
$ins_kartu->bindParam(":no_batch", $kartu['no_batch'], PDO::PARAM_STR);
$ins_kartu->bindParam(":created_at", $created_at, PDO::PARAM_STR);
$ins_kartu->bindParam(":keterangan", $keterangan, PDO::PARAM_STR);
$ins_kartu->bindParam(":ref", $id_kartu_ruangan, PDO::PARAM_INT);
$ins_kartu->bindParam(":mem_id", $id_petugas, PDO::PARAM_INT);
$ins_kartu->execute();
$id_kartu_barang = $db->lastInsertId();
$total_harga = $harga_beli * $volume_out;
//insert
$result2 = $db->prepare("INSERT INTO `barangkeluar_depo_det`(`id_barangkeluar_depo`, `id_kartu`, `id_obat`, `namabarang`,`merk`, `harga`, `volume`, `no_batch`, `expired_date`)
			VALUES (:id_parent,:id_kartu,:id_obat,:namabarang,:merk,:harga,:volume,:no_batch,:expired_date)");
$result2->bindParam(":id_parent", $id_parent, PDO::PARAM_INT);
$result2->bindParam(":id_kartu", $id_kartu_barang, PDO::PARAM_INT);
$result2->bindParam(":id_obat", $id_obat, PDO::PARAM_STR);
$result2->bindParam(":namabarang", $kartu['nama'], PDO::PARAM_STR);
$result2->bindParam(":merk", $merk, PDO::PARAM_STR);
$result2->bindParam(":harga", $harga_beli);
$result2->bindParam(":volume", $volume_out, PDO::PARAM_INT);
$result2->bindParam(":no_batch", $kartu['no_batch'], PDO::PARAM_STR);
$result2->bindParam(":expired_date", $kartu['expired'], PDO::PARAM_STR);
$result2->execute();
header("location: input_permintaan_depo.php?parent=".$id_parent."&status=1");
// check kartu stok
// $get_all_stok = $db->query("SELECT SUM(volume_kartu_akhir) as stok FROM kartu_stok_ruangan WHERE id_warehouse='".$id_depo."' AND id_obat='".$id_obat."' AND in_out='masuk' AND volume_kartu_akhir>0");
// $all_stok = $get_all_stok->fetch(PDO::FETCH_ASSOC);
// echo "stok All : ".$all_stok['stok']."<br>";
// if($all_stok['stok']>=$volume_input){
// 		$finish = false;
// 		$sisa_keluar =0;
// 		$tujuan = $warehouse;
// 		$keterangan = "Permintaan dari ".$warehouse." oleh ".$pemesan;
// 		$volume_in = 0;
// 		$created_at = date('Y-m-d H:i:s');
// 		$i=0;
// 		$in_out="booked";
// 		//get list stok sisa
// 		$get_list_stok = $db->query("SELECT k.*,g.nama FROM kartu_stok_ruangan k INNER JOIN gobat g ON(k.id_obat=g.id_obat) WHERE k.id_warehouse='".$id_depo."' AND k.id_obat='".$id_obat."' AND k.in_out='masuk' AND k.volume_kartu_akhir>0 ORDER BY created_at ASC");
// 		$kartu = $get_list_stok->fetchAll(PDO::FETCH_ASSOC);
// 		while($finish==false){
// 	    //get tuslah yang aktif
// 			$harga_beli = $kartu[$i]['harga_beli'];
// 			$volume_kartu = $kartu[$i]['volume_kartu_akhir'];
// 			$sumber = $kartu[$i]['sumber_dana'];
// 			echo "stok record : ".$volume_kartu."<br>";
// 			echo "Kebutuhan : ".$volume_input."<br>";
// 	    //cek apakah volume mencukupi
// 			if($volume_kartu>=$volume_input){
// 				//stok pengeluaran terpenuhi
// 				$sisa_keluar = 0;
// 				$volume_kartu_akhir = $volume_kartu - $volume_input;
// 				$volume_out = $volume_input;
// 	      echo "cukup";
// 			}else{
// 				$sisa_keluar = $volume_input - $volume_kartu;
// 				$volume_kartu_akhir = 0;
// 				$volume_out = $volume_kartu;
// 	      echo "terpenuhi : ".$volume_kartu.", sisa : ".$sisa_keluar;
// 			}
// 			$volume_akhir=0;
// 			$id_kartu_awal = $kartu[$i]['id_kartu_ruangan'];

// 	    //update volume_kartu_akhir berdasarkan data on point
// 			$update_vol = $db->query("UPDATE kartu_stok_ruangan SET volume_kartu_akhir='".$volume_kartu_akhir."' WHERE id_kartu_ruangan='".$id_kartu_awal."'");
// 			//check warehouse_stok
// 			$get_check = $db->query("SELECT COUNT(*) as total FROM warehouse_stok WHERE id_warehouse='".$id_depo."' AND id_obat='".$id_obat."'");
// 			$check = $get_check->fetch(PDO::FETCH_ASSOC);
// 			if($check['total']==1){
// 				//sum
// 				$get_sum = $db->query("SELECT IFNULL(SUM(volume_kartu_akhir),0) as sisa FROM kartu_stok_ruangan WHERE id_warehouse='".$id_depo."' AND id_obat='".$id_obat."' AND in_out='masuk' AND volume_kartu_akhir>0");
// 				$sum = $get_sum->fetch(PDO::FETCH_ASSOC);
// 				$sisa_stok = $sum['sisa'];
// 				$stmt = $db->query("UPDATE warehouse_stok SET stok=".$sisa_stok." WHERE id_warehouse='".$id_depo."' AND id_obat='".$id_obat."'");
// 			}else{
// 				$stmt = $db->prepare("INSERT INTO `warehouse_stok`(`id_warehouse`, `id_obat`, `stok`, `expired`, `no_batch`, `created_at`)VALUES (:id_warehouse,:id_obat,:stok,:expired,:no_batch,:created_at)");
// 				$stmt->bindParam(":id_warehouse",$id_depo,PDO::PARAM_INT);
// 				$stmt->bindParam(":id_obat",$id_obat,PDO::PARAM_INT);
// 				$stmt->bindParam(":stok",$volume_in,PDO::PARAM_INT);
// 				$stmt->bindParam(":expired",$kartu[$i]['expired'],PDO::PARAM_STR);
// 				$stmt->bindParam(":no_batch",$kartu[$i]['no_batch'],PDO::PARAM_STR);
// 				$stmt->bindParam(":created_at",$created_at,PDO::PARAM_STR);
// 				$stmt->execute();
// 			}
// 			//insert into kartu_stok_ruangan in_out=keluar
// 			$ins_kartu = $db->prepare("INSERT INTO `kartu_stok_ruangan`(`id_kartu_gobat`,`id_obat`,`id_warehouse`,`sumber_dana`,`volume_kartu_awal`, `volume_kartu_akhir`, `in_out`, `tujuan`, `volume_in`, `volume_out`, `harga_beli`,`harga_jual`,`id_tuslah`,`ket_tuslah`,`expired`,`no_batch`,`created_at`, `keterangan`,`ref`,`mem_id`) VALUES(:id_kartu_gobat,:id_barang,:id_warehouse,:sumber_dana,:volume_kartu_awal,:volume_kartu_akhir,:in_out,:tujuan,:volume_in,:volume_out,:harga_beli,:harga_jual,:id_tuslah,:ket_tuslah,:expired,:no_batch,:created_at,:keterangan,:ref,:mem_id)");
// 			$ins_kartu->bindParam(":id_kartu_gobat",$kartu[$i]['id_kartu_gobat'],PDO::PARAM_INT);
// 			$ins_kartu->bindParam(":id_barang",$id_obat,PDO::PARAM_INT);
// 			$ins_kartu->bindParam(":id_warehouse",$id_depo,PDO::PARAM_INT);
// 			$ins_kartu->bindParam(":sumber_dana",$sumber,PDO::PARAM_STR);
// 			$ins_kartu->bindParam(":volume_kartu_awal",$volume_akhir,PDO::PARAM_INT);
// 			$ins_kartu->bindParam(":volume_kartu_akhir",$volume_akhir,PDO::PARAM_INT);
// 			$ins_kartu->bindParam(":in_out",$in_out,PDO::PARAM_STR);
// 			$ins_kartu->bindParam(":tujuan",$tujuan,PDO::PARAM_STR);
// 			$ins_kartu->bindParam(":volume_in",$volume_in,PDO::PARAM_INT);
// 			$ins_kartu->bindParam(":volume_out",$volume_out,PDO::PARAM_INT);
// 			$ins_kartu->bindParam(":harga_beli",$harga_beli,PDO::PARAM_INT);
// 			$ins_kartu->bindParam(":harga_jual",$kartu[$i]['harga_jual'],PDO::PARAM_INT);
// 			$ins_kartu->bindParam(":id_tuslah",$kartu[$i]['id_tuslah'],PDO::PARAM_INT);
// 			$ins_kartu->bindParam(":ket_tuslah",$kartu[$i]['ket_tuslah'],PDO::PARAM_INT);
// 			$ins_kartu->bindParam(":expired",$kartu[$i]['expired'],PDO::PARAM_STR);
// 			$ins_kartu->bindParam(":no_batch",$kartu[$i]['no_batch'],PDO::PARAM_STR);
// 			$ins_kartu->bindParam(":created_at",$created_at,PDO::PARAM_STR);
// 			$ins_kartu->bindParam(":keterangan",$keterangan,PDO::PARAM_STR);
// 			$ins_kartu->bindParam(":ref",$id_kartu_awal,PDO::PARAM_INT);
// 			$ins_kartu->bindParam(":mem_id",$id_petugas,PDO::PARAM_INT);
// 			$ins_kartu->execute();
// 			$id_kartu_barang = $db->lastInsertId();
// 			$total_harga = $harga_beli * $volume_out;
// 			//insert
// 			$result2 = $db->prepare("INSERT INTO `barangkeluar_depo_det`(`id_barangkeluar_depo`, `id_kartu`, `id_obat`, `namabarang`, `harga`, `volume`, `no_batch`, `expired_date`)
// 			VALUES (:id_parent,:id_kartu,:id_obat,:namabarang,:harga,:volume,:no_batch,:expired_date)");
// 			$result2->bindParam(":id_parent",$id_parent,PDO::PARAM_INT);
// 			$result2->bindParam(":id_kartu",$id_kartu_barang,PDO::PARAM_INT);
// 			$result2->bindParam(":id_obat",$id_obat,PDO::PARAM_STR);
// 			$result2->bindParam(":namabarang",$kartu[$i]['nama'],PDO::PARAM_STR);
// 			$result2->bindParam(":harga",$harga_beli,PDO::PARAM_INT);
// 			$result2->bindParam(":volume",$volume_out,PDO::PARAM_INT);
// 			$result2->bindParam(":no_batch",$kartu[$i]['no_batch'],PDO::PARAM_STR);
// 			$result2->bindParam(":expired_date",$kartu[$i]['expired'],PDO::PARAM_STR);
// 			$result2->execute();
// 	    if($sisa_keluar!=0){
// 				//pointer goes to next data
// 				$i++;
// 				$volume_input = $sisa_keluar;
// 			}else{
// 				// exit loop
// 				$finish=true;
// 			}
// 	    // exit from unlimited loop
// 	    // $finish = true;
// 	  }
// 		header("location: input_permintaan_depo.php?parent=".$id_parent."&status=1");
// }else{
// 	//stok tidak mencukupi
// 	header("location: input_permintaan_depo.php?parent=".$id_parent."&status=3");
// }
