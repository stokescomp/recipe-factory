<?php
if(!isset($_SESSION)) session_start();
require(dirname(__FILE__).'/../includes/db.php');
require($_SERVER['DOCUMENT_ROOT'].'/includes/functions.php');
$root = '/recipe-factory/';
$error = array();
$token_life_minutes = 30;
//error_reporting('E_ALL');