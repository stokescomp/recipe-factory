<?php
$title = "Edit Meal Types";
$page = "edit_meal_types";
$header_message = "Edit Meal Types for The Recipe Factory";
require('../includes/init.php');
require($_SERVER['DOCUMENT_ROOT'].'/page_includes/header.php');
?>
	<section id="main" class="clear">
		<section id="register">
			<form action="" method="post" role="form">
				<div class="form-group">
					<div class="row">
						<div class="col-xs-6">
							<label for="first_name">First name: </label><input type="text" class="form-control" name="first_name" id="first_name"  value="<?php if(isset($_POST['first_name'])) echo $_POST['first_name']?>" placeholder="Enter First name" />
						</div>
						<div class="col-xs-6">
							<label for="last_name">Last name: </label><input type="text" class="form-control" name="last_name" id="last_name" value="<?php if(isset($_POST['last_name'])) echo $_POST['last_name']?>" placeholder="Enter Last name" />
						</div>
					</div>
				</div>

				<div class="form-group">
					<div class="row">
						<div class="col-xs-6">
							<label for="email">Email: </label><input type="email" class="form-control" name="email" id="email" value="<?php if(isset($_POST['email'])) echo $_POST['email']?>" placeholder="Enter Email" />
						</div>
						<div class="col-xs-6">
							<label for="username">Username: </label><input type="text" class="form-control" name="username" id="username" value="<?php if(isset($_POST['username'])) echo $_POST['username']?>" placeholder="Enter Username" />
						</div>
					</div>
					<!-- echo formElement('input', 'email','3', true, 'Placeholder text');
					echo formElement('input', 'username','3', true, 'Placeholder text'); -->
				</div>

				<div class="form-group">
					<div class="row">
						<div class="col-xs-4">
							<label for="street_address">Street: </label><input type="text" class="form-control" name="street_address" id="street_address" value="<?php if(isset($_POST['street_address'])) echo $_POST['street_address']?>" placeholder="Enter Street Address" />
						</div>
						<div class="col-xs-4">
							<label for="city">City: </label><input type="text" class="form-control" name="city" id="city" value="<?php if(isset($_POST['city'])) echo $_POST['city']?>" placeholder="Enter City" />
						</div>
						<div class="col-xs-2">
							<label for="state">State: </label><input type="text" class="form-control" name="state" id="state" value="<?php if(isset($_POST['state'])) echo $_POST['state']?>" placeholder="Enter State" />
						</div>
						<div class="col-xs-2">
							<label for="zip">Zip: </label><input type="text" class="form-control" name="zip" id="zip" value="<?php if(isset($_POST['zip'])) echo $_POST['zip']?>" placeholder="Enter Zip" />
						</div>
					</div>
				</div>
				
				<div class="form-group">
					<div class="row">
						<div class="col-xs-6">
							<label for="password">Password: </label><input type="password" class="form-control" name="password" id="password" placeholder="Enter Password" />
						</div>
						<div class="col-xs-6">
							<label for="password2">Confirm: </label><input type="password" class="form-control" name="password2" id="password2" placeholder="Confirm Password" />
						</div>
					</div>
				</div>

				<div class="form-group">
					<div class="row">
						<div class="well col-xs-12"><b>Agreement</b><br />
							Please abide to the rules in this text. Please abide to the rules in this text. Please abide to the rules in this text. Please abide to the rules in this text. Please abide to the rules in this text. Please abide to the rules in this text. Please abide to the rules in this text. Please abide to the rules in this text. Please abide to the rules in this text. Please abide to the rules in this text. 
						</div>
					</div>
				</div>

				<div class="form-group">
					<div class="row">
						<div class="col-xs-2">
							<label for="agree">Agree to terms: </label><input type="checkbox" name="agree" id="agree" />
						</div>
					</div>
				</div>
				
				<div class="form-group">
					<input type="submit" class="btn btn-default" name="submit_register" value="Register" />
					<input type="hidden" name="csrf_token" id="csrf_token" value="<?php echo $csrf_token?>" />
				</div>
			</form>
		</section>
	</section>
<?php require($_SERVER['DOCUMENT_ROOT'].'/page_includes/footer.php');