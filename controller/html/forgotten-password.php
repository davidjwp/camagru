<?php
	require 'functs.php';

	
	if (isset($_POST['email']) && isset($_POST['password1']) && isset($_POST['password2'])) {
		if (!filter_var($_POST['email'], FILTER_VALIDATE_EMAIL))
    		alert("Invalid email address");
		else if (strlen($_POST['password1']) < 5 || strlen($_POST['password1']) > 20 ||
		!preg_match('/[!@#$%^&*(){}\-_=+?\/.>,<;:]/', $_POST['password1']) ||
		!preg_match('/[A-Z]/', $_POST['password1']))
			alert("password must contain at least one special char and one upper case");
		else if ($_POST['password1'] !== $_POST['password2'])
			alert("passwords don't match");
		
		$pdo = new PDO(
			'mysql:host=model;dbname=camagru;charset=utf8',
			'camagru_admin',
			'camagru_admin_pass'
			);
			
		$stmt = $pdo->prepare("SELECT * FROM users WHERE email = :email AND is_verified = 1");
		$stmt->execute([':email' => $_POST['email']]);
		$user = $stmt->fetch();
		if (!$user) 
			alert("user not found");
		
		$stmt = $pdo->prepare("UPDATE users SET password = :password WHERE id = :id");
		$stmt->execute([
			':password' => password_hash($_POST['password1'], PASSWORD_DEFAULT), 
			':id' => $user['id']
		]);
		header('location: index.php');
		exit;
	}
			
	include 'forgotten-password.html';