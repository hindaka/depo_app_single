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
$table = 'rincian_obat_pasien';

// Table's primary key
$primaryKey = 'id_rincian_obat';

//get parameter

// Array of database columns which should be read and sent back to DataTables.
// The `db` parameter represents the column name in the database, while the `dt`
// parameter represents the DataTables column identifier. In this case simple
// indexes

$columns = array(
  array( 'db' => '`ro`.`id_rincian_obat`', 'dt' => 'id_rincian_obat', 'field' => 'id_rincian_obat', 'as' =>'id_rincian_obat'),
  array( 'db' => '`ro`.`created_at`', 'dt' => 'created_at', 'field' => 'created_at', 'as' =>'created_at'),
  array( 'db' => '`rp`.`tanggaldaftar`', 'dt' => 'tanggaldaftar', 'field' => 'tanggaldaftar', 'as' =>'tanggaldaftar'),
  array( 'db' => '`rp`.`nomedrek`', 'dt' => 'nomedrek', 'field' => 'nomedrek', 'as' =>'nomedrek'),
  array( 'db' => '`rp`.`nama`', 'dt' => 'nama', 'field' => 'nama', 'as' =>'nama'),
  array( 'db' => '`ro`.`dpjp`', 'dt' => 'dpjp', 'field' => 'dpjp', 'as' =>'dpjp'),
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
require('ssp.customized.class.php' );

$joinQuery ="FROM `rincian_obat_pasien` AS `ro` INNER JOIN `registerpasien` AS `rp` ON(`ro`.`id_pasien`=`rp`.`id_pasien`)";
$extraWhere = " `ro`.`status`='apotek' AND `ro`.`approval`='n'";
$groupBy = "";
$having = "";

echo json_encode(
	SSP::simple( $_GET, $sql_details, $table, $primaryKey, $columns, $joinQuery, $extraWhere, $groupBy, $having )
);
