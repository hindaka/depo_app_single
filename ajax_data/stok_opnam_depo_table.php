<?php
/*
 * DataTables example server-side processing script.
 *
 * Please note that this script is intentionally extremely simply to show how
 * server-side processing can be implemented, and probably shouldn't be used as
 * the basis for a large complex system. It is suitable for simple use cases as
 * for learning.
 *
 * See http://datatables.net/usage/server-side for full details on the server-
 * side processing requirements of DataTables.
 *
 * @license MIT - http://datatables.net/license_mit
 */

/* * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * *
 * Easy set variables
 */

// DB table to use
$table = 'kartu_stok_ruangan';

// Table's primary key
$primaryKey = 'id_kartu_ruangan';

//get parameter
$tipe_depo = "IGD";
$conf = json_decode(file_get_contents("../config/env_depo.json"),true);
$id_depo = $conf[$tipe_depo]["id_depo"];

$tahun = isset($_GET['tahun']) ? $_GET['tahun'] : date('Y');
$id_conf_obat = isset($_GET['c']) ? $_GET['c'] : '20';
// Array of database columns which should be read and sent back to DataTables.
// The `db` parameter represents the column name in the database, while the `dt`
// parameter represents the DataTables column identifier. In this case simple
// indexes

// array( 'db' => '`cp`.`id_conf_detail`', 'dt' => 'id_conf_detail', 'field' => 'id_conf_detail', 'as' =>'id_conf_detail'),
$columns = array(
  array( 'db' => '`g`.`no_urut_depo_igd`', 'dt' => 'no_urut_depo_igd', 'field' => 'no_urut_depo_igd', 'as' =>'no_urut_depo_igd'),
  array( 'db' => '`ks`.`id_kartu_ruangan`', 'dt' => 'id_kartu_ruangan', 'field' => 'id_kartu_ruangan', 'as' =>'id_kartu_ruangan'),
  array( 'db' => '`g`.`id_obat`', 'dt' => 'id_obat', 'field' => 'id_obat', 'as' =>'id_obat'),
  array( 'db' => '`g`.`nama`', 'dt' => 'nama', 'field' => 'nama', 'as' =>'nama'),
  array( 'db' => '`ks`.`sumber_dana`', 'dt' => 'sumber_dana', 'field' => 'sumber_dana', 'as' =>'sumber_dana'),
  array( 'db' => 'SUM(`ks`.`volume_kartu_akhir`)', 'dt' => 'sisa_stok', 'field' => 'sisa_stok', 'as' =>'sisa_stok'),
  array( 'db' => '`ks`.`no_batch`', 'dt' => 'no_batch', 'field' => 'no_batch', 'as' =>'no_batch'),
  array( 'db' => '`ks`.`expired`', 'dt' => 'expired', 'field' => 'expired', 'as' =>'expired'),
  array( 'db' => '`ks`.`harga_beli`', 'dt' => 'harga_beli', 'field' => 'harga_beli', 'as' =>'harga_beli'),
);

// SQL server connection information
require_once('../../inc/set_env.php');
$sql_details = array(
    'user' => $userPdo,
    'pass' => $passPdo,
    'db'   => $dbPdo,
    'host' => $hostPdo
);

/* * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * *
 * If you just want to use the basic configuration for DataTables with PHP
 * server-side, there is no need to edit below this line.
 */

// require( 'ssp.class.php' );
require('ssp.customized.class.php' );
// SELECT ks.id_kartu_ruangan,g.nama,g.jenis,ks.no_batch,ks.expired,ks.volume_kartu_akhir,ks.harga_beli,ks.harga_jual,ks.created_at FROM kartu_stok_ruangan ks INNER JOIN gobat g ON(ks.id_obat=g.id_obat) INNER JOIN conf_detail_penyimpanan cd ON(g.id_obat=cd.id_obat) WHERE ks.id_warehouse='57' AND ks.in_out='masuk' AND ks.volume_kartu_akhir>0 AND YEAR(ks.created_at)='2020' AND cd.id_conf_obat='13'
$joinQuery = "FROM `kartu_stok_ruangan` AS `ks` INNER JOIN `gobat` AS `g` ON(`ks`.`id_obat`=`g`.`id_obat`)";
$extraWhere = " `ks`.`id_warehouse`='".$id_depo."' AND `ks`.`in_out`='masuk' AND `ks`.`volume_kartu_akhir`>0 AND YEAR(`ks`.`created_at`)='".$tahun."' AND `g`.`lemari_depo_igd`='".$id_conf_obat."'";
$groupBy = " `ks`.`id_obat`,`ks`.`no_batch`,`ks`.harga_beli";
$having = "";

echo json_encode(
	SSP::simple( $_GET, $sql_details, $table, $primaryKey, $columns, $joinQuery, $extraWhere, $groupBy, $having )
);
