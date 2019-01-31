<?php
if(!isset($_SERVER['DOCUMENT_ROOT_COPY']))
	$_SERVER['DOCUMENT_ROOT_COPY'] = $_SERVER['DOCUMENT_ROOT'];
$_SERVER['DOCUMENT_ROOT'] = $_SERVER['DOCUMENT_ROOT_COPY'] . '/recipe-factory';
if(isset($_SERVER['HTTP_HOST']) && $_SERVER['HTTP_HOST'] == 'localhost'){$localdev = 1;} else {$localdev = 0;}
if($localdev){
	$prepend_table = '';
	$databaseHost = 'localhost';
	$databaseName = 'recipe_factory';
	$databaseUser = 'rf_web_user';
	$databasePass = '$h^kK3@jDo)od@A*#';
}
else {
	$prepend_table = 'recipe_factory_';
	$databaseHost = $_SERVER["DATABASE1_HOST"];
	$databaseName = $_SERVER["DATABASE1_NAME"];
	$databaseUser = $_SERVER["DATABASE1_USER"];
	$databasePass = $_SERVER["DATABASE1_PASS"];
}
require_once $_SERVER['DOCUMENT_ROOT'].'/includes/easypdo.mysql.php';
$db = EasyPDO_MySQL::Instance($databaseHost, $databaseName, $databaseUser, $databasePass);
if(!$db) die('no connection possible');