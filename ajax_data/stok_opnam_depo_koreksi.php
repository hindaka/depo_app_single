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
$table = 'stok_opname_det';

// Table's primary key
$primaryKey = 'id_det_so';

//get parameter
$so = isset($_GET['so']) ? $_GET['so'] : '';

// Array of database columns which should be read and sent back to DataTables.
// The `db` parameter represents the column name in the database, while the `dt`
// parameter represents the DataTables column identifier. In this case simple
// indexes

// array( 'db' => '`cp`.`id_conf_detail`', 'dt' => 'id_conf_detail', 'field' => 'id_conf_detail', 'as' =>'id_conf_detail'),
$columns = array(
  array( 'db' => '`g`.`no_urut_depo_igd`', 'dt' => 'no_urut_depo_igd', 'field' => 'no_urut_depo_igd', 'as' =>'no_urut_depo_igd'),
  array( 'db' => '`sd`.`id_det_so`', 'dt' => 'id_det_so', 'field' => 'id_det_so', 'as' =>'id_det_so'),
  array( 'db' => '`sd`.`id_parent_so`', 'dt' => 'id_parent_so', 'field' => 'id_parent_so', 'as' =>'id_parent_so'),
  array( 'db' => '`sd`.`id_obat`', 'dt' => 'id_obat', 'field' => 'id_obat', 'as' =>'id_obat'),
  array( 'db' => '`g`.`nama`', 'dt' => 'nama', 'field' => 'nama', 'as' =>'nama'),
  array( 'db' => '`sd`.`no_batch`', 'dt' => 'no_batch', 'field' => 'no_batch', 'as' =>'no_batch'),
  array( 'db' => '`sd`.`expired`', 'dt' => 'expired', 'field' => 'expired', 'as' =>'expired'),
  array( 'db' => '`sd`.`stok_sistem`', 'dt' => 'stok_sistem', 'field' => 'stok_sistem', 'as' =>'stok_sistem'),
  array( 'db' => '`sd`.`mutasi_masuk`', 'dt' => 'mutasi_masuk', 'field' => 'mutasi_masuk', 'as' =>'mutasi_masuk'),
  array( 'db' => '`sd`.`pengurangan`', 'dt' => 'pengurangan', 'field' => 'pengurangan', 'as' =>'pengurangan'),
  array( 'db' => '`sd`.`sisa_real`', 'dt' => 'sisa_real', 'field' => 'sisa_real', 'as' =>'sisa_real'),
  array( 'db' => '`sd`.`fisik`', 'dt' => 'fisik', 'field' => 'fisik', 'as' =>'fisik'),
  array( 'db' => '`sd`.`selisih`', 'dt' => 'selisih', 'field' => 'selisih', 'as' =>'selisih'),
  array( 'db' => '`sd`.`alasan`', 'dt' => 'alasan', 'field' => 'alasan', 'as' =>'alasan'),
  array( 'db' => '`sd`.`koreksi`', 'dt' => 'koreksi', 'field' => 'koreksi', 'as' =>'koreksi'),
  array( 'db' => '`sd`.`harga_beli`', 'dt' => 'harga_beli', 'field' => 'harga_beli', 'as' =>'harga_beli'),
  array( 'db' => '`g`.`sumber`', 'dt' => 'sumber', 'field' => 'sumber', 'as' =>'sumber'),
  array( 'db' => '`sd`.`id_kartu`', 'dt' => 'id_kartu', 'field' => 'id_kartu', 'as' =>'id_kartu'),
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
$joinQuery = "FROM `stok_opname_det` AS `sd` INNER JOIN `gobat` AS `g` ON(`g`.`id_obat`=`sd`.`id_obat`)";
// $joinQuery .=" INNER JOIN `kartu_stok_ruangan` AS `ks` ON(`sd`.`id_kartu`=`ks`.`id_kartu_ruangan`)";
$extraWhere = " `sd`.`id_parent_so`='".$so."'";
$groupBy = "";
$having = "";

echo json_encode(
	SSP::simple( $_GET, $sql_details, $table, $primaryKey, $columns, $joinQuery, $extraWhere, $groupBy, $having )
);
