debug = 1;
root = '/recipe-factory';
function addIngredient(){
	//show delete ingredient button if there is 1 left before adding another.
	number_ingredients = $('#ingredients_list .ingredient').length;
	ingredient_id = number_ingredients + 1;
	if(number_ingredients == 1) $('#ingredients_list .deleteIngredientBtn:eq(0)').removeClass('hide');
	template_ingredient_measurement = $('#template_ingredient_measurement').html().replace(/ingredient_measurement_number/g,'ingredient_measurement_'+ingredient_id);
	$('#ingredients_list').append('<div class="form-group ingredient" id="ingredient_'+ingredient_id+'">\
			<input type="hidden" name="ingredient_order[]" value="'+ingredient_id+'" />\
			<div class="row">\
				<div class="col-xs-2">\
					<select class="form-control ingredient_dropdown" name="ingredient_name[]" id="ingredient_name_'+ingredient_id+'" onclick="clickIngredientDropdown(this)">\
						<option value="">Choose Ingredient</option>\
					</select>\
				</div>\
				<div class="col-xs-2">\
					'+template_ingredient_measurement+'\
				</div>\
				<div class="col-xs-1">\
					<input type="text" class="form-control" name="ingredient_amount[]" id="ingredient_amount_'+ingredient_id+'" onchange="changeAmount(this)" />\
				</div>\
				<div class="col-xs-1">\
					<select class="form-control" name="ingredient_fraction_amount[]" id="ingredient_fraction_amount_'+ingredient_id+'">\
						<option value=""></option>\
						<option value="0.75">3/4</option>\
						<option value="0.66">2/3</option>\
						<option value="0.5">1/2</option>\
						<option value="0.33">1/3</option>\
						<option value="0.25">1/4</option>\
						<option value="0.125">1/8</option>\
					</select>\
				</div>\
				<div class="col-xs-3">\
					<input type="text" class="form-control" name="ingredient_note[]" id="ingredient_note_'+ingredient_id+'" placeholder="Type A Note" />\
				</div>\
				<div class="col-xs-1">\
					<button class="btn btn-default deleteIngredientBtn" onclick="deleteIngredient('+ingredient_id+')">x</button>\
				</div>\
			</div>\
		</div>');
	ingredient_id++;
}

function addStep(){
	number_steps = $('#steps_list .deleteStepBtn').length;
	step_id = number_steps + 1;
	if(number_steps == 1) $('#steps_list .deleteStepBtn').removeClass('hide');
	$('#steps_list').append('<div class="form-group" id="step_'+step_id+'"><div class="row">'+
				'<input type="hidden" name="step_order[]" value="'+step_id+'" />'+
				'<div class="col-xs-1">'+
					'<span class="step_number">'+step_id+'</span>'+
				'</div>'+
				'<div class="col-xs-6">'+
					'<textarea class="form-control" name="step_description[]" id="step_description_'+step_id+'" class="step_description"></textarea>'+
				'</div>'+
				'<div class="col-xs-4">'+
					'<input type="file" class="form-control" name="step_file[]" id="step_file_'+step_id+'" class="step_file" />'+
				'</div>'+
				'<button class="btn btn-default deleteStepBtn" onclick="deleteStep('+step_id+')">x</button>'+
			'</div>'+
		'</div>');
	step_id++;
	//reorder the step numbers
	id = 1;
	$('#steps_list .step_number').each(function(){
		$(this).text(id);
		console.log(id)
		id++;
	});
}
function deleteIngredient(id){
	$('#ingredient_'+id).remove();
	number_ingredients = $('#ingredients_list .deleteIngredientBtn').length;
	if(number_ingredients == 1) $('#ingredients_list .deleteIngredientBtn').addClass('hide');
}

function deleteStep(id){
	$('#step_'+id).remove();
	number_steps = $('#steps_list .deleteStepBtn').length;
	if(number_steps == 1) $('#steps_list .deleteStepBtn').addClass('hide');

	//reorder the step numbers
	id = 1;
	$('#steps_list .step_number').each(function(){
		$(this).text(id);
		console.log(id)
		id++;
	});
}
function processAddRecipe(){
	//error = false;
	//error = check_required_text('title');
	//error = check_required_text('meal');
	//error = check_required_text('description');

	// if(error){
	// 	alert('there are errors. Fix them before trying again.')
	// } else {
		//no error found
		document.forms['add_recipe_form'].submit();
	// }
}

function processEditRecipe(){
	document.forms['add_recipe_form'].submit();
}

function check_required_text(id){
	//check if there is a value in the input
	if($('#'+id).val()==""){
		error_result = true;
		alert('Enter the '+id);
	} else {
		error_result = false
	}
	return error || error_result;
}

function changeMeasurement(self){
	id = self.id.replace('ingredient_measurement_','');
	number_type = $('option:selected',self).attr('data-number-type');
	$('#ingredient_amount_'+id).val('').attr('data-number-type',number_type);
	if(number_type == 'WHOLE' || number_type == 'DECIMAL'){
		$('#ingredient_fraction_amount_'+id).addClass('hide');
		$('#ingredient_fraction_amount_'+id).val('');
	}
	else {
		$('#ingredient_fraction_amount_'+id).removeClass('hide');
	}
}

function changeAmount(self){
	id = self.id.replace('ingredient_amount_','');
	number_type = $(self).attr('data-number-type');
	error = false;
	value = $(self).val();
	if(number_type == 'WHOLE' || number_type == 'FRACTION'){
		if(value.indexOf('.') !== -1){
			value = value.substring(0,value.indexOf('.'));
			message = 'Enter only whole numbers for this type of measurement';
			error = true;
		}
	}
	if(value.replace(/[^0-9\.+]/g,'') != value){
		value = value.replace(/[^0-9\.+]/g,'');
		message = 'Only enter numbers in the amount.';
		error = true;
	}
	if(error){
		$(self).val(value);
		showMessage('errors',message, 'error');
	}
}

function deleteRecipe(){
	makeHttpRequestPOST(root+'/process_includes/ajax.php', 'delete_recipe=1&recipe_id='+G_recipe_id
		+'&csrf_token='+G_csrf_token, callback_delete_recipe);
}

function callback_delete_recipe(text){
	console.log(text);
	text = parseJSON(text);
	if(text.success){
		window.location.href = root+'/manage_recipes';
	} else {
		showMessage('errors','There was an error processing this request: ' + text, 'error');
	}
}

