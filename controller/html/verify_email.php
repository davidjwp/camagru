<?php
	if (!isset($_GET["token"])) {
		header("location: /index.php");
		exit;
	}

	$token = $_GET["token"];
	if ($token) {
		$pdo = new PDO(
			"mysql:host=model;dbname=camagru;charset=utf8",
			"camagru_admin",
			"camagru_admin_pass"
		);

		$stmt = $pdo->prepare("SELECT * FROM users WHERE token = :token");
		$stmt->execute([':token' => $token]);
		$user = $stmt->fetch();

		if (!$user) { 
			echo 'no user for token foung';
			exit;
		}

		 $stmt = $pdo->prepare("UPDATE users SET is_verified = 1, verification_token = NULL WHERE id = :id");
		 $stmt->execute([':id' => $user['id']]);
		
		 header('Location: /index.php');
    	exit;
	}