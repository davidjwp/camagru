<?php
	require '../functs.php';
	session_start();

	$data = json_decode(file_get_contents('php://input'), true);

	if (!isset($data['csrf_token'])) {
		$_SESSION['csrf-token'] = bin2hex(random_bytes(32));
	}

	$pdo = new PDO(
		'mysql:host=model;dbname=camagru;charset=utf8',
		'camagru_admin',
		'camagru_admin_pass'
	);

	if (isset($data['username']) && isset($data['email']) && 
	isset($data['csrf_token']) && $data['csrf_token'] === $_SESSION['csrf-token']) {
		
		if (!filter_var($data['email'], FILTER_VALIDATE_EMAIL)) 
			exit (json_encode(['success'=>false, 'message'=>'invalid email address']));
		else {
			$stmt = $pdo->prepare("SELECT * FROM users WHERE email = :email AND is_verified = 1 AND username = :username");
			$stmt->execute([':email' => $data['email'], ':username'=> $data['username']]);
			$user = $stmt->fetch();

			if (!$user) exit (json_encode(['success'=>false, 'message'=>'user not found']));
			else {
				$token = random_bytes(32);
		
				$stmt = $pdo->prepare("UPDATE users SET verification_token = :token WHERE id = :id");
				$stmt->execute([
					':token' => bin2hex($token),
					':id' => $user['id']
				]);
				sendMail(['type'=>"token","value"=> $token], "password_reset", $data["email"]);
				exit (json_encode(['success'=>true, 'message'=>'mail sent to '.htmlspecialchars($data['email'])]));
			}
		}
	}
	
	if (isset($data['form1'])) {
		header('Content-Type: application/json');
		exit ;
	}

	include '../send_password_reset.html';