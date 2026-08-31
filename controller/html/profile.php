<?php
	require_once 'functs.php';
	session_start();
	session_regenerate_id(true);
	
	if (empty($_POST['csrf-token'])) {
		$_SESSION['csrf-token'] = bin2hex(random_bytes(32));
	}
		
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

	$doc = new DOMDocument();
	$doc->loadHTMLFile('profile.html');

	if (empty($_POST['csrf-token'])) {
		$_SESSION['csrf-token'] = bin2hex(random_bytes(32));

		$doc->getElementById('csrf-token1')->setAttribute('value', $_SESSION['csrf-token']);
		$doc->getElementById('csrf-token2')->setAttribute('value', $_SESSION['csrf-token']);
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
			window.location.href='/profile.php';</script>"
			);
		$change[1] = 'email';
	}

	if (isset($_POST['csrf-token']) && $_POST['csrf-token'] === $_SESSION['csrf-token']) {
		error_log('DEBUG HERE ');
		foreach ($change as $ch) {
			if (!empty($ch)) {
				$stmt = $pdo->prepare("UPDATE users SET $ch = :$ch WHERE id = :id");
				$stmt->execute([":$ch"=> $_POST[$ch], ':id'=>$user['id']]);
				$_SESSION['user'][$ch] = $_POST[$ch];
			}	
		}
	}
	
	if (!empty($_POST["password1"]) && !empty($_POST["password2"])) {
		if (strlen($_POST['password1']) < 5 || strlen($_POST['password1']) > 20 ||
		!preg_match('/[!@#$%^&*(){}\-_=+?\/.>,<;:]/', $_POST['password1']) ||
		!preg_match('/[A-Z]/', $_POST['password1']))
		alert("password must contain at least one special char and one upper case");
		else if ($_POST['password1'] !== $_POST['password2']) {
			$alert = "<script>alert('password don't match');</script>";
		}
		else {
			$stmt = $pdo->prepare("UPDATE users SET password = :password WHERE id = :id");
			$stmt->execute([
				':password' => password_hash($_POST['password1'], PASSWORD_DEFAULT), 
				':id' => $user['id']
				]);
		}
	}


	
	//fetch notification status and add input element
	$doc->getElementById('welcome_header')->nodeValue = "Welcome ". $user['username'];
	$doc->getElementById('email_display')->nodeValue = $user['email'];

	$stmt = $pdo->prepare("SELECT notification FROM users WHERE id = :id");
	$stmt->execute([":id"=>$_SESSION['user']['id']]);
	$notification = $stmt->fetch()['notification'] ? 1: 0;

	$target = $doc->getElementById('toggle');

	$toggle = $doc->createElement('input');
	$toggle->setAttribute('type', 'checkbox');
	$toggle->setAttribute('id', 'notif-toggle');
	if (!$notification) $toggle->removeAttribute('checked'); 
	else $toggle->setAttribute('checked', 'checked');

	$span = $doc->createElement('span');
	$span->setAttribute('class', 'slider');

	$target->appendChild($toggle);
	$target->appendChild($span);

	//get json from toggle POST fetch and update notification accordingly
	$data = json_decode(file_get_contents('php://input'), true);
	if (isset($data['checked'])) {
		$notification = $data['notification'] ? 1 : 0;
		$stmt = $pdo->prepare("UPDATE users SET notification = :notification WHERE id = :id");
		$stmt->execute([":notification"=>$notification, ":id"=>$user['id']]);
		echo json_encode(['success'=>true]);
		exit ;
	}

	echo $doc->saveHTML();
	if (isset($alert)) {
		echo $alert;
	}
	exit;
