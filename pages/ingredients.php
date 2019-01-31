<?php 
/*
notes. make squares around the arrows. Make arrows pointing down you click to jump to the ingredients section or to the ingredient details section. use anchors.
Make the ingredients row twice as tall for ipads. For normal size make it grow taller slowly when you hover over.
Make foodgroups come first then ingredients.
Make button food groups that says "Add Ingredient". It will pull up a list of food groups and ingredients. 
	There will be check boxes next to ingredients. You can check more than one. There won't be any checkboxes 
	next to ingredients in the current food group. It will make a copy of these ingredients in this food group.
	In an ingredient there will be a button that says: "add to" and it lists all food groups and you can check 
	multiple boxes except not the food group you are in now.
Make a button in the food groups right pane that says "Add Ingredient". It will open new ingredient box and auto fill in the in food group id.
Make a button in the food groups right pane that says "Add Food Group". It will open new food group box and auto fill in the in food group id.
Keep the order of the ingredients when it refreshes. Make an array of food groups that are open and then reopen 
	those in php by making the class open them. Pass the function a list of groups to open.
Make an icon appear next to the active menu item so you know what you have just clicked on.
Make it check if there was changes every 30 seconds. Keep the food groups open that you had open when it found new ones.
When clicking on add ingredient or food group put cursor in the name textbox.
After the message appears make it disappear after 2 seconds or as soon as you click add new again.
Why does each new ingredient skip 3 ids?
make spining animation when you click add new so you know something is happening. Also same for when you click on ingredient or food group.
make column in db for is_disabled and make the default 0.
*/
$title = "View Ingredients";
$page = "view_ingredients";
$header_message = "View Ingredients";
require('../includes/init.php');
require($_SERVER['DOCUMENT_ROOT'].'/page_includes/header.php');
if(checkLoggedIn('normal') == false) exit();
?>
<script>
onload=function(){
	getFoodGroupTree();
	getIngredientTree();
};

function startIngredientPage(){
	getFoodGroupTree();
	getIngredientTree();
}

function clickFoodGroupDropdown(self){
	//make the current dropdown have an active class
	if($(self).hasClass('active')){
		$('#covering').hide();
		$('#food_group_box').hide();
		$(self).removeClass('active');
	} else {
		$('#covering').show();
		//remove all the active classes and then readd it to the current element then move the ingredient box under the current dropdown
		$('.food_group_dropdown').removeClass('active');
		$(self).addClass('active');
		$('#food_group_box').css({
			top: $(self).offset().top + $(self).height() + 18 + 'px',
			left: $(self).offset().left + 'px'
		});
		//no need to store the dropdown id since there is only one on the page
		// G_current_ingredient_dropdown = $(self).prop('id').replace('food_group_name_','');
		//remove active ingredients
		$('#food_group div.food_groups').removeClass('active');
		//get current ingredient and make it the active ingredient
		current_id = $('#input_food_group').val();
		if(current_id != ''){
			$('#food_group_box li.active').removeClass('active');
			$('#food_group_box #foodGroupItem'+current_id).addClass('active');
			//close all food groups
			//open the food groups to the active ingredient
		}
		$('#food_group_box').show();
	}
}

function hideCovering(){
	$('#covering').hide();
	$('#food_group_box').hide();
	$('#input_food_group').removeClass('active'); 
}
</script>
<style>
#food_group_box{
	display: none;
	position: absolute;
	background-color: #FFF;
}
#food_group_box #food_groups{
	min-height: 200px;
}
#food_group_box .btn{
	display: none;
}
</style>
<article id="main">
	<a class="btn btn-default" onclick="clickAddIngredient()">Add Ingredient</a>
	<a class="btn btn-default" href="#" onclick="clickAddFoodGroup()">Add Food Group</a>
	<a class="btn btn-default" href="#" onclick="alert('This will let you send a message to the admins requesting a new ingredient.')">Request Ingredient</a>
	<br class="clear"/><br />
	<section>
	<div id="ingredient_box" class="well">
		<h2 onclick="clickAddIngredient()">Current Ingredients</h2>
		<div id="ingredients">

<?php //using ajax function: getIngredientTree() to call after the page loads.
//display_ingredient_tree($tree); ?>

		</div><!-- end Ingredients -->
	</div>

	<div id="ingredient_details_box" class="hide well">
		<h2 id="ingredient_details_title">Ingredient</h2>
		<div id="status" class="hide alert alert-success">
            <p id="status_message"></p>
        </div>
		<input type="hidden" id="active_id" value="" />
		<input type="hidden" id="active_parent_food_group_id" value="" />
		<input type="hidden" id="active_type" value="ingredient" />
		<div id="viewableSection">
			<a class="btn btn-info" href="#" onclick="toggleEditIngredient();hideMessage('status')">Edit</a> 
			<a class="btn btn-info" href="#" onclick="deleteIngredient()">Delete</a>
			<div id="ingredient_details_view"></div>
		</div>
		<div id="editableSection" class="hide">
			<a class="btn btn-info" href="#" onclick="toggleEditIngredient();hideMessage('status')">View</a> 
			<a class="btn btn-info" href="#" onclick="deleteIngredient()">Delete</a>
			<div id="ingredient_details_edit"></div>
		</div>
		<div id="addableSection" class="hide">
			<div id="ingredient_details_add"></div>
		</div>
	</div>
	<br class="clear" /><br/><br/><br/><br/><br/><br/>
	</section>
	<div id="food_group_box" class="dropdown">
		<div id="food_groups"></div>
	</div>
	<ul id="tree3" class="tree">
	    <li class="branch"><i class="indicator glyphicon glyphicon-chevron-right"></i><a href="#">TECH</a>
	        <ul>
	            <li style="display: list-item;">Company Maintenance</li>
	            <li class="branch" style="display: list-item;"><i class="indicator glyphicon glyphicon-chevron-right"></i>Employees
	                <ul>
	                    <li class="branch" style="display: list-item;"><i class="indicator glyphicon glyphicon-chevron-right"></i>Reports
	                        <ul>
	                            <li style="display: list-item;">Report1</li>
	                            <li style="display: list-item;">Report2</li>
	                            <li style="display: list-item;">Report3</li>
	                        </ul>
	                    </li>
	                    <li style="display: list-item;">Employee Maint.</li>
	                </ul>
	            </li>
	            <li style="display: list-item;">Human Resources</li>
	        </ul>
	    </li>
	    <li class="branch"><i class="indicator glyphicon glyphicon-chevron-down"></i>XRP
	        <ul>
	            <li style="display: none;">Company Maintenance</li>
	            <li class="branch" style="display: none;"><i class="indicator glyphicon glyphicon-chevron-down"></i>Employees
	                <ul>
	                    <li class="branch" style="display: none;"><i class="indicator glyphicon glyphicon-chevron-down"></i>Reports
	                        <ul>
	                            <li style="display: none;">Report1</li>
	                            <li style="display: none;">Report2</li>
	                            <li style="display: none;">Report3</li>
	                        </ul>
	                    </li>
	                    <li style="display: none;">Employee Maint.</li>
	                </ul>
	            </li>
	            <li style="display: none;">Human Resources</li>
	        </ul>
	    </li>
	</ul>
</article>
<script>
$.fn.extend({
    treed: function (o) {
      
      var openedClass = 'glyphicon-minus-sign';
      var closedClass = 'glyphicon-plus-sign';
      
      if (typeof o != 'undefined'){
        if (typeof o.openedClass != 'undefined'){
        openedClass = o.openedClass;
        }
        if (typeof o.closedClass != 'undefined'){
        closedClass = o.closedClass;
        }
      };
      
        //initialize each of the top levels
        var tree = $(this);
        tree.addClass("tree");
        tree.find('li').has("ul").each(function () {
            var branch = $(this); //li with children ul
            branch.prepend("<i class='indicator glyphicon " + closedClass + "'></i>");
            branch.addClass('branch');
            branch.on('click', function (e) {
                if (this == e.target) {
                    var icon = $(this).children('i:first');
                    icon.toggleClass(openedClass + " " + closedClass);
                    $(this).children().children().toggle();
                }
            })
            branch.children().children().toggle();
        });
        //fire event from the dynamically added icon
      tree.find('.branch .indicator').each(function(){
        $(this).on('click', function () {
            $(this).closest('li').click();
        });
      });
        //fire event to open branch if the li contains an anchor instead of text
        tree.find('.branch>a').each(function () {
            $(this).on('click', function (e) {
                $(this).closest('li').click();
                e.preventDefault();
            });
        });
        //fire event to open branch if the li contains a button instead of text
        tree.find('.branch>button').each(function () {
            $(this).on('click', function (e) {
                $(this).closest('li').click();
                e.preventDefault();
            });
        });
    }
});

//Initialization of treeviews
$('#tree3').treed();
</script>
<style>
.tree, .tree ul {
    margin:0;
    padding:0;
    list-style:none
}
.tree ul {
    margin-left:1em;
    position:relative
}
.tree ul ul {
    margin-left:.5em
}
.tree ul:before {
    content:"";
    display:block;
    width:0;
    position:absolute;
    top:0;
    bottom:0;
    left:0;
    border-left:1px solid
}
.tree li {
    margin:0;
    padding:0 1em;
    line-height:2em;
    color:#369;
    font-weight:700;
    position:relative
}
.tree ul li:before {
    content:"";
    display:block;
    width:10px;
    height:0;
    border-top:1px solid;
    margin-top:-1px;
    position:absolute;
    top:1em;
    left:0
}
.tree ul li:last-child:before {
    background:#fff;
    height:auto;
    top:1em;
    bottom:0
}
.indicator {
    margin-right:5px;
}
.tree li a {
    text-decoration: none;
    color:#369;
}
.tree li button, .tree li button:active, .tree li button:focus {
    text-decoration: none;
    color:#369;
    border:none;
    background:transparent;
    margin:0px 0px 0px 0px;
    padding:0px 0px 0px 0px;
    outline: 0;
}
</style>
<div id="covering" onclick="hideCovering()"></div>
<script src='<?php echo $root?>js/functions_view_ingredients.js?t=<?php echo filemtime('../js/functions_view_ingredients.js')?>'></script>
<?php require($_SERVER['DOCUMENT_ROOT'].'/page_includes/footer.php');