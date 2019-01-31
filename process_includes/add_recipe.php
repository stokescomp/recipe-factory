<?php
if(isset($_POST['title'])){
	//echo "<pre>".print_r($_POST,1).print_r($_FILES,1)."</pre>";
	//$filepath = $_FILES['image']['tmp_name'];
	//$image_info = getimagesize($filepath);
	//print_r($image_info);
	//header('Content-type:'.$image_info['mime']);
	//echo file_get_contents($_FILES['image']['tmp_name']);
	$error = checkCSRFToken($_POST['csrf_token'], $error);
	//print_r($error);
	if(!$error){
		if(empty($_POST['title'])) $error[] = 'Enter the Title';
		if(empty($_POST['description'])) $error[] = 'Enter the Description';
		if(empty($_POST['meal'])) $error[] = 'Enter the Meal';
	}
	if(!$error){
		if(isset($_POST['is_recipe_private']))
			$is_recipe_private = 'Y';
		else
			$is_recipe_private = 'N';
		if(isset($_POST['is_recipe_draft']))
			$is_recipe_draft = 'Y';
		else
			$is_recipe_draft = 'N';

		//make these appear empty in the webpage instead of being changed to a 0.
		if($_POST['prep_time_minutes'] == '') $_POST['prep_time_minutes'] = null;
		if($_POST['preheat_temp'] == '') $_POST['preheat_temp'] = null;
		if($_POST['makes_amount'] == '') $_POST['makes_amount'] = null;
		if($_POST['overridden_serving_amount'] == '') $_POST['overridden_serving_amount'] = null;
		if($_POST['overridden_calories'] == '') $_POST['overridden_calories'] = null;

		$recipe_id = $db->ExecuteSQL("INSERT INTO {$prepend_table}recipe (title, subtitle, description, 
			prep_time_minutes, preheat_temp, overridden_serving_amount, is_recipe_private, 
			is_recipe_draft, citation_url, citation_person, holiday_id, makes_amount, serving_type_id, overridden_calories, created_by) 
			VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)"
			, 'sssiiiiissiiiii'
			, $_POST['title'], $_POST['subtitle'], $_POST['description'], $_POST['prep_time_minutes'], 
			$_POST['preheat_temp'], $_POST['overridden_serving_amount'], $is_recipe_private, $is_recipe_draft, $_POST['citation_url'], $_POST['citation_person'], 
			$_POST['holiday'], $_POST['makes_amount'], $_POST['serving_type'], $_POST['overridden_calories'], $_SESSION['user_id']);

		if($recipe_id == ''){
			$error[] = "{$_SESSION['name']}, There was an error adding the recipe";
		} else {
			//join the two groups of categories
			if(!isset($_POST['general_category']))
				$_POST['general_category'] = array();
			if(!isset($_POST['specific_category']))
				$_POST['specific_category'] = array();
			$category = array_merge($_POST['general_category'], $_POST['specific_category']);
			
			//insert the select options.
			if(isset($_POST['user_group']))
				foreach ($_POST['user_group'] as $key => $each_user_group) {
					$db->ExecuteSQL("INSERT INTO {$prepend_table}recipe_to_user_group (recipe_id, user_group_id) VALUES (?, ?)"
									, 'ii'
									, $recipe_id, $each_user_group);
				}
			foreach ($category as $key => $each_category) {
				$db->ExecuteSQL("INSERT INTO {$prepend_table}recipe_to_category (recipe_id, category_id) VALUES (?, ?)"
								, 'ii'
								, $recipe_id, $each_category);
			}
			if(isset($_POST['meal']))
				foreach ($_POST['meal'] as $key => $each_meal) {
					$db->ExecuteSQL("INSERT INTO {$prepend_table}recipe_to_meal (recipe_id, meal_id) VALUES (?, ?)"
									, 'ii'
									, $recipe_id, $each_meal);
				}
			
			$small_image_path = $recipe_id."-small.jpg";
			$medium_image_path = $recipe_id."-medium.jpg";
			$large_image_path = $recipe_id."-large.jpg";
			//resize the images
			//save the images
			//update the recipe table with the image paths

			//add the ingredients
			$ingredient_count = count($_POST['ingredient_name']);
			
			//if there are no ingredients we won't insert anything
		    if($ingredient_count > 0){
				//insert the ingredients
			    $question_mark_list = str_repeat('(?,?,?,?,?,?, CURRENT_TIMESTAMP, ?),', $ingredient_count - 1);
			    $type_list = str_repeat('iiidsii', $ingredient_count);
			    $value_list = [];
			    foreach ($_POST['ingredient_name'] as $key => $eachIngredient) {
			        $value_list[] = $recipe_id;
			        $value_list[] = $eachIngredient;
			        $value_list[] = $_POST['ingredient_measurement'][$key];
			        $value_list[] = $_POST['ingredient_amount'][$key];
			        $value_list[] = $_POST['ingredient_note'][$key];
			        $value_list[] = $_POST['ingredient_order'][$key];
			        $value_list[] = $_SESSION['user_id'];
			    }
			    //IGNORE keyword stops errors from happening, when a row in the database already exists.
			    $db->ExecuteSQL("INSERT IGNORE INTO recipe_ingredient
			            (recipe_id, ingredient_id, measurement_id, amount, note, `order`, update_date, updated_by) VALUES
			            $question_mark_list (?,?,?,?,?,?, CURRENT_TIMESTAMP, ?)"
			        , $type_list
			        , $value_list);
			}

			//add instructions
			$instruction_count = count($_POST['step_description']);

			if($instruction_count > 0){
				//insert the instructions
			    $question_mark_list = str_repeat('(?,?,?),', $instruction_count - 1);
			    $type_list = str_repeat('isi', $instruction_count);
			    $value_list = [];
			    foreach ($_POST['step_description'] as $key => $eachInstruction) {
			        $value_list[] = $recipe_id;
			        $value_list[] = $eachInstruction;
			        $value_list[] = $_POST['step_order'][$key];
			    }
			    $insert_result = $db->ExecuteSQL("INSERT IGNORE INTO recipe_instruction
				            (recipe_id, description, `order`) VALUES
				            $question_mark_list (?,?,?)"
				        , $type_list
				        , $value_list);
			}

			deleteToken($_POST['csrf_token']);
			$_SESSION['message'][] = "{$_SESSION['name']}, your recipe was created successfully!";
			header("Location: {$root}recipe/edit/$recipe_id");
			exit();
		}
	}
}