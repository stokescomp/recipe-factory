<?php 
$title = "Forgot Password";
$page = "forgot-password";
$header_message = "You forgot your password?";
//if its the first time we only ask them to enter their email else we ask for their new password
if(isset($action) && $action != ''){
	$forgot_password_id = $action;
	$first_time = false;
} else {
	$first_time = true;
	$forgot_password_id = '';
}
require('../includes/init.php');
require($_SERVER['DOCUMENT_ROOT'].'/page_includes/header.php');
?>
	<section id="main">
		<?php
		if(isset($_SESSION['message'])){
			echo '<section id="message_box"><ul class="message">';
			foreach($_SESSION['message'] as $each_message){
				echo "<li>$each_message</li>";
			}
			echo "</ul></section>";
			unset($_SESSION['message']);
		}?>
		<section id="register">
		<?php if($first_time){ ?>
			<p>To test getting an id in your email click the link. Here is your forgot password <a href="forgot-password/3df7d8d6s5s">link</a>.</p>
			<p>Enter your email and we shall send you a reset link in your email</p><br />
			<form action="" method="post">
				<label for="email">Email: </label><input type="text" name="email" value="<?php if(isset($_POST['email'])) echo $_POST['email']?>" id="email" /><br />
				<input type="hidden" name="csrf_token" id="csrf_token" value="<?php echo $csrf_token?>" />
				<input type="submit" value="Submit" name="submit_register" class="button-primary" />
			</form>
		<?php } else if($forgot_password_id != ''){ ?>
			<p>Enter your New password</p><br />
			<form action="" method="post">
				<label for="password">Password: </label><input type="password" name="password" id="password" /><br />
				<label for="password2">Confirm Password: </label><input type="password" name="password2" id="password2" /><br />
				<input type="hidden" name="csrf_token" id="csrf_token" value="<?php echo $csrf_token?>" />
				<input type="submit" value="Submit" name="submit_register" class="button-primary" />
			</form>
		<?php } ?>
		</section>
	</section>
<?php require($_SERVER['DOCUMENT_ROOT'].'/page_includes/footer.php');