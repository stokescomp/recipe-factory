<?php
$title = "Manager Recipes";
$page = "manage_recipes";
$header_message = "Manage Recipes for The Recipe Factory";
require('../includes/init.php');
require($_SERVER['DOCUMENT_ROOT'].'/page_includes/header.php');
$sql = "SELECT * FROM recipe ORDER BY title";
$recipeList = $db->FetchAll($sql);
?>
<article id="main">
	<div style="margin-left:200px;height:300px;width:300px; overflow:auto">
	<p>Manage Recipes information</p>
   	<?php
	foreach($recipeList as $eachRecipe){
		echo "<p>
		<a href='/recipe-factory/recipe/view/{$eachRecipe['recipe_id']}'>View</a>
		<a href='/recipe-factory/recipe/edit/{$eachRecipe['recipe_id']}'>Edit ".htmlspecialchars($eachRecipe['title'], ENT_QUOTES)."</a>
		</p>";
	}
   	?>	
	</div>
</article>
<?php require($_SERVER['DOCUMENT_ROOT'].'/page_includes/footer.php');

// [recipe_id] => 1
// [serving_type_id] => 3
// [holiday_id] => 
// [is_recipe_private] => 0
// [is_recipe_draft] => 0
// [citation_url] => 
// [citation_person] => 
// [title] => Jello
// [subtitle] => the red kind - with bananas
// [description] => A friend of mine likes this better than all of my dessert recipes.
// [image_path_large] => 
// [image_path_medium] => 
// [image_path_small] => 
// [is_serving_number_overridden] => 0
// [serving_number] => 1
// [overridden_serving_amount] => 0
// [makes_amount] => 24
// [prep_time_minutes] => 5
// [preheat_temp] => 
// [is_calories_overridden] => 0
// [calories] => 
// [overridden_calories] => 
// [creation_date] => 2014-01-01 19:51:35
// [created_by] => 2
// [update_date] => 2014-07-19 23:19:21
// [updated_by] => 1