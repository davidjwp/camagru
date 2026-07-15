<?php
	session_start();
	require_once 'functs.php';

	check_session($_SESSION['user']);
	$pdo = new PDO(
		"mysql:host=model;dbname=camagru;charset=utf8",
		"camagru_admin",
		"camagru_admin_pass"
	);

	if (isset($_POST["disconnect"])) {
		session_destroy();

		header("location: /index.php");
		exit;
	}

	$user = $_SESSION["user"];

	$change = ['',''];
	if (!empty($_POST["username"])) {
		if (strlen($_POST['username']) < 5 || strlen($_POST['username']) > 20)
			alert("username at least 5 char long and no longer than 20 chars");
		$change[0] = 'username';
	}
	if (!empty($_POST["email"])) {
		if (!filter_var($_POST['email'], FILTER_VALIDATE_EMAIL))
    		alert("Invalid email address");
		$change[1] = 'email';
	}

	foreach ($change as $ch) {
		if (!empty($ch)) {
			$stmt = $pdo->prepare("UPDATE users SET $ch = :$ch WHERE id = :id");
			$stmt->execute([":$ch"=> $_POST[$ch], ':id'=>$user['id']]);
			$_SESSION['user'][$ch] = $_POST[$ch];
		}	
	}
	
	// include("profile.html");

	if (isset($_POST['reset_password']) || 
	!empty($_POST["password1"]) ||
	!empty($_POST["password2"])) {
		echo "<br><form action='profile.php' method='post'> 
			<input type='password' name='password1' placeholder='new password'><br>
			<input type='password' name='password2' placeholder='confirm new password'><br>
			<input type='submit' name='submit' value='send'>
		</form>";
	}

	if (!empty($_POST["password1"]) && !empty($_POST["password2"])) {
		if (strlen($_POST['password1']) < 5 || strlen($_POST['password1']) > 20 ||
		!preg_match('/[!@#$%^&*(){}\-_=+?\/.>,<;:]/', $_POST['password1']) ||
		!preg_match('/[A-Z]/', $_POST['password1']))
			alert("password must contain at least one special char and one upper case");
		else if ($_POST['password1'] !== $_POST['password2'])
			alert("passwords don't match");

		$stmt = $pdo->prepare("UPDATE users SET password = :password WHERE id = :id");
		$stmt->execute([
			':password' => password_hash($_POST['password1'], PASSWORD_DEFAULT), 
			':id' => $user['id']
			]);
	}
	$doc = new DOMDocument();
	$doc->loadHTMLFile('profile.html');
	
	// var_dump($_SESSION);
	$doc->getElementById('welcome_header')->nodeValue = "Welcome ". $_SESSION['user']['username'];
	$doc->getElementById('email_info')->nodeValue = $_SESSION['user']['email'];
	echo $doc->saveHTML();
	// header('location: index.php');
	exit;
