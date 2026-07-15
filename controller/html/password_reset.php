<?php
	require_once 'functs.php';

	$pdo = new PDO(
		'mysql:host=model;dbname=camagru;charset=utf8',
		'camagru_admin',
		'camagru_admin_pass'
	);

    $token = $_GET['token'] ?? $_POST['token'] ?? null;
    if (!$token) {
        header('Location: /index.php');
        exit;
    }

    $stmt = $pdo->prepare("SELECT * FROM users WHERE verification_token = :token AND is_verified = 1");
    $stmt->execute([':token' => $token]);
    $user = $stmt->fetch();
    if (!$user) alert("invalid or expired token");

	if ($_SERVER['REQUEST_METHOD'] == 'POST' && !empty($_POST['password1']) && !empty($_POST['password2'])) {
		if (strlen($_POST['password1']) < 5 || strlen($_POST['password1']) > 20 ||
		!preg_match('/[!@#$%^&*(){}\-_=+?\/.>,<;:]/', $_POST['password1']) ||
		!preg_match('/[A-Z]/', $_POST['password1']))
			alert("password must contain at least one special char and one upper case");
		else if ($_POST['password1'] !== $_POST['password2'])
			alert("passwords don't match");

		$stmt = $pdo->prepare("UPDATE users SET password = :password WHERE id = :id");
		$stmt->execute([
			':password' => password_hash($_POST['password1'], PASSWORD_DEFAULT), 
			':id' => $user['id']
		]);
		
		header('location: index.php');
		exit;
	}

	include "password_reset.html";