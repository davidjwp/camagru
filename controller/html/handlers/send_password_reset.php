<?php
	require '../functs.php';
	session_start();
	$alert;

	if (empty($_POST['csrf-token'])) {
		$_SESSION['csrf-token'] = bin2hex(random_bytes(32));
	}

		if (isset($_POST['csrf-token']))
		error_log('csrf token post password reset'.$_POST['csrf-token']);
	else
		error_log('NO POST TOKEN');

	$pdo = new PDO(
		'mysql:host=model;dbname=camagru;charset=utf8',
		'camagru_admin',
		'camagru_admin_pass'
	);

	if (isset($_POST['username']) && isset($_POST['email']) && 
	isset($_POST['csrf-token']) && $_POST['csrf-token'] === $_SESSION['csrf-token']) {
		if (!filter_var($_POST['email'], FILTER_VALIDATE_EMAIL)) {
			$alert = "<script>alert('invalid email address');</script>";
		}
		else {
			$stmt = $pdo->prepare("SELECT * FROM users WHERE email = :email AND is_verified = 1 AND username = :username");
			$stmt->execute([':email' => $_POST['email'], ':username'=> $_POST['username']]);
			$user = $stmt->fetch();
			if (!$user) $alert = "<script>alert('user not found');</script>";
			else {
				$token = random_bytes(32);
		
				$stmt = $pdo->prepare("UPDATE users SET verification_token = :token WHERE id = :id");
				$stmt->execute([
					':token' => bin2hex($token),
					':id' => $user['id']
				]);
				sendMail(['type'=>"token","value"=> $token], "password_reset", $_POST["email"]);
				$alert = "<script>alert('mail sent to ".htmlspecialchars($_POST['email'])."')</script>";
			}
		}
	}
	include '../send_password_reset.html';

	if (isset($alert)) {
		echo $alert;
	}