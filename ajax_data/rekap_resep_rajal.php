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
$table = 'resep';

// Table's primary key
$primaryKey = 'id_resep';

//get parameter

// Array of database columns which should be read and sent back to DataTables.
// The `db` parameter represents the column name in the database, while the `dt`
// parameter represents the DataTables column identifier. In this case simple
// indexes

$columns = array(
  array( 'db' => '`r`.`id_resep`', 'dt' => 'id_resep', 'field' => 'id_resep', 'as' =>'id_resep'),
  array( 'db' => '`r`.`tgl_resep`', 'dt' => 'tgl_resep', 'field' => 'tgl_resep', 'as' =>'tgl_resep'),
  array( 'db' => '`r`.`dokter`', 'dt' => 'dokter', 'field' => 'dokter', 'as'=>'dokter'),
  array( 'db' => '`r`.`ruang`', 'dt' => 'ruang', 'field' => 'ruang', 'as'=>'ruang'),
  array( 'db' => '`r`.`nomedrek`', 'dt' => 'nomedrek', 'field' => 'nomedrek'),
  array( 'db' => '`r`.`bayar`', 'dt' => 'bayar', 'field' => 'bayar'),
  array( 'db' => '`r`.`nama`', 'dt' => 'nama_pasien', 'field' => 'nama_pasien', 'as'=>'nama_pasien'),
  array( 'db' => '`r`.`statusbayar`', 'dt' => 'statusbayar', 'field' => 'statusbayar', 'as'=>'statusbayar'),
  array( 'db' => '`r`.`jenis_transaksi`', 'dt' => 'jenis_transaksi', 'field' => 'jenis_transaksi', 'as'=>'jenis_transaksi'),
  array( 'db' => '`a`.`nama`', 'dt' => 'nama_petugas', 'field' => 'nama_petugas', 'as' =>'nama_petugas'),
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

$joinQuery = "FROM `resep` AS `r` LEFT JOIN `anggota` AS `a` ON(`r`.`mem_id`=`a`.`mem_id`)";
$extraWhere = " `r`.`statusbayar`<>'Belum dibayar' AND `r`.`ruang` LIKE 'IGD'";
$groupBy = "";
$having = "";

echo json_encode(
	SSP::simple( $_GET, $sql_details, $table, $primaryKey, $columns, $joinQuery, $extraWhere, $groupBy, $having )
);
