<?php
	require_once 'functs.php';
	session_start();

	if (!isset($_SESSION['user'])) {
		header('location: /index.php');
		exit;
	}

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

	/*check username or email then change them*/
	$change = ['',''];
	if (!empty($_POST["username"])) {
		if (strlen($_POST['username']) < 5 || strlen($_POST['username']) > 20) exit(
				"<script>alert('username must be between 5 and 20 chars');
				window.location.href='/profile.php';</script>"
			);
		$change[0] = 'username';
	}
	if (!empty($_POST["email"])) {
		if (!filter_var($_POST['email'], FILTER_VALIDATE_EMAIL)) exit(
			"<script>alert('Error: "."Invalid email address"."');
			window.location.href='/profile.php';</script>");
			header('location: /profile.php');
		$change[1] = 'email';
	}

	foreach ($change as $ch) {
		if (!empty($ch)) {
			$stmt = $pdo->prepare("UPDATE users SET $ch = :$ch WHERE id = :id");
			$stmt->execute([":$ch"=> $_POST[$ch], ':id'=>$user['id']]);
			$_SESSION['user'][$ch] = $_POST[$ch];
		}	
	}

	/*append password reset html*/
	$doc = new DOMDocument();
	$doc->loadHTMLFile('profile.html');

	if (isset($_POST['reset_password']) || !empty($_POST["password1"]) || !empty($_POST["password2"])) AppendPasswordReset($doc);

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
	
	$stmt = $pdo->prepare("SELECT notification FROM users WHERE id = :id");
	$stmt->execute([":id"=>$_SESSION['user']['id']]);
	$notification = $stmt->fetch()['notification'] ? 1: 0;

	$target = $doc->getElementById('checkbox');
	$checkbox = $doc->createElement('input');
	$checkbox->setAttribute('type', 'checkbox');
	if ($notification) $checkbox->removeAttribute('checked'); 
	else $checkbox->setAttribute('checked', 'checked');

	$target->appendChild($checkbox);
	$data = json_decode(file_get_contents('php://input'), true);

	$doc->getElementById('welcome_header')->nodeValue = "Welcome ". $user['username'];
	$doc->getElementById('email_info')->nodeValue = $user['email'];
	echo $doc->saveHTML();
	if (isset($data['checked'])) {
		$notification = $data['notification'] ? 0 : 1;
		error_log("NOTIFICATION PRESENT ". $notification . " ". $user['id']);
		$stmt = $pdo->prepare("UPDATE users SET notification = :notification WHERE id = :id");
		$stmt->execute([":notification"=>$notification, ":id"=>$user['id']]);
	}

	exit;
