<?php
	include 'sign_in.html';
	

	var_dump($_POST);
	$pdo = new PDO(
		"mysql:host=model;dbname=camagru;charset=utf8",
		"camagru_admin",
		"camagru_admin_pass"
	);
	if (isset($_POST["username"]) && isset($_POST["password"])) {
		$stmt = $pdo->prepare("SELECT * FROM users WHERE username = :username");
		$stmt->execute([':username' => $_POST['username']]);
		$user = $stmt->fetch();
		
		var_dump($user);
		if ($user) {
			session_start();
			$_SESSION['user'] = $user;
			header('location: /home.php');
			exit;
		}
	}