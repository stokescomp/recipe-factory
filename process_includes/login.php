<?php
if(isset($_POST['submit_login'])){
	$error = checkCSRFToken($_POST['csrf_token'], $error);
	if(!$error){
		//check if email exists
		if(empty($_POST['login-username'])) $error[] = 'Enter your email or username';
		if(empty($_POST['login-password'])) $error[] = 'Enter your password';
	}
	if(!$error){
		require_once($_SERVER['DOCUMENT_ROOT'].'/includes/db.php');
		//echo SHA1("RandomCharactersBeforePassword".'a17fed27eaa842282862ff7c1b9c8395a26ac320'.';lkj fdsa ;lkj<.,9#%[}0'."AfterSaltRandomCharacters");
		$password = hashPassword($_POST['login-password']);
		$sql = "SELECT count(1) as count, user_id, first_name, last_name FROM {$prepend_table}user WHERE (username = ? OR email = ?) AND password = SHA1(CONCAT('RandomCharactersBeforePassword',?,salt,'AfterSaltRandomCharacters')) LIMIT 1";
		foreach($db->Fetch($sql, 'sss', $_POST['login-username'], $_POST['login-username'], $password) as $row){
			if($row['count'] == 0){
				$error[] = "You entered the wrong <i>email / username</i> or password."; 
				break;
			}
			deleteToken($_POST['csrf_token']);
			$_SESSION['user_id'] = $row['user_id'];
			$name = $row['first_name'].' '.$row['last_name'];
			$_SESSION['name'] = $name;
			$_SESSION['message'][] = "$name you have logged in!";
			header('Location: '.$_SERVER['REQUEST_URI']);
		}
	}
}