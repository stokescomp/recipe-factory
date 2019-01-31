<?php 
error_reporting(E_ALL);
if(isset($_POST['submit_register'])){
	$error = checkCSRFToken($_POST['csrf_token'], $error);
	if(!$error){
		if(empty($_POST['first_name']) && empty($_POST['last_name'])){
			$error[] = 'Enter your name.';
		} else if(empty($_POST['first_name'])){
			$error[] = 'Enter your first name.';
		} else if(empty($_POST['last_name'])){
			$error[] = 'Enter your last name.';
		}

		if(empty($_POST['email']) && empty($_POST['username'])){
			$error[] = 'Enter your email and username.';
		} else if(empty($_POST['email'])){
			$error[] = 'Enter your email.';
		} else {
			//check if email is taken
			include $_SERVER['DOCUMENT_ROOT'].'/includes/db.php';
			$sql = "SELECT count(1) FROM {$prepend_table}user WHERE email = ? OR username = ?";
			if($db->FetchValue($sql, 'ss', $_POST['email'], $_POST['username']) > 0){
				$error[] = 'This email is already taken.';
			} else if(empty($_POST['username'])){
				$error[] = 'Enter your username.';
			}
		}
		if(empty($_POST['city'])) $error[] = 'Enter your City.';
		if(empty($_POST['state'])) $error[] = 'Enter your State.';
		if(empty($_POST['zip'])) $error[] = 'Enter your Zip.';
		//agree to terms
		if(empty($_POST['password'])){
			$error[] = 'Enter your password';
		} else if(strlen($_POST['password']) < 8){
			$error[] = 'Password must be at least 8 characters long.';
		} else if(empty($_POST['password2'])){
			$error[] = 'Enter the confirmation password.';
		} else if($_POST['password'] != $_POST['password2']){
			$error[] = 'The confirmation password should be the same.';
		}
		if(empty($_POST['agree'])) $error[] = 'Agree to the terms.';
	}
	if(!$error){
		require $_SERVER['DOCUMENT_ROOT'].'/includes/db.php';
		$name = ucwords($_POST['first_name'].' '.$_POST['last_name']);
		list($password, $salt) = hashPassword($_POST['password'], true);
		//exit($password.' : '.$salt);
		//echo "raw password: {$_POST['password']}<br />";
		//echo 'raw string being sha1ed: RandomCharactersBeforePassword'.sha1($_POST['password']).$salt.'AfterSaltRandomCharacters<br />';
		//echo "after sha1: $password<br />";
		$user_id = $db->ExecuteSQL("INSERT INTO {$prepend_table}user (first_name, last_name, username, email, password, salt, street_address, city, state_id, zip) VALUES 
			(?, ?, ?, ?, ?, ?, ?, ?, ?, ?)", 'ssssssssss', 
			$_POST['first_name'], $_POST['last_name'], $_POST['username'], $_POST['email'], $password, $salt, $_POST['street_address'], $_POST['city'], $_POST['state'], $_POST['zip']); 
		if($user_id == ''){
			$error[] = "$name, There was an error registering your user";
		} else {
			deleteToken($_POST['csrf_token']);
			$_SESSION['message'][] = "$name, your user was created successfully!<br />Try logging in";
			header('Location: /index.php');
			exit();
		}
	}
}