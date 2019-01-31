<?php
// echo "<pre>";
// print_r($_GET);exit();
// print_r($_SERVER);
if(isset($_GET['dynamic'])){
	$file = $_SERVER['DOCUMENT_ROOT'].'/recipe-factory/pages/'.$_GET['dynamic'].'.php';
	if(file_exists($file))
		include($file);
	else
		include($_SERVER['DOCUMENT_ROOT'].'/recipe-factory/pages/not_found.php');
	exit();
}
$requestParts = explode('/',$_GET['request']);
$category = $requestParts[0];
$action = $requestParts[1];
$data = '';
if(isset($requestParts[2])) $data = $requestParts[2];
// print_r($requestParts);
// print_r($_GET);
//echo "<br />$action the $category using $data.";
if($category == 'recipe' || $category == 'ingredients' || $category == 'forgot-password'){
	include($_SERVER['DOCUMENT_ROOT'].'/recipe-factory/pages/'.$category.'.php');
} else {
	exit('No page found with requested page.');
}