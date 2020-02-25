// The functions for javascript on edit recipes
debug = 1;
root = '/recipe-factory';
//listener functions with their ajax calls and callback functions
function clickIngredient(event, self){
	event = event || window.event // cross-browser event
    if (event.stopPropagation) {
        // W3C standard variant
        event.stopPropagation()
    } else {
        // IE variant
        event.cancelBubble = true
    }
    //console.log(self.parentNode.parentNode.id)
	id = self.parentNode.parentNode.id.replace('ingredient','');
	makeHttpRequestPOST(root+'/process_includes/ajax.php', 'view_ingredient=1&ingredient_id='
		+id+'&csrf_token='+G_csrf_token, callback_clickIngredient);
	$('#ingredient_details_title').text('Ingredient');
	$('#active_type').val('ingredient');
	$('#active_id').val(id);
	//console.log('a'+$('#active_id').val());
	$('#ingredient_details_box,#viewableSection').removeClass('hide');
	$('#addableSection,#editableSection').addClass('hide');
	hideMessage('status');
}
//only used on add/edit recipe page
pickIngredient = function(){};

function callback_clickIngredient(text){
	//console.log(text);
	text = parseJSON(text);
	if(text == false) return;
	ingredient_name = text.ingredient_name;
	calories_per_ounce = text.calories_per_ounce
	if(calories_per_ounce == null) calories_per_ounce = '';
	special_qualities = text.special_qualities
	food_group_id = text.food_group_id
	food_group_name = text.food_group_name
	details_view = '<label>Name:</label> '+ingredient_name+'<br /><label>In Food Group:</label> '+
		food_group_name+'<br /><label>Calories / Ounce:</label> '+calories_per_ounce+'<br /><label>Special Qualities:</label> '+
		special_qualities;
	details_edit = "<label for='input_name'>Name:</label> <input type='text' id='input_name' value='"+ingredient_name+"' class='form-control' />"+
		"<label for='input_food_group'>In Food Group:</label>"+
		"<select class='food_group_dropdown form-control' id='input_food_group' onmousedown='clickFoodGroupDropdown(this)'>"+
		"<option value='"+food_group_id+"'>"+food_group_name+"</option></select>"+
		"<label for='input_calories_per_ounce'>Calories / Ounce:</label> <input type='text' id='input_calories_per_ounce' value='"+calories_per_ounce+"' class='form-control' />"+
		"<label for='input_special_qualities'>Special Qualitites:</label> <input type='text' id='input_special_qualities' value='"+special_qualities+"' class='form-control' />"+
		"<a class='btn btn-success' href='#' onclick='editIngredient()'>Save</a>";
	$('#viewableSection').removeClass('hide');
	$('#editableSection').addClass('hide');
	$('#active_parent_food_group_id').val(food_group_id);
	$('#ingredient_details_view').html(details_view);
	$('#ingredient_details_edit').html(details_edit);
}

function clickFoodGroup(event, self){
	event = event || window.event // cross-browser event
    if (event.stopPropagation) {
        // W3C standard variant
        event.stopPropagation()
    } else {
        // IE variant
        event.cancelBubble = true
    }
	id = self.parentNode.id.replace('foodGroupItem','');
	makeHttpRequestPOST(root+'/process_includes/ajax.php', 'view_food_group=1&food_group_id='
		+id+'&csrf_token='+G_csrf_token, callback_clickFoodGroup);
	$('#ingredient_details_title').text('Food Group');
	$('#active_type').val('food_group');
	$('#active_id').val(id);
	$('#ingredient_details_box,#viewableSection').removeClass('hide');
	$('#addableSection,#editableSection').addClass('hide');
	hideMessage('status');
}
function callback_clickFoodGroup(text){
	//console.log(text);
	text = parseJSON(text);
	if(text == false) return;
	food_group_name = text.food_group_name;
	food_group_id = text.food_group_id;
	parent_food_group_id = text.parent_food_group_id;
	parent_food_group_name = text.parent_food_group_name;
	if(parent_food_group_name === '') parent_food_group_name = 'None: Top Level Food Group';
	if(food_group_id === null) food_group_id = '0';
	if(parent_food_group_id === null) parent_food_group_id = '0';
	if(food_group_name === null) food_group_name = '';
	details_view = '<label>Name:</label> '+food_group_name+'<br /><label>In food group:</label> '+
		parent_food_group_name;
	details_edit = "<label for='input_name'>Name:</label> <input type='text' id='input_name' value='"+food_group_name+"' class='form-control' />"+
		"<label for='input_food_group'>In Food Group:</label>"+
		"<select class='food_group_dropdown form-control' id='input_food_group' onmousedown='clickFoodGroupDropdown(this)'>"+
		"<option value='"+parent_food_group_id+"'>"+parent_food_group_name+"</option></select>"+
		"<a class='btn btn-success' href='#' onclick='editIngredient()'>Save</a>";
	$('#viewableSection').removeClass('hide');
	$('#editableSection').addClass('hide');
	$('#active_parent_food_group_id').val(food_group_id);
	$('#ingredient_details_view').html(details_view);
	$('#ingredient_details_edit').html(details_edit);
}

function clickAddIngredient(){
	$('#ingredient_details_title').text('Add Ingredient');
	$('#active_type').val('add_ingredient');
	//clear values
	$('#active_id').val('');
	hideMessage('status');
	$('#ingredient_details_box,#addableSection').removeClass('hide');
	$('#viewableSection,#editableSection').addClass('hide');
	details = "<label for='input_name_add'>Name:</label> <input type='text' id='input_name_add' class='form-control' />"+
		"<label for='input_food_group'>In Food Group:</label>"+
		"<select class='food_group_dropdown form-control' id='input_food_group' onmousedown='clickFoodGroupDropdown(this)'>"+
		"<option value='0'>None: Top Level Food Group</option></select>"+
		"<label for='input_calories_per_ounce_add'>Calories / Ounce:</label> <input type='text' id='input_calories_per_ounce_add' class='form-control' />"+
		"<label for='input_special_qualities_add'>Special Qualitites:</label> <input type='text' id='input_special_qualities_add' class='form-control' />"+
		"<a class='btn btn-success' href='#' onclick='addIngredient()'>Add New</a>";
	$('#ingredient_details_add').html(details);
}

function addIngredient(){
	getIngredientDetailFormData();
	if(data != '') makeHttpRequestPOST(root+'/process_includes/ajax.php', 'edit_ingredient=1&type='
		+type+'&action=add&'+data+'&csrf_token='+G_csrf_token, callback_addIngredient);
}
function callback_addIngredient(text){
	//console.log(text);
	text = parseJSON(text);
	if(text == false) return;
	if(text.success){
		showMessage('status','New Ingredient added successfully');
		$('#input_name_add').val('').focus();
		$('#input_calories_per_ounce_add').val('');
		$('#input_special_qualities_add').val('');
		getIngredientTree();
	}
	else
		showMessage('status',text.message, 'error');
}

function clickAddFoodGroup(){
	$('#ingredient_details_title').text('Add Food Group');
	$('#active_type').val('add_food_group');
	//clear values
	$('#active_id').val('');
	hideMessage('status');
	$('#ingredient_details_box,#addableSection').removeClass('hide');
	$('#viewableSection,#editableSection').addClass('hide');
	details = "<label for='input_name_add'>Name:</label> <input type='text' id='input_name_add' class='form-control' />"+
		"<label for='input_food_group'>In Food Group:</label>"+
		"<select class='food_group_dropdown form-control' id='input_food_group' onmousedown='clickFoodGroupDropdown(this)'>"+
			"<option value='0'>None: Top Level Food Group</option></select>"+
		"<a class='btn btn-success' href='#' onclick='addFoodGroup()'>Add New</a>";
	$('#ingredient_details_add').html(details);
}

function addFoodGroup(){
	getIngredientDetailFormData();
	if(data != '') makeHttpRequestPOST(root+'/process_includes/ajax.php', 'edit_ingredient=1&type='
		+type+'&action=add&'+data+'&csrf_token='+G_csrf_token, callback_addFoodGroup);
}
function callback_addFoodGroup(text){
	//console.log(text);
	text = parseJSON(text);
	if(text == false) return;
	if(text.success){
		showMessage('status','New Food Group added successfully');
		$('#input_name_add').val('');
		getIngredientTree();
		getFoodGroupTree();
	}
	else
		showMessage('status',text.message, 'error');
}


function toggleEditIngredient(){
	$('#viewableSection').toggleClass('hide');
	$('#editableSection').toggleClass('hide');
}
function getIngredientDetailFormData(){
	type = $('#active_type').val();
	add_string = '';
	if(type.indexOf('add_') != -1) {
		add_string = '_add';
		type = type.replace('add_','');
		id = '';
	}
	data = '';
	if(type == 'ingredient') {
		name = $('#input_name'+add_string).val();
		new_food_group_id = $('#input_food_group').val();
		calories_per_ounce = $('#input_calories_per_ounce'+add_string).val();
		special_qualities = $('#input_special_qualities'+add_string).val();
		data = 'ingredient_id='+id+'&name='+name+'&new_food_group_id='+new_food_group_id+'&calories_per_ounce='+calories_per_ounce+'&special_qualities='+special_qualities;
	} else if(type == 'food_group') {
		name = $('#input_name'+add_string).val();
		new_food_group_id = $('#input_food_group').val();
		data = 'food_group_id='+id+'&name='+name+'&new_food_group_id='+new_food_group_id;
	}
}

function editIngredient(){
	id = $('#active_id').val();
	parent_food_group_id = $('#active_parent_food_group_id').val();
	getIngredientDetailFormData();
	if(data != '') makeHttpRequestPOST(root+'/process_includes/ajax.php', 'edit_ingredient=1&type='+type+'&action=edit&ingredient_id='+id
		+'&parent_food_group_id='+parent_food_group_id+'&'+data+'&csrf_token='+G_csrf_token, callback_editIngredient);
}
function callback_editIngredient(text){
	//console.log(text);
	text = parseJSON(text);
	if(text == false) return;
	if(text.success){
		showMessage('status','Updated Successfully');
		$('#input_name_edit').val('');
		getIngredientTree();
		//reload view
		if(text.type == 'ingredient')
			clickIngredient(event,$('#ingredient'+$('#active_id').val()+' span').get(0));
		else {
			getFoodGroupTree();
			clickFoodGroup(event,$('#foodGroupItem'+$('#active_id').val()+' span').get(0));
		}
	}
	else
		showMessage('status',text.message, 'error');
}

function deleteIngredient(){
	id = $('#active_id').val();
	parent_food_group_id = $('#active_parent_food_group_id').val();
	getIngredientDetailFormData();
	if(data != '') makeHttpRequestPOST(root+'/process_includes/ajax.php', 'delete_ingredient=1&type='
		+type+'&id='+id+'&csrf_token='+G_csrf_token, callback_deleteIngredient);
}

function callback_deleteIngredient(text){
	//console.log(text);
	text = parseJSON(text);
	if(text.success){
		showMessage('status',text.message);
		if(type == 'ingredient')
			$('div #ingredient'+id).hide();
		else if(type == 'food_group'){
			$('div #foodGroup'+id).parent().hide();
		}
		$('#ingredient_details_box,#viewableSection').addClass('hide');
	} else {
		showMessage('status',text.message, 'error');
	}
	if(text == false) return;
}

function getIngredientTree(){
	makeHttpRequestPOST(root+'/process_includes/ajax.php', 'get_ingredient_tree=1'
		+'&csrf_token='+G_csrf_token, callback_getIngredientTree);
}
function callback_getIngredientTree(text){
	$('#ingredients').html(text);
}

function getFoodGroupTree(){
	makeHttpRequestPOST(root+'/process_includes/ajax.php', 'get_food_group_tree=1'
		+'&csrf_token='+G_csrf_token, callback_getFoodGroupTree);
}
function callback_getFoodGroupTree(text){
	empty_food_group = 	'<div>' +
			                '<div class="foodGroup noChildren"><span class="caret-right" onclick="collapseFoodGroup(this, 0)"></span>' + 
			                	'<li id="foodGroupItem0" onclick="collapseFoodGroup(this, 0, \'sibling\')">None: Top Level Food Group</li>' +
			                '</div>' +
			                '<ul id="foodGroup0"></ul>' +
			            '</div>';
	$('#food_groups').html(empty_food_group);
    $('#food_groups').append(text);
}



function isFoodGroupDropdownActive(self){
	return $('#food_group_box').find(self).length > 0;
}
function collapseFoodGroup(self, id, menuType){
	//find out if this is the food group dropdown
	chosenFoodGroup = '';
	food_group_dropdown = isFoodGroupDropdownActive(self);
	food_group_dropdown_id = '';
	if(food_group_dropdown){
		food_group_dropdown_id = '#food_group_box ';
		chosenFoodGroup = $(food_group_dropdown_id+'#foodGroupItem'+id).text().replace('view/edit','');
		if($(food_group_dropdown_id+'.noChildren').find(self).length > 0){
			if($(self).prop('class').indexOf('caret') != -1){
				return;
			}
			$('#food_group_box div#food_groups li.active').removeClass('active');
			$(self).addClass('active');
			$('#input_food_group option').val(id);
			$('#input_food_group option').text(chosenFoodGroup);
			$('.food_group_dropdown').removeClass('active');
			$('#food_group_box').fadeOut(500);
			$('#covering').hide();
			return;
		}
	}
	if(menuType == 'sibling'){
		if(food_group_dropdown){
			$('#food_group_box div#food_groups li.active').removeClass('active');
			$(self).addClass('active');
			$('#input_food_group option').val(id);
			$('#input_food_group option').text(chosenFoodGroup);
			$('.food_group_dropdown').removeClass('active');
			$('#food_group_box').fadeOut(500);
			$('#covering').hide();
			return;
		}
		self = $(self).prev();
		theClass = $(self).attr('class');
	}
	else {
		theClass = $(self).attr('class');
	}

	if(theClass == 'caret'){
		//close it
		$(food_group_dropdown_id+'#foodGroupItem'+id).parent().parent().find('ul').hide();
		$(self).attr('class', 'caret-right');
	} else {
		//open it
		$(food_group_dropdown_id+'#foodGroupItem'+id).parent().parent().find('ul').show();
		$(food_group_dropdown_id+'#foodGroupItem'+id).parent().parent().find('ul ul').hide();
		$(food_group_dropdown_id+'#foodGroupItem'+id).parent().parent().find('.caret').attr('class','caret-right');
		$(self).attr('class', 'caret');
	}
}


