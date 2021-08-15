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
$table = 'tpn_apotek';

// Table's primary key
$primaryKey = 'id_tpn_apotek';


// Array of database columns which should be read and sent back to DataTables.
// The `db` parameter represents the column name in the database, while the `dt`
// parameter represents the DataTables column identifier. In this case simple
// indexes
$columns = array(
  array( 'db' => '`t`.`id_tpn_apotek`', 'dt' => 'id_tpn_apotek', 'field' => 'id_tpn_apotek', 'as' =>'id_tpn_apotek'),
  array( 'db' => '`t`.`tipe_tpn`', 'dt' => 'tipe_tpn', 'field' => 'tipe_tpn', 'as' =>'tipe_tpn'),
  array( 'db' => '`t`.`konsentrasi`', 'dt' => 'konsentrasi', 'field' => 'konsentrasi', 'as' =>'konsentrasi'),
  array( 'db' => '`t`.`gr`', 'dt' => 'gr', 'field' => 'gr', 'as' =>'gr'),
  array( 'db' => '`t`.`amino_acid`', 'dt' => 'amino_acid', 'field' => 'amino_acid', 'as' =>'amino_acid'),
  array( 'db' => '`t`.`dex40`', 'dt' => 'dex40', 'field' => 'dex40', 'as' =>'dex40'),
  array( 'db' => '`t`.`dex10`', 'dt' => 'dex10', 'field' => 'dex10', 'as' =>'dex10'),
  array( 'db' => '`t`.`kcl`', 'dt' => 'kcl', 'field' => 'kcl', 'as' =>'kcl'),
  array( 'db' => '`t`.`ca_glu_10`', 'dt' => 'ca_glu_10', 'field' => 'ca_glu_10', 'as' =>'ca_glu_10'),
  array( 'db' => '`t`.`mgso4_40`', 'dt' => 'mgso4_40', 'field' => 'mgso4_40', 'as' =>'mgso4_40'),
  array( 'db' => '`t`.`ns_3`', 'dt' => 'ns_3', 'field' => 'ns_3', 'as' =>'ns_3'),
  array( 'db' => '`t`.`created_at`', 'dt' => 'created_at', 'field' => 'created_at', 'as' =>'created_at')
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

$joinQuery = "FROM `tpn_apotek` AS `t`";
$extraWhere = "";
$groupBy = "";
$having = "";

echo json_encode(
	SSP::simple( $_GET, $sql_details, $table, $primaryKey, $columns, $joinQuery, $extraWhere, $groupBy, $having )
);
