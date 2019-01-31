<?php
// echo "<pre>";
// print_r($_SERVER);exit;
if(!isset($_SESSION)) session_start();//echo "<pre>".print_r($_SESSION,1).print_r($_SERVER,1)."</pre>";
$live_refresh = false;
if(isset($title)) 
	$title = $title . ' - The Recipe Factory';
else
	$title = 'The Recipe Factory';
deleteExpiredTokens();
$csrf_token = setToken();
require $_SERVER['DOCUMENT_ROOT'].'/process_includes/login.php';
?><!DOCTYPE html>
<html>
<head>
	<meta charset="UTF-8">
	<meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no">
	<title><?php echo $title?></title>
	<link rel="icon" type="image/png" href="<?php echo $root?>favicon.png" />
	<!-- <link rel='stylesheet' type='text/css' href='/css/bootstrap-default.css' /> -->
	<link rel='stylesheet' type='text/css' href='<?php echo $root?>css/bootstrap.css' />
	<!-- <link rel='stylesheet' type='text/css' href='/css/bootstrap-theme.css' /> -->
	<link rel='stylesheet' type='text/css' href='<?php echo $root?>css/menu.css' />
	<link rel='stylesheet' type='text/css' href='<?php echo $root?>css/main.css' />
	<link rel='stylesheet' type='text/css' href='<?php echo $root?>css/style.css' />
	<script src='<?php echo $root?>js/jquery.min.js'></script>
	<script src='<?php echo $root?>js/ajax.js'></script>
	<script src='<?php echo $root?>js/bootstrap.js'></script>
	<script src='<?php echo $root?>js/functions.js'></script>
	<script>
	<?php require $_SERVER['DOCUMENT_ROOT'].'/process_includes/embeded_js.php';?>
	</script>
</head>
<body class="<?php echo $page;?>">
	<header>
		<div class="navbar navbar-default navbar-fixed-top">
      		<div class="container">
        		<div class="navbar-header">
					<a href="<?php echo $root?>" class="navbar-brand">Recipe Factory</a>
					<button class="navbar-toggle" type="button" data-toggle="collapse" data-target="#navbar-main">
						<span class="icon-bar"></span>
						<span class="icon-bar"></span>
						<span class="icon-bar"></span>
					</button>
		        </div>
		        <div class="navbar-collapse collapse" id="navbar-main">
					<ul class="nav navbar-nav">
			          	<li class="<?php if($page == 'home') echo 'active'?>">
			              <a href="<?php echo $root?>">Home</a>
			            </li>
			            <li class="<?php if($page == 'about') echo 'active'?>">
			              <a href="<?php echo $root?>about">About</a>
			            </li>
			            <li class="<?php if($page == 'feedback') echo 'active'?>">
			              <a href="<?php echo $root?>feedback">Feedback</a>
			            </li>
			            <li class="<?php if($page == 'my_account') echo 'active'?>">
			              <a href="<?php echo $root?>my_account">My Account</a>
			            </li>
			            <li class="dropdown <?php if($page == 'recipe_manager') echo 'active'?>">
							<a class="dropdown-toggle" data-toggle="dropdown" href="#" id="recipe_manager">Recipe Manager <span class="caret"></span></a>
							<ul class="dropdown-menu" aria-labelledby="recipe_manager">
								<li><a tabindex="-1" href="<?php echo $root?>recipe">Create New recipe</a></li>
								<li><a tabindex="-1" href="<?php echo $root?>manage_recipes">List My recipes</a></li>
								<li><a tabindex="-1" href="<?php echo $root?>ingredients">View Ingredients</a></li>
							</ul>
			            </li>
			            <li class="dropdown <?php if($page == 'manage') echo 'active'?>">
			              <a class="dropdown-toggle" data-toggle="dropdown" href="#" id="manage">Manage <span class="caret"></span></a>
			              <ul class="dropdown-menu" aria-labelledby="manage">
			                <li><a tabindex="-1" href="<?php echo $root?>manage_recipes">Manage Recipes</a></li>
			                <li class="divider"></li>
			                <li><a tabindex="-1" href="<?php echo $root?>manage_users">Manage Users</a></li>
			                <li><a tabindex="-1" href="<?php echo $root?>edit_users">Edit Users</a></li>
			                <li class="divider"></li>
			                <li><a tabindex="-1" href="<?php echo $root?>manage_categories">Manage Categories</a></li>
			                <li><a tabindex="-1" href="<?php echo $root?>edit_categories">Edit Categories</a></li>
			              </ul>
			            </li>
			            <li class="dropdown <?php if($page == 'admin') echo 'active'?>">
			              <a class="dropdown-toggle" data-toggle="dropdown" href="#" id="admin">Admin <span class="caret"></span></a>
			              <ul class="dropdown-menu" aria-labelledby="admin">
			                <li><a tabindex="-1" href="<?php echo $root?>edit_ingredients">Edit Ingredents</a></li>
			                <li><a tabindex="-1" href="<?php echo $root?>edit_food_groups">Edit Food Groups</a></li>
			                <li><a tabindex="-1" href="<?php echo $root?>edit_measurements">Edit Measurements</a></li>
			                <li><a tabindex="-1" href="<?php echo $root?>edit_serving_types">Edit Serving Types</a></li>
			                <li><a tabindex="-1" href="<?php echo $root?>edit_substitutes">Edit Substitutes</a></li>
			                <li><a tabindex="-1" href="<?php echo $root?>edit_meal_types">Edit Meal Types</a></li>
			                <li><a tabindex="-1" href="<?php echo $root?>edit_user_groups">Edit User Groups</a></li>
			                <li><a tabindex="-1" href="<?php echo $root?>undo_delete">Undo Delete</a></li>
			              </ul>
			            </li>
					</ul>

					<div class="navbar navbar-nav navbar-left">

						<div id="login">
							<form action="<?php echo $_SERVER['REQUEST_URI']?>" method="POST" role="form">
								<div class="form-group">
									<div class="row">
								<?php
									if(isset($_SESSION['user_id'])){ ?>											
										<div class="col-sm-3">
											<span><a href="<?php echo $root?>my_account"><span class="icon-mail glyphicon glyphicon-comment"></span><span class="notification-text"> 1 </span></a></span>
											<span><a href="#" onclick="alert('show notifications')"><span class="icon-notifications glyphicon glyphicon-bell"></span><span class="notification-text"> 3 </span></a></span>
										</div>
										<div class="col-sm-2">
											<a href='<?php echo $root?>logout'>Logoff</a>
										</div>
										<div>
											<?php 
												echo 'You are logged in as ' . $_SESSION['name'];
											?>
										</div>
									<?php
									} else { ?>
										<div class="col-sm-3">
											<input type="text" class="form-control" name="login-username" id="login-username" placeholder="Username or Email" />
										</div>
										<div class="col-sm-3">
											<input type="password" class="form-control" name="login-password" id="login-password" placeholder="Enter Password" />
											<span class="input-group-btn">
												<input type="hidden" name="csrf_token" id="csrf_token" value="<?php echo $csrf_token?>" />
												<input type="submit" class="btn btn-default" name="submit_login" value="Login" />
											</span>
										</div>
										<div class="col-sm-3">
											<div id="login-link">
												<div><a href="<?php echo $root?>forgot-password">Forgot Password</a></div>
												<div><a href="<?php echo $root?>register">Register Account</a></div>
											</div>
										</div>
										<?php } //end login block?>
									</div>
								</div>
							</form>
						</div>
					</div>
		        </div>
			</div>
	    </div>
	</header>
	<br class="clear">
	<?php
	if(isset($header_message)) echo "<h1>$header_message</h1>";
	if($error) $error_status = 'show'; else $error_status = 'hide';
	echo '<section class="error">
			<div id="errors" class="'.$error_status.' alert alert-dismissable alert-danger">
          		<button type="button" class="close" data-dismiss="alert">x</button>
          		<strong>Oh snap!</strong>
        		<ul id="errors_message">';
	if($error){
		foreach($error as $each_error){
			echo "<li>$each_error</li>";
		}
	}
	echo "</ul></div></section>";
	if(isset($_SESSION['message'])){
		echo '<section id="message_box" class="success">
				<div class="alert alert-dismissable alert-success">
              		<button type="button" class="close" data-dismiss="alert">×</button>
              		<strong>For your infomation</strong>
            		<ul>';
		foreach($_SESSION['message'] as $each_message){
			echo "<li>$each_message</li>";
		}
		echo "</ul></div></section>";

		
		unset($_SESSION['message']);
	}
	?>