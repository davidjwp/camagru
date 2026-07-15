<?php
	require_once 'functs.php';
	include ("sign_up.html");
	
	if (!empty($_POST)) {
		$ver = 0;
		if (!empty($_POST['username'])) $ver |= 1;
		if (!empty($_POST['password'])) $ver |= 2;
		if (!empty($_POST['email'])) $ver |= 4;

		/*change error warnings to small unintrusive pop ups later on*/
		if ($ver != 7) alert("missing Username, password or email");

		if (strlen($_POST['username']) < 5 || strlen($_POST['username']) > 20)
			alert("username at least 5 char long and no longer than 20 chars");
		else if (strlen($_POST['password']) < 5 || strlen($_POST['password']) > 20 ||
		!preg_match('/[!@#$%^&*(){}\-_=+?\/.>,<;:]/', $_POST['password']) ||
		!preg_match('/[A-Z]/', $_POST['password']))
			alert("password must contain at least one special char and one upper case");
		if (!filter_var($_POST['email'], FILTER_VALIDATE_EMAIL))
    		alert("Invalid email address");

		$pdo = new PDO(
			"mysql:host=model;dbname=camagru;charset=utf8",
			"camagru_admin",
			"camagru_admin_pass"
		);

		$token = random_bytes(32);

		/*checks that user exists then insert user row into users table*/
		$stmt = $pdo->prepare("SELECT * FROM users WHERE username = :username AND email = :email");
		$stmt->execute([':username' => $_POST["username"],':email'=> $_POST['email']]);
		$user = $stmt->fetch();
		if (!$user) {
			$stmt = $pdo->prepare("INSERT INTO users (username, email, password, verification_token) 
			VALUES (:username, :email, :password, :token)");
			$stmt->execute([
				':username' => $_POST['username'],
				':email' => $_POST['email'],
				':password' => password_hash($_POST['password'], PASSWORD_DEFAULT),
				':token' => bin2hex($token)
			]);
			sendMail($token, "verification");
		}
		else if (!$user['is_verified']) sendMail($token, "verification");
		else alert("user already exists");
	}