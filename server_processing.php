<?php
ini_set('display_errors',1);
//if (isset($_SERVER['HTTP_X_REQUESTED_WITH']) && ( $_SERVER['HTTP_X_REQUESTED_WITH'] == 'XMLHttpRequest' )) {
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
 //$gabung = $_GET['gabung'];
	$bulan=isset($_GET['bulan']) ? $_GET['bulan'] : date('m');
	$tahun = isset($_GET['tahun']) ? $_GET['tahun'] : date('Y');
	$gabung=$tahun."-".$bulan;

// DB table to use
$table = 'rincian_obat_pasien';

// Table's primary key
$primaryKey = 'id_rincian_obat';

// Array of database columns which should be read and sent back to DataTables.
// The `db` parameter represents the column name in the database, while the `dt`
// parameter represents the DataTables column identifier. In this case simple
// indexes
$columns = array(
	array( 'db' => '`io`.`created_at`', 		'dt' => 0, 'field' => 'created_at' ),
	array( 'db' => '`rp`.`nomedrek`',  			'dt' => 1, 'field' => 'nomedrek' ),
	array( 'db' => '`rp`.`nama`',   			'dt' => 2, 'field' => 'nama' ),
	array( 'db' => '`io`.`dpjp`',     			'dt' => 3, 'field' => 'dpjp'),
	array( 'db' => '`io`.`total_biaya_apotek`', 'dt' => 4, 'formatter' => function($d, $row)
				{
					return number_format($row['total_biaya_apotek'],0,'.','.');
				},'field' => 'total_biaya_apotek' ),
	array( 'db' => '`io`.`approval`',     		'dt' => 5, 'field' => 'approval' ),
	array( 'db' => '`io`.`id_rincian_obat`', 	'dt' => 6, 'formatter' => function( $d, $row)
				{
					return "<a type='button' href='rkeluar_ranap_d.php?d=".$row['id_rincian_obat']."' class='btn btn-md btn-primary'>Detail</a>";
				},'field' => 'id_rincian_obat' ),
);

// SQL server connection information
//require('config.php');
//$sql_details = array(
//	'user' => $db_username,
//	'pass' => $db_password,
//	'db'   => $db_name,
//	'host' => $db_host
//);
require_once('../inc/set_env.php');
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

include('ajax_data/ssp.customized.class.php' );
$joinQuery = "FROM `rincian_obat_pasien` AS `io` INNER JOIN `registerpasien` AS `rp` ON(`io`.`id_pasien`=`rp`.`id_pasien`)";
$extraWhere = "`io`.`created_at` LIKE '%".$gabung."%' AND `io`.`status`<>'apotek' AND `io`.`approval`='y'";
$groupBy="";
$having="";
//$joinQuery = "FROM `user` AS `u` JOIN `user_details` AS `ud` ON (`ud`.`user_id` = `u`.`id`)";
//$extraWhere = "`u`.`salary` >= 90000";
//$groupBy = "`u`.`office`";
//$having = "`u`.`salary` >= 140000";

echo json_encode(
	SSP::simple( $_GET, $sql_details, $table, $primaryKey, $columns, $joinQuery, $extraWhere, $groupBy, $having )
	// SSP::simple( $_GET, $sql_details, $table, $primaryKey, $columns, $joinQuery, $extraWhere)
);
//}else{
	//echo '<script>window.location="404.html"</script>';
//}
