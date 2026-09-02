<?php
	session_start();

	if (empty($_POST['csrf-token'])) {
		$_SESSION['csrf-token'] = bin2hex(random_bytes(32));
	}

	//create PDO connection to database then fetch user
	$pdo = new PDO(
		"mysql:host=model;dbname=camagru;charset=utf8",
		"camagru_admin",
		"camagru_admin_pass"
	);

	//check username, password and csrf token then search user
	if (isset($_POST["username"]) && isset($_POST["password"]) && 
	$_POST['csrf-token'] == $_SESSION['csrf-token']) {
		$stmt = $pdo->prepare("SELECT * FROM users WHERE username = :username");
		$stmt->execute([':username' => $_POST['username']]);
		$user = $stmt->fetch();

		//if user is right start session and regenerate a new id to avoid session fixation
		if ($user && password_verify($_POST['password'], $user['password'])) {
			session_regenerate_id(true);
			$_SESSION['csrf-token'] = bin2hex(random_bytes((32)));
			$_SESSION['user'] = $user;
			header('location: /home.php');
			exit;
		}
	}

	include 'sign_in.html';	