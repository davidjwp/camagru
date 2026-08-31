<?php
	require_once 'functs.php';
	session_start();

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

    $token = $_GET['token'] ?? $_POST['token'] ?? null;
    if (!$token) {
        header('Location: /index.php');
        exit;
    }

    $stmt = $pdo->prepare("SELECT * FROM users WHERE verification_token = :token AND is_verified = 1");
    $stmt->execute([':token' => $token]);
    $user = $stmt->fetch();
    if (!$user) {
		alert('invalid or expired token');
		include "password_reset";
		exit ;
	}

	if (!empty($_POST['password1']) && !empty($_POST['password2']) &&
	isset($_POST['csrf-token']) && $_POST['csrf-tokn'] === $_SESSION['csrf-token']) {
		
		if (strlen($_POST['password1']) < 5 || strlen($_POST['password1']) > 20 ||
		!preg_match('/[!@#$%^&*(){}\-_=+?\/.>,<;:]/', $_POST['password1']) ||
		!preg_match('/[A-Z]/', $_POST['password1'])) {
			include "password_reset.html";
			alert("password must contain at least one special char and one upper case");
			exit ;
		}
		else if ($_POST['password1'] !== $_POST['password2']) {
			include "password_reset.html";
			alert("passwords don't match");
			exit ;
		}

		$stmt = $pdo->prepare("UPDATE users SET password = :password WHERE id = :id");
		$stmt->execute([
			':password' => password_hash($_POST['password1'], PASSWORD_DEFAULT), 
			':id' => $user['id']
		]);
		
		header('location: index.php');
		exit;
	}

	include "password_reset.html";