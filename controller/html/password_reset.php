<?php
	require_once 'functs.php';
	session_start();

	$data = json_decode(file_get_contents('php://input'), true);

	$pdo = new PDO(
		'mysql:host=model;dbname=camagru;charset=utf8',
		'camagru_admin',
		'camagru_admin_pass'
	);

    $token = $_GET['token'] ?? $data['token'] ?? null;
    
	if (!$token) {
		if (!empty($data)) exit(json_encode(['redirect'=>true]));
	
		header('Location: /index.php');
        exit;
    }

    $stmt = $pdo->prepare("SELECT * FROM users WHERE verification_token = :token AND is_verified = 1");
    $stmt->execute([':token' => $token]);
    $user = $stmt->fetch();
    if (!$user) exit(json_encode(['redirect'=>false,'message'=>'invalid or expired token']));

	if (isset($data['password1']) && isset($data['password2'])) {

		if (strlen($data['password1']) < 5 || strlen($data['password1']) > 20 ||
		!preg_match('/[!@#$%^&*(){}\-_=+?\/.>,<;:]/', $data['password1']) ||
		!preg_match('/[A-Z]/', $data['password1'])) 
			exit(json_encode(['redirect'=>false, 'message'=>'password must contain at least one special char and one upper case']));
		else if ($data['password1'] !== $data['password2'])
			exit(json_encode(['redirect'=>false, 'message'=>'passwords don\'t match']));

		$stmt = $pdo->prepare("UPDATE users SET password = :password WHERE id = :id");
		$stmt->execute([
			':password' => password_hash($data['password1'], PASSWORD_DEFAULT), 
			':id' => $user['id']
		]);
		session_regenerate_id(true);

		exit(json_encode(['redirect'=>true, 'nsi'=>session_id()]));
	}

	if (isset($data['form1'])) {
		header('Content-Type: application/json');
		exit ;
	}

	include "password_reset.html";