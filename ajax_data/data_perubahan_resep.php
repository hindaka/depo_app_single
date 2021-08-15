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
$table = 'perubahan_resep';

// Table's primary key
$primaryKey = 'id_perubahan_resep';

//get parameter
$id_resep = isset($_GET['r']) ? $_GET['r'] : '';
// Array of database columns which should be read and sent back to DataTables.
// The `db` parameter represents the column name in the database, while the `dt`
// parameter represents the DataTables column identifier. In this case simple
// indexes
$columns = array(
  array( 'db' => '`p`.`id_perubahan_resep`', 'dt' => 'id_perubahan_resep', 'field' => 'id_perubahan_resep', 'as' =>'id_perubahan_resep'),
  array( 'db' => '`p`.`tertulis`', 'dt' => 'tertulis', 'field' => 'tertulis', 'as' =>'tertulis'),
  array( 'db' => '`p`.`menjadi`', 'dt' => 'menjadi', 'field' => 'menjadi', 'as' =>'menjadi'),
  array( 'db' => '`peg`.`nama`', 'dt' => 'nama', 'field' => 'nama', 'as' =>'nama')
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
// SELECT * FROM resep r INNER JOIN `warehouse_out` wo ON(r.id_resep=wo.id_resep) INNER JOIN warehouse_stok ws ON(wo.id_warehouse_stok=ws.id_warehouse_stok) INNER JOIN gobat g ON(ws.id_obat=g.id_obat) WHERE r.id_resep='113741'
$joinQuery = "FROM `perubahan_resep` AS `p` INNER JOIN `petugas` AS `pet` ON(`p`.`petugas_approve`=`pet`.`id_petugas`)";
$joinQuery .=" INNER JOIN `pegawai` AS `peg` ON(`pet`.`id_pegawai`=`peg`.`id_pegawai`)";
$extraWhere = " `p`.`id_resep`='".$id_resep."'";
$groupBy = "";
$having = "";

echo json_encode(
	SSP::simple( $_GET, $sql_details, $table, $primaryKey, $columns, $joinQuery, $extraWhere, $groupBy, $having )
);
