<?php
if(isset($_POST['title'])){
	// echo "<pre>".print_r($_POST,1);
	
	// exit();
	$error = checkCSRFToken($_POST['csrf_token'], $error);
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

		$update_result = $db->ExecuteSQL("UPDATE {$prepend_table}recipe SET title = ?, subtitle = ?, description = ?, 
			prep_time_minutes = ?, preheat_temp = ?, overridden_serving_amount = ?, 
			is_recipe_private = ?, is_recipe_draft = ?, citation_url = ?, citation_person = ?,
			holiday_id = ?, makes_amount = ?, serving_type_id = ?, overridden_calories = ?,
			update_date = CURRENT_TIMESTAMP, updated_by = ? WHERE recipe_id = ?", 
			'sssiiiiissiiiiii', 
			$_POST['title'], $_POST['subtitle'], $_POST['description'], 
			$_POST['prep_time_minutes'], $_POST['preheat_temp'], $_POST['overridden_serving_amount'], 
			$is_recipe_private, $is_recipe_draft, $_POST['citation_url'], $_POST['citation_person'], 
			$_POST['holiday'], $_POST['makes_amount'], $_POST['serving_type'], $_POST['overridden_calories'], 
			$_SESSION['user_id'], $_POST['recipe_id']);

		//use the sql to remove any rows that are no longer selected:
		// DELETE FROM recipe_to_user_group  WHERE recipe_id = 9 AND user_group_id NOT IN(1,2,5);
		// add the ones that are in these usergroups
		//ignore keyword will make no error when there is a recipe in the database with the 
		//selected user_group already selected.
		//INSERT IGNORE INTO recipe_to_user_group (recipe_id, user_group_id) VALUES(9,1),(9,2),(9,5)

		if(!$update_result){
			$error[] = "{$_SESSION['name']}, There was an error editing the recipe";
			$error = true;
		}
	}

	if(!$error){
		//join the two groups of categories
		if(!isset($_POST['general_category']))
			$_POST['general_category'] = array();
		if(!isset($_POST['specific_category']))
			$_POST['specific_category'] = array();
		$category = array_merge($_POST['general_category'], $_POST['specific_category']);

		//make the following a function and pass the recipe_id, user_group as a parameter
		if(isset($_POST['user_group']))
			updateMulipleOptionList($_POST['recipe_id'],$_POST['user_group'],'user_group');
		updateMulipleOptionList($_POST['recipe_id'],$category,'category');
		if(isset($_POST['meal']))
			updateMulipleOptionList($_POST['recipe_id'],$_POST['meal'],'meal');

		//update image
		
		//update ingredients
		$ingredient_count = count($_POST['ingredient_name']);

		//the first integer in the type list will represent the recipe_id
		$type_list = 'i';
		$value_list = array($_POST['recipe_id']);
		if($ingredient_count > 0){
			$question_mark_list = str_repeat('?,', $ingredient_count - 1);
			$type_list .= str_repeat('i', $ingredient_count);
	        $value_list = array_merge($value_list, $_POST['ingredient_name']);
    	}
		//delete any ingredients no longer in the recipe.
		$sql = "DELETE FROM recipe_ingredient WHERE recipe_id = ?";
		if($ingredient_count > 0)
        	$sql .= " AND ingredient_id NOT IN({$question_mark_list}?)";

		$db->ExecuteSQL($sql
	        , $type_list
	        , $value_list);
		
		//if there are no ingredients we won't insert anything
	    if($ingredient_count > 0){
	    	//update anything that already has an ingredient
	    	foreach ($_POST['ingredient_name'] as $key => $eachIngredient) {
	    		$amount = $_POST['ingredient_amount'][$key] + $_POST['ingredient_fraction_amount'][$key];
	    		$_POST['ingredient_amount'][$key] = $amount;
			    $db->ExecuteSQL("UPDATE recipe_ingredient
			            SET ingredient_id = ?, measurement_id = ?, amount = ?, note = ?, `order` = ?, update_date = CURRENT_TIMESTAMP, updated_by = ?
			            WHERE recipe_id = ? AND ingredient_id = ?"
			        , 'iidsiiii'
			        , $eachIngredient, $_POST['ingredient_measurement'][$key], $amount, 
			        	$_POST['ingredient_note'][$key], $_POST['ingredient_order'][$key], $_SESSION['user_id'], $_POST['recipe_id'], $eachIngredient);
		    }

			//insert the ingredients
		    $question_mark_list = str_repeat('(?,?,?,?,?,?, CURRENT_TIMESTAMP, ?),', $ingredient_count - 1);
		    $type_list = str_repeat('iiidsii', $ingredient_count);
		    $value_list = [];
		    foreach ($_POST['ingredient_name'] as $key => $eachIngredient) {
		        $value_list[] = $_POST['recipe_id'];
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
// exit;
		//update instructions
	    $instruction_count = count($_POST['step_description']);

		//the first integer in the type list will represent the recipe_id
		$type_list = 'i';
		$value_list = array($_POST['recipe_id']);
		if($instruction_count > 0){
			$question_mark_list = str_repeat('?,', $instruction_count - 1);
			$type_list .= str_repeat('i', $instruction_count);
	        $value_list = array_merge($value_list, $_POST['step_order']);
    	}
		//delete any instructions no longer in the recipe.
		$sql = "DELETE FROM recipe_instruction WHERE recipe_id = ?";
		if($instruction_count > 0)
        	$sql .= " AND `order` NOT IN({$question_mark_list}?)";

		$db->ExecuteSQL($sql
	        , $type_list
	        , $value_list);

		//if there are no instructions we won't insert anything
	    if($instruction_count > 0){
	    	//update anything that already has an instruction
	    	foreach ($_POST['step_description'] as $key => $eachInstruction) {
			    $db->ExecuteSQL("UPDATE recipe_instruction
			            SET description = ?
			            WHERE recipe_id = ? AND `order` = ?"
			        , 'sii'
			        , $eachInstruction, $_POST['recipe_id'], $_POST['step_order'][$key]);
		    }

		    //insert the instructions
		    $question_mark_list = str_repeat('(?,?,?),', $instruction_count - 1);
		    $type_list = str_repeat('isi', $instruction_count);
		    $value_list = [];
		    foreach ($_POST['step_description'] as $key => $eachInstruction) {
		        $value_list[] = $_POST['recipe_id'];
		        $value_list[] = $eachInstruction;
		        $value_list[] = $_POST['step_order'][$key];
		    }
		    $insert_result = $db->ExecuteSQL("INSERT IGNORE INTO recipe_instruction
			            (recipe_id, description, `order`) VALUES
			            $question_mark_list (?,?,?)"
			        , $type_list
			        , $value_list);
		}

	}

	if(!$error){
		//if all goes well then set the message they will see for successs. 
		//Send them back to the page they are already on so the tokens are set.
		deleteToken($_POST['csrf_token']);
		$_SESSION['message'][] = "{$_SESSION['name']}, your recipe was updated successfully!";
		header("Location: {$root}recipe/edit/{$_POST['recipe_id']}");
		exit();
	}

}