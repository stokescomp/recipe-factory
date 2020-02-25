<?php
if(!isset($_SESSION)) session_start();
require_once '../includes/init.php';
require_once '../includes/db.php';
deleteExpiredTokens();
if(!isset($_POST['csrf_token'])) $_POST['csrf_token'] = '';
$error = checkCSRFToken($_POST['csrf_token'], $error);
if($error){
	exit('_CSRF_ERROR_'.$error[0]);
}
if(isset($_POST['view_ingredient'])){
	$ingredient_id = $_POST['ingredient_id'];
	$sql = "SELECT i.name as ingredient_name, calories_per_ounce, special_qualities, im.food_group_id, fg.name as food_group_name 
		FROM {$prepend_table}ingredient i 
		INNER JOIN {$prepend_table}ingredient_map im USING(ingredient_id) 
		INNER JOIN {$prepend_table}food_group fg ON fg.food_group_id = im.food_group_id 
		WHERE i.ingredient_id = ?";
	$data = $db->FetchArray($sql, 'i', $ingredient_id);
	$data['ingredient_name'] = htmlspecialchars($data['ingredient_name'], ENT_QUOTES);
	$data['special_qualities'] = htmlspecialchars($data['special_qualities'], ENT_QUOTES);
	$data['food_group_name'] = htmlspecialchars($data['food_group_name'], ENT_QUOTES);
	echo json_encode($data);
}

if(isset($_POST['view_food_group'])){
	$food_group_id = $_POST['food_group_id'];
	$sql = "SELECT fg.food_group_id, fg.name as food_group_name, fg.parent_food_group_id, (SELECT name FROM {$prepend_table}food_group WHERE food_group_id = fg.parent_food_group_id) as parent_food_group_name 
		FROM {$prepend_table}food_group fg
		WHERE fg.food_group_id = ?";
	$data = $db->FetchArray($sql, 'i', $food_group_id);
	$data['food_group_name'] = htmlspecialchars($data['food_group_name'], ENT_QUOTES);
	$data['parent_food_group_name'] = htmlspecialchars($data['parent_food_group_name'], ENT_QUOTES);
	echo json_encode($data);
}

if(isset($_POST['edit_ingredient'])){
	$add = false;
	$error = false;
	$type = htmlspecialchars($_POST['type'], ENT_QUOTES);
	$name = $_POST['name'];
	//can be parent or just food_group_id depending on if we are editing a food group or ingredient
	if(isset($_POST['food_group_id']))
		$food_group_id = $_POST['food_group_id'];
	else
		$food_group_id = '';
	$new_food_group_id = $_POST['new_food_group_id'];
	
	if($new_food_group_id == 0) $new_food_group_id = null;
	if(checkLoggedIn() == false) exit();
	if(isset($_SESSION['user_id']))$user_id = $_SESSION['user_id'];
	//can be edit or add
	$action = $_POST['action'];
	//action can be add or edit
	if($action == 'add'){
		$add = true;
	}
	if($name == ''){
		$error = true;
		$message = 'You need to enter your name';
	}
	// Foodgroup is empty when adding
	if(!$add && $new_food_group_id === $food_group_id){
		$error = true;
		$message = "You can't put the food group inside it self.";
	}
	if($type == 'ingredient'){
		$ingredient_id = $_POST['ingredient_id'];
		$calories_per_ounce = $_POST['calories_per_ounce'];
		if(empty($calories_per_ounce)) $calories_per_ounce = NULL;
		$special_qualities = $_POST['special_qualities'];
		if(@0+$new_food_group_id == 0) $new_food_group_id = '';
		if(!$error && $new_food_group_id == ''){
			$error = true;
			$message = 'You need to enter a food group. Pick a number from an existing food group';
		}
		//see if the food group exists.
		$sql = "SELECT food_group_id FROM {$prepend_table}food_group WHERE food_group_id = ?";
		$data = $db->FetchArray($sql, 'i', $new_food_group_id);
		if(empty($data['food_group_id'] )){
			$error = true;
			$message = 'You need to enter an existing food group. Pick a number next to a food group. Later we will make this user friendly.';
		}
		if($error)
			echo '{"success":false,"message":"'.$message.'"}';
		else {
			//add or edit the ingredient
			if($add){
				$sql = "INSERT INTO {$prepend_table}ingredient (name, calories_per_ounce, special_qualities, creation_date, created_by) 
					VALUES(?, ?, ?, CURRENT_TIMESTAMP(), ?)";
				$ingredient_id = $db->ExecuteSQL($sql, 'sisi', $name, $calories_per_ounce, $special_qualities, $user_id);
				//add ingredient map
				$sql = "INSERT INTO {$prepend_table}ingredient_map (ingredient_id, food_group_id) VALUES(?, ?)";
				$db->ExecuteSQL($sql, 'ii', $ingredient_id, $new_food_group_id);
			} else {
				$sql = "UPDATE {$prepend_table}ingredient SET name = ?, calories_per_ounce = ?, special_qualities = ?, updated_by = ?
						WHERE ingredient_id = ?";
						// echo $sql."$name, $calories_per_ounce, $special_qualities, $user_id, $ingredient_id;";
				$db->ExecuteSQL($sql, 'sisii', $name, $calories_per_ounce, $special_qualities, $user_id, $ingredient_id);
				//update old food_group_id and ingredient_id
				$sql = "UPDATE {$prepend_table}ingredient_map SET ingredient_id = ?, food_group_id = ? WHERE ingredient_id = ?";
				// echo $sql."$ingredient_id, $new_food_group_id, $ingredient_id";
				$db->ExecuteSQL($sql, 'iii', $ingredient_id, $new_food_group_id, $ingredient_id);
			}
			echo '{"success":true,"type":"'.$type.'"}';
		}
	} else if($type == 'food_group'){
		if($new_food_group_id != 0){
			//see if the food group exists.
			$sql = "SELECT food_group_id FROM {$prepend_table}food_group WHERE food_group_id = ?";
			$data = $db->FetchArray($sql, 'i', $new_food_group_id);
			if(empty($data['food_group_id'])){
				$error = true;
				$message = 'You need to enter an existing food group. Pick a number next to a food group. Later we will make this user friendly.';
			}
		}
		if($error)
			echo '{"success":false,"message":"'.$message.'"}';
		else {
			//add or edit the food group	
			if($add){
				$sql = "INSERT INTO {$prepend_table}food_group (parent_food_group_id, name, creation_date, created_by) 
					VALUES(?, ?, CURRENT_TIMESTAMP(), ?)";
				$db->ExecuteSQL($sql, 'isi', $new_food_group_id, $name, $user_id);
			} else {
				if($new_food_group_id !== null && isFoodGroupAncestorOfFoodGroup($new_food_group_id, $food_group_id)){
					$error = true;
					$message = 'You can not use a food group that is a child of the food group you are editing.';
				}
				if($error){
					echo '{"success":false,"message":"'.$message.'"}';
					exit();
				} else {
					$sql = "UPDATE {$prepend_table}food_group SET parent_food_group_id = ?, name = ?, updated_by = ?
							WHERE food_group_id = ?";
					$db->ExecuteSQL($sql, 'isii', $new_food_group_id, $name, $user_id, $food_group_id);
					//echo $sql.$new_food_group_id. $name. $user_id.' :'. $food_group_id;
				}
			}

			echo '{"success":true}';
		}
	}
}

if(isset($_POST['delete_ingredient'])){
	//print_r($_POST);
	$count = 0;
	$message = '';
	$id = $_POST['id'];
	if($_POST['type'] == 'ingredient'){
		$message = ' if the ingredient is being used in a recipe.';
		$sql = "SELECT count(*) as count FROM recipe_ingredient WHERE ingredient_id = ?";
		$data = $db->FetchArray($sql, 'i', $id);

		$count = $data['count'];
		if($count == 0){
			//delete ingredient
			$sql = "DELETE FROM ingredient WHERE ingredient_id = ?";
			$db->ExecuteSQL($sql, 'i', $id);
			$sql = "DELETE FROM ingredient_map WHERE ingredient_id = ?";
			$db->ExecuteSQL($sql, 'i', $id);
		}
	} else if($_POST['type'] == 'food_group'){
		$message = ' if there are ingredients in it. Remove all ingredients that are inside before deleting.';
		//if there are any ingredients in the food group or in one of its children then don't delete anything
		$count = canDeleteFoodGroup($id);
		if($count == 0){
			deleteFoodGroup($id);
		}
	} else {
		echo '{"success":false,"message":"The type is not one that is known. Use ingredient or food_group."}';
	}
	if($count > 0){
		echo '{"success":false,"message":"You can not delete the ' . str_replace("_"," ",$_POST['type']) . $message . '"}';
	} else {
		echo '{"success":true,"message":"The ' . str_replace("_"," ",$_POST['type']) . ' will be deleted."}';
	}
}

if(isset($_POST['get_ingredient_tree'])){
	//get the ingredients and food groups
	$sql = "SELECT CONCAT(11,im.food_group_id,im.ingredient_id) AS id
			, im.ingredient_id AS orig_id
			, IFNULL(CONCAT(99,im.food_group_id),null) AS parentId
			, i.name, 'ingredient' AS type 
			FROM {$prepend_table}ingredient_map im 
			INNER JOIN {$prepend_table}ingredient i ON im.ingredient_id = i.ingredient_id
			UNION ALL
			SELECT CONCAT(99,food_group_id)
			, food_group_id
			, ifnull(CONCAT(99,parent_food_group_id),null)
			, name, 'food_group' 
			FROM {$prepend_table}food_group";
	$tree = array();
	$all_ingredients = array();
	foreach($db->FetchAll($sql) as $each){
		$all_ingredients[$each['id']] = $each;
	}
	//map the tree of ingredients and food groups
	$tree = mapTree($all_ingredients);
	echo display_ingredient_tree($tree);
}

if(isset($_POST['get_food_group_tree'])){
	//get the food groups
	$sql = "SELECT CONCAT(99,food_group_id) AS id
			, food_group_id AS orig_id
			, IFNULL(CONCAT(99,parent_food_group_id),null) AS parentId
			, name
			, 'food_group' AS type 
			FROM food_group";
	$tree = array();
	$all_food_groups = array();
	foreach($db->FetchAll($sql) as $each){
		$all_food_groups[$each['id']] = $each;
	}
	//map the tree food groups
	$tree = mapTree($all_food_groups);
	echo display_ingredient_tree($tree);
}


if(isset($_POST['delete_recipe'])){
	$id = $_POST['recipe_id'];
	$db->StartTransaction();
	$sql = "DELETE FROM recipe_to_category WHERE recipe_id = ?";
	$result = $db->ExecuteSQL($sql, 'i', $id);
	$sql = "DELETE FROM recipe_to_meal WHERE recipe_id = ?";
	$db->ExecuteSQL($sql, 'i', $id);
	$sql = "DELETE FROM recipe_to_user_group WHERE recipe_id = ?";
	$db->ExecuteSQL($sql, 'i', $id);
	$sql = "DELETE FROM recipe_ingredient WHERE recipe_id = ?";
	$db->ExecuteSQL($sql, 'i', $id);
	$sql = "DELETE FROM recipe_instruction WHERE recipe_id = ?";
	$db->ExecuteSQL($sql, 'i', $id);
	$sql = "DELETE FROM recipe WHERE recipe_id = ?";
	$db->ExecuteSQL($sql, 'i', $id);
	$db->CommitTransaction();
	echo '{"success":true}';
}