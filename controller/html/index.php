<?php
	$pdo = new PDO(
		"mysql:host=model;dbname=camagru;charset=utf8",
		"camagru_admin",
		"camagru_admin_pass"
	);
	if (isset($_POST["username"]) && isset($_POST["password"])) {
		$stmt = $pdo->prepare("SELECT * FROM users WHERE username = :username");
		$stmt->execute([':username' => $_POST['username']]);
		$user = $stmt->fetch();
		
		if ($user && password_verify($_POST['password'], $user['password'])) {
			session_start();
			$_SESSION['user'] = $user;
			header('location: /home.php');
			exit;
		}
	}
	include 'sign_in.html';
	// echo '<br>'.session_status();