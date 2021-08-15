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
$table = 'conf_detail_penyimpanan';

// Table's primary key
$primaryKey = 'id_conf_detail';

//get parameter

// Array of database columns which should be read and sent back to DataTables.
// The `db` parameter represents the column name in the database, while the `dt`
// parameter represents the DataTables column identifier. In this case simple
// indexes

$columns = array(
  array( 'db' => '`ks`.`id_kartu_ruangan`', 'dt' => 'id_kartu_ruangan', 'field' => 'id_kartu_ruangan', 'as' =>'id_kartu_ruangan'),
  array( 'db' => '`g`.`nama`', 'dt' => 'nama', 'field' => 'nama', 'as' =>'nama'),
  array( 'db' => '`ks`.`volume_kartu_akhir`', 'dt' => 'volume_kartu_akhir', 'field' => 'volume_kartu_akhir', 'as' =>'volume_kartu_akhir'),
  array( 'db' => '`ks`.`no_batch`', 'dt' => 'no_batch', 'field' => 'no_batch', 'as' =>'no_batch'),
  array( 'db' => '`ks`.`expired`', 'dt' => 'expired', 'field' => 'expired', 'as' =>'expired'),

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
$joinQuery = " FROM `conf_detail_penyimpanan` AS `cd` LEFT JOIN `kartu_stok_ruangan` AS `ks` ON(`cd`.`id_obat`=`ks`.`id_obat`) ";
$joinQuery .= " INNER JOIN `gobat` AS `g` ON(`ks`.`id_obat`=`g`.`id_obat`)";
$extraWhere = " `ks`.`id_warehouse`='57' AND `ks`.`in_out`='masuk' AND `ks`.`volume_kartu_akhir`>0 AND YEAR(`ks`.`created_at`)='2020'";
$extraWhere .= " AND `cd`.`id_conf_obat`='19'";
$groupBy = "";
$having = "";

echo json_encode(
	SSP::simple( $_GET, $sql_details, $table, $primaryKey, $columns, $joinQuery, $extraWhere, $groupBy, $having )
);
