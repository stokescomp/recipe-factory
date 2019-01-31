Cooking is fun!<br />This is in the included file.
<?php echo '<h3>title: '.$_POST['title'].'</h3>';
echo '<h3>subtitle: '.$_POST['subtitle'].'</h3>';
echo '<h3>description: '.$_POST['description'].'</h3>';
echo '<h3>prep time: '.$_POST['prep_time_minutes'].'</h3>';
echo '<h3>preheat temperature: '.$_POST['preheat_temp'].'</h3>';
if($_POST['overridden_serving_amount'] != '') {
	echo '<h3>Override Serving Amount: '.$_POST['overridden_serving_amount'].'</h3>';
}
if($_POST['is_recipe_private'] == 'Y'){
	echo '<h3>The recipe is private</h3>';
}
if($_POST['is_recipe_draft'] == 'Y'){
	echo '<h3>It is a recipe draft</h3>';
}
echo '<h3>citation url: '.$_POST['citation_url'].'</h3>';
echo '<h3>citation person: '.$_POST['citation_person'].'</h3>';
if($_POST['holiday'] != 0) {
	echo '<h3>Holiday: '.$_POST['holiday'].'</h3>';
}
echo '<h3>makes amount: '.$_POST['makes_amount'].'</h3>';
echo '<h3>Servering Type: '.$_POST['serving_type'].'</h3>';
// echo '<pre>';
print_r($serving_type_list);
// echo '</pre>';
//get the type
//foreach($serving_type_list as $each_type){
	echo $each_type['name'].': '.$each_type['serving_type_id'].'<br/>';
//}

foreach($serving_type_list) as $each_type){
	if current id == each_type id
}


if($_POST['overridden_calories'] != '') {
	echo '<h3>Calories: '.$_POST['overridden_calories'].'</h3>';
}
?>