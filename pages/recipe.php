<?php
//think about changing the dropdowns for choosing multiple options into either checkboxes or labels with an x to remove 
//and an add button to pick from a list to add. Look at how gmail lets you add people to an email: there is an x to the right of the people.
require('../includes/init.php');
echo $action;
if($action == 'edit'){
	$title = "Edit Recipe";
	$page = "edit_recipe";
	$header_message = "Edit Recipe for The Recipe Factory";
	require($_SERVER['DOCUMENT_ROOT'].'/process_includes/edit_recipe.php');
	if($category == 'recipe'){
		$recipe_id = $data;
		//no error
		if(count($error) == 0){
			//get the variables from the database and save them to $_POST
			$recipe_data = $db->FetchAll("SELECT * FROM {$prepend_table}recipe WHERE recipe_id = ?", 'i', $recipe_id)[0];
			// print_r($recipe_data);exit;
			$_POST['title'] = htmlspecialchars($recipe_data['title'], ENT_QUOTES);
			$_POST['subtitle'] = htmlspecialchars($recipe_data['subtitle'], ENT_QUOTES);
			$_POST['description'] = $recipe_data['description'];
			$_POST['prep_time_minutes'] = $recipe_data['prep_time_minutes']; 
			$_POST['preheat_temp'] = $recipe_data['preheat_temp'];
			$_POST['overridden_serving_amount'] = $recipe_data['overridden_serving_amount'];
			$_POST['is_recipe_private'] = $recipe_data['is_recipe_private'];
			$_POST['is_recipe_draft'] = $recipe_data['is_recipe_draft'];
			$_POST['citation_url'] = htmlspecialchars($recipe_data['citation_url'], ENT_QUOTES);
			$_POST['citation_person'] = htmlspecialchars($recipe_data['citation_person'], ENT_QUOTES);
			$_POST['holiday'] = $recipe_data['holiday_id'];
			$_POST['makes_amount'] = $recipe_data['makes_amount'];
			$_POST['serving_type'] = $recipe_data['serving_type_id'];
			$_POST['overridden_calories'] = $recipe_data['overridden_calories'];
		}
	}
} else if($action == 'view') {
	$title = "View New Recipe";
	$page = "view_recipe";
	$header_message = "View Recipe for The Recipe Factory";
	$recipe_id = $data;
		//no error
		if(count($error) == 0){
			//get the variables from the database and save them to $_POST
			$recipe_data = $db->FetchAll("SELECT * FROM {$prepend_table}recipe WHERE recipe_id = ?", 'i', $recipe_id)[0];
			// print_r($recipe_data);exit;
			$_POST['title'] = htmlspecialchars($recipe_data['title'], ENT_QUOTES);
			$_POST['subtitle'] = htmlspecialchars($recipe_data['subtitle'], ENT_QUOTES);
			$_POST['description'] = $recipe_data['description'];
			$_POST['prep_time_minutes'] = $recipe_data['prep_time_minutes']; 
			$_POST['preheat_temp'] = $recipe_data['preheat_temp'];
			$_POST['overridden_serving_amount'] = $recipe_data['overridden_serving_amount'];
			$_POST['is_recipe_private'] = $recipe_data['is_recipe_private'];
			$_POST['is_recipe_draft'] = $recipe_data['is_recipe_draft'];
			$_POST['citation_url'] = htmlspecialchars($recipe_data['citation_url'], ENT_QUOTES);
			$_POST['citation_person'] = htmlspecialchars($recipe_data['citation_person'], ENT_QUOTES);
			$_POST['holiday'] = $recipe_data['holiday_id'];
			$_POST['makes_amount'] = $recipe_data['makes_amount'];
			$_POST['serving_type'] = $recipe_data['serving_type_id'];
			$_POST['overridden_calories'] = $recipe_data['overridden_calories'];
		}
} else {
	$title = "Add New Recipe";
	$page = "add_recipe";
	$header_message = "New Recipe for The Recipe Factory";
	require $_SERVER['DOCUMENT_ROOT'].'/process_includes/add_recipe.php';
	$recipe_id = '';
}

require $_SERVER['DOCUMENT_ROOT'].'/page_includes/header.php';
if(checkLoggedIn('normal') == false) exit();
// print_r($_POST);

//user group list
//this subquery looks up if the user group is attached to the recipe and makes a column called selected with a 1 if it is used by the recipe and null otherwise
$sql = "SELECT user_group_id, name, 
(SELECT 1 FROM {$prepend_table}recipe_to_user_group WHERE recipe_id = ? AND user_group_id = ug.user_group_id) AS selected 
FROM {$prepend_table}user_group ug JOIN {$prepend_table}user_to_user_group USING (user_group_id) WHERE user_id = ?";
$user_group_list = $db->FetchAll($sql, 'ii', $recipe_id, $_SESSION['user_id']);

//category list
$sql = "SELECT category_id, name, is_general,
(SELECT 1 FROM {$prepend_table}recipe_to_category WHERE recipe_id = ? AND category_id = c.category_id) AS selected 
FROM {$prepend_table}category c";
$category_list = $db->FetchAll($sql, 'i', $recipe_id);

//meal list
$sql = "SELECT meal_id, name, 
(SELECT 1 FROM {$prepend_table}recipe_to_meal WHERE recipe_id = ? AND meal_id = m.meal_id) AS selected
FROM {$prepend_table}meal m";
$meal_list = $db->FetchAll($sql, 'i', $recipe_id);

//holiday list
$sql = "SELECT holiday_id, name FROM {$prepend_table}holiday";
$holiday_list = $db->FetchAll($sql);

//serving type list (Size of pan etc.)
$sql = "SELECT serving_type_id, name FROM {$prepend_table}serving_size";
$serving_type_list = $db->FetchAll($sql);

//measurement list
$sql = "SELECT measurement_id, name, number_type FROM {$prepend_table}measurement";
$measurement_list = $db->FetchAll($sql);

//ingredients
$sql = "SELECT ingredient_id, (SELECT name FROM ingredient WHERE ingredient_id = ri.ingredient_id) as ingredient_name, measurement_id, ri.amount, note, number_type
	FROM recipe_ingredient ri LEFT JOIN measurement USING(measurement_id)
	WHERE recipe_id = ? ORDER BY ri.order";
$ingredient_list = $db->FetchAll($sql, 'i', $recipe_id);
if(count($ingredient_list) == 0) 
	$ingredient_list = array(array('ingredient_id'=>'','ingredient_name'=>'','measurement_id'=>'','amount'=>'','note'=>'','number_type'=>''));

//instructions
$sql = "SELECT description FROM recipe_instruction WHERE recipe_id = ?";
$instruction_list = $db->FetchAll($sql, 'i', $recipe_id);
if(count($instruction_list) == 0) 
	$instruction_list = array(array('description'=>''));

// echo '<p>.</p><pre>'.print_r($user_group_list,1).'</pre>';
?>
<script src='<?php echo $root;?>js/functions_view_ingredients.js?t=<?php echo filemtime('../js/functions_view_ingredients.js')?>'></script>
<script src='<?php echo $root;?>js/functions_add_recipe.js?t=<?php echo filemtime('../js/functions_add_recipe.js')?>'></script>
<script>
onload = getIngredientTree;
G_current_ingredient_dropdown = '';
G_recipe_id = '<?php echo $recipe_id?>';

function pickIngredient(self){
	$('#ingredient_box div.ingredients').removeClass('active');
	//make it the active ingredient
	$(self).addClass('active');
	currentIngredientId = $(self).parent().prop('id').replace('ingredient','');
	currentIngredientName = $('.ingredientName', self).text();
	$('#ingredient_name_'+G_current_ingredient_dropdown+' option').val(currentIngredientId);
	$('#ingredient_name_'+G_current_ingredient_dropdown+' option').text(currentIngredientName);
	//remove all acitve classes so clicking same dropdown will now work
	$('.ingredient_dropdown').removeClass('active');
	$('#ingredient_box').fadeOut(500);
}
function clickFoodGroup(self){}

$(document).ready(function() {
	$(".ingredient_dropdown").mousedown(function(e) {
	    e.preventDefault();
	});
});

function clickIngredientDropdown(self){
	//make the current dropdown have an active class
	if($(self).hasClass('active')){
		$('#ingredient_box').hide();
		$(self).removeClass('active');
	} else {
		//remove all the active classes and then readd it to the current element then move the ingredient box under the current dropdown
		$('.ingredient_dropdown').removeClass('active');
		$(self).addClass('active');
		$('#ingredient_box').css({
			top: $(self).offset().top + $(self).height() + 18 + 'px',
			left: $(self).offset().left + 'px'
		});

		G_current_ingredient_dropdown = $(self).prop('id').replace('ingredient_name_','');
		//remove active ingredients
		$('#ingredient_box div.ingredients').removeClass('active');
		//get current ingredient and make it the active ingredient
		current_id = $('#ingredient_name_'+G_current_ingredient_dropdown).val();
		if(current_id != ''){
			$('#ingredient'+current_id).addClass('active');
			//close all food groups
			//open the food groups to the active ingredient
		}
		$('#ingredient_box').show();
	}
}
</script>
<style>
#ingredient_box{
	display: none;
	position: absolute;
}
#ingredient_box{
	height: 212px;
	min-height: 150px;
}
.noChildren{
	display: none;
}
</style>
	<section id="main" class="clear">
		<?php if($action == 'edit' || $action == ''){
			include('../page_includes/edit_recipe.php');
		} else if($action == 'view'){
			include('../page_includes/view_recipe.php');
		} ?>

	</section>


	<section class="hide">
		<div id="template_ingredient_measurement">
			<select class="form-control" name="ingredient_measurement[]" id="ingredient_measurement_number" onchange="changeMeasurement(this)">
				<option value="">Choose Measurement</option>
				<?php
				foreach($measurement_list as $each){
					echo '<option value="'.$each['measurement_id'].'" ';
					if(isset($_POST['ingredient_measurement_']) == $each['measurement_id']) echo "selected='selected'";
					echo ' data-number-type="' . $each['number_type'].'">'.$each['name'].'</option>';
				}
				?>
			</select>
		</div>
	</section>
	<div id="ingredient_box" class="dropdown">
		<div id="ingredients"></div>
	</div>
<?php require($_SERVER['DOCUMENT_ROOT'].'/page_includes/footer.php');

?>