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
$table = 'stok_opname';

// Table's primary key
$primaryKey = 'id_so';

//get parameter
$tipe_depo = "IGD";
$conf = json_decode(file_get_contents("../config/env_depo.json"),true);
$id_depo = $conf[$tipe_depo]["id_depo"];
$bulan = isset($_GET['bulan']) ? $_GET['bulan'] : date('m');
$tahun = isset($_GET['tahun']) ? $_GET['tahun'] : date('Y');
$sync = "n";
$koreksi = "y";
// Array of database columns which should be read and sent back to DataTables.
// The `db` parameter represents the column name in the database, while the `dt`
// parameter represents the DataTables column identifier. In this case simple
// indexes

// array( 'db' => '`cp`.`id_conf_detail`', 'dt' => 'id_conf_detail', 'field' => 'id_conf_detail', 'as' =>'id_conf_detail'),
$columns = array(
  array( 'db' => '`so`.`id_so`', 'dt' => 'id_so', 'field' => 'id_so', 'as' =>'id_so'),
  array( 'db' => '`so`.`tanggal_so`', 'dt' => 'tanggal_so', 'field' => 'tanggal_so', 'as' =>'tanggal_so'),
  array( 'db' => '`so`.`penyimpanan`', 'dt' => 'penyimpanan', 'field' => 'penyimpanan', 'as' =>'penyimpanan'),
  array( 'db' => '`peg`.`nama`', 'dt' => 'nama', 'field' => 'nama', 'as' =>'nama'),
  array( 'db' => '`so`.`koreksi_status`', 'dt' => 'koreksi_status', 'field' => 'koreksi_status', 'as' =>'koreksi_status'),
  array( 'db' => '`so`.`sync`', 'dt' => 'sync', 'field' => 'sync', 'as' =>'sync'),
  array( 'db' => '`so`.`created_at`', 'dt' => 'created_at', 'field' => 'created_at', 'as' =>'created_at'),
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
$joinQuery = "FROM `stok_opname` AS `so` INNER JOIN `pegawai` AS `peg` ON(`so`.`petugas_so`=`peg`.`id_pegawai`)";
$extraWhere = " `so`.`id_warehouse`='".$id_depo."' AND YEAR(`so`.`tanggal_so`)='".$tahun."' AND MONTH(`so`.`tanggal_so`)='".$bulan."' AND `so`.`koreksi_status`='".$koreksi."'";
$groupBy = "";
$having = "";

echo json_encode(
	SSP::simple( $_GET, $sql_details, $table, $primaryKey, $columns, $joinQuery, $extraWhere, $groupBy, $having )
);
