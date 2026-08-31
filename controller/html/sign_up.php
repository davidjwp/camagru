<?php
	session_start();
	require_once 'functs.php';
	$alert;

	if (empty($_POST['csrf-token'])) {
		$_SESSION['csrf-token'] = bin2hex(random_bytes(32));
	}

	if (!empty($_POST) && isset($_POST['csrf-token']) && 
	$_SESSION['csrf-token'] === $_POST['csrf-token']) {
		$ver = 0;
		if (!empty($_POST['username'])) $ver |= 1;
		if (!empty($_POST['password'])) $ver |= 2;
		if (!empty($_POST['email'])) $ver |= 4;

		if ($ver != 7) {
			include "sign_up.html";
			alert("missing Username, password or email"); 
			exit ;
		}

		if (strlen($_POST['username']) < 5 || strlen($_POST['username']) > 20) {	
			include "sign_up.html";
			alert("username at least 5 char long and no longer than 20 chars");
			exit ;
		}

		if (strlen($_POST['password']) < 5 || strlen($_POST['password']) > 20 ||
		!preg_match('/[!@#$%^&*(){}\-_=+?\/.>,<;:]/', $_POST['password']) ||
		!preg_match('/[A-Z]/', $_POST['password'])) {
			include "sign_up.html";
			alert("password must contain at least one special char and one upper case");
			exit ;
		}

		if (!isset($_POST['email']) || !filter_var($_POST['email'], FILTER_VALIDATE_EMAIL)) {
			include "sign_up.html";
    		alert("Invalid email address");
			exit ;
		}

		$pdo = new PDO(
			"mysql:host=model;dbname=camagru;charset=utf8",
			"camagru_admin",
			"camagru_admin_pass"
		);

		$token = random_bytes(32);

		/*checks that user exists then insert user row into users table*/
		$stmt = $pdo->prepare("SELECT * FROM users WHERE username = :username OR email = :email");
		$stmt->execute([':username' => $_POST["username"],':email'=> $_POST['email']]);
		$user = $stmt->fetch();
		if (!$user) {
			$stmt = $pdo->prepare("INSERT INTO users (username, email, password, verification_token, notification) 
			VALUES (:username, :email, :password, :token, :notification)");
			$stmt->execute([
				':username' => $_POST['username'],
				':email' => $_POST['email'],
				':password' => password_hash($_POST['password'], PASSWORD_DEFAULT),
				':token' => bin2hex($token),
				':notification'=>1
			]);
			sendMail(['type'=>"token","value"=> $token], "verification", $_POST['email']);
			$alert = "<script>alert('a verification email was sent to ".htmlspecialchars($_POST['email'])."')</script>";
		}
		else if (!$user['is_verified']) sendMail(['type'=>"token","value"=> $token], "verification", $_POST["email"]);
		else $alert = "<script>alert('user already exists');</script>";
	}

	include "sign_up.html";

	if (isset($alert)) {
		echo $alert;
	}