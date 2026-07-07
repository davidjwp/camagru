<?php
	include 'sign_in.html';
	
	$pdo = new PDO(
		"mysql:host=model;dbname=camagru;charset=utf8",
		"camagru_admin",
		"camagru_admin_pass"
	);
	if (isset($_POST["username"]) && isset($_POST["password"])) {
		$stmt = $pdo->prepare("SELECT * FROM users WHERE :username = username AND :password = password");
		$stmt->execute([':username' => $_POST['username'], ':password' => $_POST['password']]);
		$user = $stmt->fetch();
		if ($user) {
			$_SESSION['user'] = $user;
			
		}
	}