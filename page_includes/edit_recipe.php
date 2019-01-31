<section id="add_edit_recipe">
			<form action="" method="post" role="form" id="add_recipe_form" enctype="multipart/form-data">
				<div class="form-group">
					<div class="row">
						<div class="col-xs-1">
							<?php
							if($page == 'add_recipe'){
								echo '<span class="btn btn-default" onclick="processAddRecipe()">Add New recipe</span>';
							} else {
								echo '<span class="btn btn-default" onclick="processEditRecipe()">Save Recipe</span>';
							}
							?>
						</div>
						<div class="col-xs-1">
						<?php
							if($page == 'edit_recipe'){
								echo '<span class="btn btn-default" onclick="deleteRecipe()">Delete Recipe</span>';
							}
						?>
						</div>
					</div>
					<br />
					<div class="row">
						<div class="col-xs-6">
							<label for="title">Title: </label><input type="text" class="form-control" name="title" id="title"  value="<?php if(isset($_POST['title'])) echo $_POST['title']?>" placeholder="Enter Recipe Title" />
						</div>
						<div class="col-xs-3">
							<label for="is_recipe_private">Make Recipe Private: </label><input type="checkbox" name="is_recipe_private" id="is_recipe_private" value="1" <?php if(isset($_POST['is_recipe_private']) && $_POST['is_recipe_private'] == 'Y') echo "checked='checked'"?> />
						</div>
						<div class="col-xs-3">
							<label for="is_recipe_draft">Make Recipe Draft: </label><input type="checkbox" name="is_recipe_draft" id="is_recipe_draft" value="1" <?php if(isset($_POST['is_recipe_draft']) && $_POST['is_recipe_draft'] == 'Y') echo "checked='checked'"?> />
						</div>
						<div class="col-xs-3">
							<label for="citation_url">Recipe Citation URL </label><input type="text" name="citation_url" id="citation_url" <?php if(isset($_POST['citation_url'])) echo "value='{$_POST['citation_url']}'";?> />
						</div>
						<div class="col-xs-3">
							<label for="citation_person">Cited Person or Book </label><input type="text" name="citation_person" id="citation_person" <?php if(isset($_POST['citation_person'])) echo "value='{$_POST['citation_person']}'";?> />
						</div>
					</div>
				</div>

				<div class="form-group">
					<div class="row">
						<div class="col-xs-6">
							<label for="subtitle">Subtitle: </label><input type="subtitle" class="form-control" name="subtitle" id="subtitle" value="<?php if(isset($_POST['subtitle'])) echo $_POST['subtitle']?>" placeholder="Enter Subtitle" />
						</div>
						<div class="col-xs-6">
							<label for="user_group">User Group: </label> <a class="button right" href="#" onclick="$('#user_group').val('')">Clear Options</a>
							<select class="form-control" name="user_group[]" id="user_group" multiple>
							<?php
							foreach($user_group_list as $each){
								echo '<option value="'. $each['user_group_id'].'"';
								if(isset($_POST['user_group'])){
									if(in_array($each['user_group_id'],$_POST['user_group']))
										echo " selected='selected'";
								} else {
									if($each['selected'] == 1) 
										echo " selected='selected'";
								}
								echo '>'.$each['user_group_id'].' '.$each['name'].'</option>'."\n";
							}
							?>
							</select>
						</div>
					</div>
					<!-- echo formElement('input', 'email','3', true, 'Placeholder text');
					echo formElement('input', 'username','3', true, 'Placeholder text'); -->
				</div>

				<div class="form-group">
					<div class="row">
						<div class="col-xs-6">
							<label for="description">Description: </label><textarea rows="4" class="form-control" name="description" id="description" placeholder="Describe Recipe Here"><?php if(isset($_POST['description'])) echo $_POST['description']?></textarea>
						</div>
						<div class="col-xs-3">
							<label for="general_category">General Category: </label> <a class="button right" href="#" onclick="$('#general_category').val('')">Clear Options</a>
							<select class="form-control" name="general_category[]" id="general_category" multiple>
							<?php
							foreach($category_list as $each){
								//skip any that are not general
								if($each['is_general'] == 'N') continue;
								echo '<option value="'. $each['category_id'].'"';
								if(isset($_POST['general_category'])){
									if(in_array($each['category_id'],$_POST['general_category']))
										echo " selected='selected'";
								} else {
									if($each['selected'] == 1) 
										echo " selected='selected'";
								}
								echo '>'.$each['name'].'</option>'."\n";
							}
							?>
							
							</select>
						</div>
						<div class="col-xs-3">
							<label for="specific_category">Specific Category: </label> <a class="button right" href="#" onclick="$('#specific_category').val('')">Clear Options</a>
							<select class="form-control" name="specific_category[]" id="specific_category" multiple>
							<?php
							foreach($category_list as $each){
								//skip any that are general
								if($each['is_general'] == 'Y') continue;
								echo '<option value="'. $each['category_id'].'"';
								if(isset($_POST['specific_category'])){
									if(in_array($each['category_id'],$_POST['specific_category']))
										echo " selected='selected'";
								} else {
									if($each['selected'] == 1) 
										echo " selected='selected'";
								}
								echo '>'.$each['name'].'</option>'."\n";
							}
							?>
							
							</select>
						</div>
						<div class="col-xs-6">
							<label for="meal">Meal: </label> <a class="button right" href="#" onclick="$('#meal').val('')">Clear Options</a>
							<select class="form-control" name="meal[]" id="meal" multiple>
							<?php
							foreach($meal_list as $each){
								echo '<option value="'. $each['meal_id'].'"';
								if(isset($_POST['meal'])){
									if(in_array($each['meal_id'],$_POST['meal']))
										echo " selected='selected'";
								} else {
									if($each['selected'] == 1) 
										echo " selected='selected'";
								}
								echo '>'.$each['name'].'</option>'."\n";
							}
							?>
							</select>
						</div>
					</div>
				</div>
				<div class="form-group">
					<div class="row">
						<div class="col-xs-6">
							<label for="image">Choose Image: </label><input type="file" class="form-control" name="image" id="image" placeholder="Upload Image" />
						</div>
						<div class="col-xs-6">
							<label for="holiday">Holiday: </label>
							<select class="form-control" name="holiday" id="holiday">
								<option value="">Choose Holiday</option>
							<?php
							foreach($holiday_list as $each){
								echo '<option value="'. $each['holiday_id'].'"';
								if(isset($_POST['holiday']) && $_POST['holiday'] == $each['holiday_id'])
									echo " selected='selected'";
								echo '>'.$each['name'].'</option>'."\n";
							}
							?>
							</select>
						</div>
					</div>
				</div>
				<div class="form-group">
					<div class="row">
						<div class="col-xs-3">
							<label for="prep_time_minutes">Prep Time: </label><input type="text" class="form-control" name="prep_time_minutes" id="prep_time_minutes"  value="<?php if(isset($_POST['prep_time_minutes'])) echo $_POST['prep_time_minutes']?>" placeholder="Enter Minutes of Prep Time" />
						</div>
						<div class="col-xs-3">
							<label for="preheat_temp">Preheat Oven: </label><input type="text" class="form-control" name="preheat_temp" id="preheat_temp"  value="<?php if(isset($_POST['preheat_temp'])) echo $_POST['preheat_temp']?>" placeholder="Preheat Temperature" />
						</div>
						
						<div class="col-xs-3">
							<label for="makes_amount">Makes Amount: </label><input type="text" class="form-control" name="makes_amount" id="makes_amount"  value="<?php if(isset($_POST['makes_amount'])) echo $_POST['makes_amount']?>" placeholder="Makes Amount" />
						</div>
						<div class="col-xs-3">
							<label for="serving_type">Type: </label>
							<select class="form-control" name="serving_type" id="serving_type">
								<option value="">Choose Type</option>
							<?php
							foreach($serving_type_list as $each){
								echo '<option value="'. $each['serving_type_id'].'"';
								if(isset($_POST['serving_type']) && $_POST['serving_type'] == $each['serving_type_id'])
									echo " selected='selected'";
								echo '>'.$each['name'].'</option>'."\n";
							}
							?>
							</select>
						</div>
					</div>
				</div>
				<div class="form-group">
					<div class="row">
						<div class="col-xs-3">
							<label for="overridden_serving_amount">Number Serves: </label><input type="text" class="form-control" name="overridden_serving_amount" id="overridden_serving_amount"  value="<?php if(isset($_POST['overridden_serving_amount'])) echo $_POST['overridden_serving_amount']?>" placeholder="Serves" />
						</div>
						<div class="col-xs-3">
							<label for="overridden_calories">Calories: </label><input type="text" class="form-control" name="overridden_calories" id="overridden_calories"  value="<?php if(isset($_POST['overridden_calories'])) echo $_POST['overridden_calories']?>" placeholder="Calories" />
						</div>
						<div class="col-xs-3">
							<label for="calories">Calculated Calories: </label> Auto filled later.
						</div>
						<div class="col-xs-3">
							<label for="serving_number">Calculated Number Serves: </label>Auto filled later.
						</div>
					</div>
				</div>
				<div class="form-group">
					<div class="row">
						<div class="col-xs-1">
							<span class="btn btn-default" onclick="addIngredient()">Add Ingredient</span>
						</div>
					</div>
				</div>
				<div id="ingredients_list">
					<div class="row">
						<div class="col-xs-2">
							<label for="ingredient_name_1">Ingredient: </label>
						</div>
						<div class="col-xs-2">
							<label for="ingredient_measurement_1">Measurement: </label>
						</div>
						<div class="col-xs-1">
							<label for="ingredient_amount_1">Amount: </label>
						</div>
						<div class="col-xs-1">
							<label for="ingredient_amount_1">Fraction: </label>
						</div>
						<div class="col-xs-3">
							<label for="ingredient_note_1">Note: </label>
						</div>
					</div>
					<?php
					foreach ($ingredient_list as $key => $eachIngredient) {
						$current_number = $key + 1;
						$eachIngredient['amount'] = str_replace('.000', '', $eachIngredient['amount']);
						$whole_number = $eachIngredient['amount'];
						if(strpos($eachIngredient['amount'],".") !== false)
							$whole_number = substr($eachIngredient['amount'], 0, strpos($eachIngredient['amount'], "."));
						if($whole_number == 0) $whole_number = '';

						//hide the fraction part when we are using gallons and quarts etc. that only need whole numbers.
						if($eachIngredient['number_type'] == 'WHOLE' || $eachIngredient['number_type'] == 'DECIMAL'){
							$hide_fraction = true;
							if($eachIngredient['number_type'] == 'WHOLE')
								$eachIngredient['amount'] = substr($eachIngredient['amount'], 0, strpos($eachIngredient['amount'],"."));
						}
						else
							$hide_fraction = false;
					?>
					<div class="form-group ingredient" id="ingredient_<?php echo $current_number ?>">
						<input type="hidden" name="ingredient_order[]" value="<?php echo $current_number ?>" />
						<div class="row">
							<div class="col-xs-2">
								<label for="ingredient_name_<?php echo $current_number ?>" class="hide">Ingredient: </label>
								<select class="form-control ingredient_dropdown" name="ingredient_name[]" id="ingredient_name_<?php echo $current_number ?>" onmousedown="clickIngredientDropdown(this)">
									<?php
									if($eachIngredient['ingredient_id'] == '') 
										echo "<option value=\"\">Choose Ingredient</option>";
									else
										echo "<option value=\"{$eachIngredient['ingredient_id']}\">".htmlspecialchars($eachIngredient['ingredient_name'])."</option>";
									?>
								</select>
							</div>
							<div class="col-xs-2">
								<select class="form-control" name="ingredient_measurement[]" id="ingredient_measurement_<?php echo $current_number ?>" onchange="changeMeasurement(this)">
									<option value="">Choose Measurement</option>
									<?php 
									foreach($measurement_list as $each){
										echo '<option value="'.$each['measurement_id'].'" ';
										if($eachIngredient['measurement_id'] == $each['measurement_id']) echo "selected='selected'";
										echo ' data-number-type="' . $each['number_type'].'">'.htmlspecialchars($each['name']).'</option>';
									}
									?>
								</select>
							</div>
							<div class="col-xs-1">
								<input type="text" class="form-control" name="ingredient_amount[]" id="ingredient_amount_<?php echo $current_number ?>" 
									value="<?php echo $whole_number?>" onchange="changeAmount(this)" data-number-type="<?php echo $eachIngredient['number_type']?>" />
							</div>
							<?php if($hide_fraction == false){ ?>
							<div class="col-xs-1">
								<select class="form-control" name="ingredient_fraction_amount[]" id="ingredient_fraction_amount_<?php echo $current_number ?>">
									<option value=""></option>
									<option value="0.75" <?php if(strpos($eachIngredient['amount'],".75") !== false) echo "selected='selected'";?>>3/4</option>
									<option value="0.66" <?php if(strpos($eachIngredient['amount'],".66") !== false) echo "selected='selected'";?>>2/3</option>
									<option value="0.5" <?php if(strpos($eachIngredient['amount'],".5") !== false) echo "selected='selected'";?>>1/2</option>
									<option value="0.33" <?php if(strpos($eachIngredient['amount'],".33") !== false) echo "selected='selected'";?>>1/3</option>
									<option value="0.25" <?php if(strpos($eachIngredient['amount'],".25") !== false) echo "selected='selected'";?>>1/4</option>
									<option value="0.125" <?php if(strpos($eachIngredient['amount'],".125") !== false) echo "selected='selected'";?>>1/8</option>
								</select>
							</div>
							<?php } else { ?>
							<div class="col-xs-1">
								<select class="form-control hide" name="ingredient_fraction_amount[]" id="ingredient_fraction_amount_<?php echo $current_number ?>">\
									<option value=""></option>
									<option value="0.75">3/4</option>
									<option value="0.66">2/3</option>
									<option value="0.5">1/2</option>
									<option value="0.33">1/3</option>
									<option value="0.25">1/4</option>
									<option value="0.125">1/8</option>
								</select>
							</div>
							<?php } ?>
							<div class="col-xs-3">
								<input type="text" class="form-control" name="ingredient_note[]" id="ingredient_note_<?php echo $current_number ?>" 
									placeholder="Type A Note" value='<?php echo htmlspecialchars($eachIngredient['note'], ENT_QUOTES)?>' />
							</div>
							<div class="col-xs-1">
								<span class="btn btn-default <?php if(count($ingredient_list) == 1) echo 'hide';?> deleteIngredientBtn" onclick="deleteIngredient(<?php echo $current_number ?>)">x</span>
							</div>
						</div>
					</div>
					<?php } ?>
				</div>
				<div class="form-group">
					<div class="row">
						<div class="col-xs-1">
							<span class="btn btn-default" onclick="addIngredient()">Add Ingredient</span>
						</div>
					</div>
				</div>
				<div class="form-group">
					<div class="row">
						<div class="col-xs-1">
							<label>Instructions: </label>
							<span class="btn btn-default" onclick="addStep()">Add Step</span>
						</div>
					</div>
				</div>
				<div id="steps_list">
					<?php foreach ($instruction_list as $key => $eachInstruction) { 
						$current_number = $key + 1;
					?>
					<div class="form-group" id="step_<?php echo $current_number ?>">
						<input type="hidden" name="step_order[]" value="<?php echo $current_number ?>" />
						<div class="row">
							<div class="col-xs-1">
								<span class="step_number"><?php echo $current_number ?></span>
							</div>
							<div class="col-xs-6">
								<textarea class="form-control" name="step_description[]" id="step_description_<?php echo $current_number ?>"><?php echo $eachInstruction['description'];?></textarea>
							</div>
							<div class="col-xs-4">
								<input type="file" class="form-control" name="step_file[]" id="step_file_<?php echo $current_number ?>" />
							</div>
							<span class="btn btn-default <?php if(count($instruction_list) == 1) echo 'hide';?> deleteStepBtn" onclick="deleteStep(<?php echo $current_number ?>)">x</span>
						</div>
					</div>
					<?php } ?>
				</div>
				<div class="form-group">
					<div class="row">
						<div class="col-xs-1">
							<span class="btn btn-default" onclick="addStep()">Add Step</span>
						</div>
					</div>
				</div>

				<div class="form-group">
					<div class="row">
						<div class="col-xs-1">
							<?php
							if($page == 'add_recipe'){
								echo '<span class="btn btn-default" onclick="processAddRecipe()">Add New recipe</span>';
							} else {
								echo '<span class="btn btn-default" onclick="processEditRecipe()">Save Recipe</span>';
							}
							?>
							<input type="hidden" name="csrf_token" id="csrf_token" value="<?php echo $csrf_token?>" />
							<input type="hidden" name="recipe_id" id="recipe_id" value="<?php echo $recipe_id?>" />
						</div>
					</div>
				</div>
			</form>
		</section>

