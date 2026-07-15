<?php
	require 'functs.php';

	$pdo = new PDO(
		'mysql:host=model;dbname=camagru;charset=utf8',
		'camagru_admin',
		'camagru_admin_pass'
	);
	
	if (isset($_POST['username']) && isset($_POST['email'])) {
		if (!filter_var($_POST['email'], FILTER_VALIDATE_EMAIL))
    		alert("Invalid email address");
		
		$stmt = $pdo->prepare("SELECT * FROM users WHERE email = :email AND is_verified = 1 AND username = :username");
		$stmt->execute([':email' => $_POST['email'], ':username'=> $_POST['username']]);
		$user = $stmt->fetch();
		if (!$user) alert("user not found");
		
		$token = random_bytes(32);
		
		$stmt = $pdo->prepare("UPDATE users SET verification_token = :token WHERE id = :id");
		$stmt->execute([
			':token' => bin2hex($token),
			':id' => $user['id']
		]);
		sendMail($token, "password_reset", $_POST["email"]);
	}
			
	include 'send_password_reset.html';