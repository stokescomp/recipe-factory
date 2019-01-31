<?php
echo "got to api.php";
//echo "<pre>";
print_r($_GET);
// print_r($_SERVER);
$requestParts = explode('/',$_GET['request']);
$category = $requestParts[0];
$action = $requestParts[1];
$data = $requestParts[2];
//print_r($requestParts);
//echo "<br />$action the $category using $data.";

//if($category == 'recipe'){
	//print_r($_SERVER);
//	include($_SERVER['DOCUMENT_ROOT'].'/recipe-factory/pages/add_recipe.php');
	// include($_SERVER['DOCUMENT_ROOT'].'/recipe-factory/index.php');
//}