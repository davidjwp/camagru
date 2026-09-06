<?php
	session_start();
	require_once 'functs.php';

	$data = json_decode(file_get_contents('php://input'), true);

	if (!empty($data)) {
	//if any error in user input send back a json with error message
	if (empty($data['username'])) exit (json_encode(['success'=>false, 'message'=>'missing username']));
	if (empty($data['password'])) exit (json_encode(['success'=>false, 'message'=>'missing password']));
	if (empty($data['email'])) exit (json_encode(['success'=>false, 'message'=>'missing email']));
	
	if (strlen($data['username']) < 5 || strlen($data['username']) > 20)
		exit (json_encode(['success'=>false, 'message'=>'username at least 5 char long and no longer than 20 chars']));
		
	if (strlen($data['password']) < 5 || strlen($data['password']) > 20 ||
	!preg_match('/[!@#$%^&*(){}\-_=+?\/.>,<;:]/', $data['password']) ||
	!preg_match('/[A-Z]/', $data['password'])) 
		exit (json_encode(['success'=>false, 'message'=>'password must contain at least one special char and one upper case']));

	if (!isset($data['email']) || !filter_var($data['email'], FILTER_VALIDATE_EMAIL))
		exit (json_encode(['success'=>false, 'message'=>'invalid email address']));
		
	$pdo = new PDO(
		"mysql:host=model;dbname=camagru;charset=utf8",
		$_SERVER['MYSQL_USER'],
		$_SERVER['MYSQL_PASSWORD']
	);

	$token = random_bytes(32);


	//checks that user exists then insert user row into users table
	$stmt = $pdo->prepare("SELECT * FROM users WHERE username = :username OR email = :email");
	$stmt->execute([':username' => $data["username"],':email'=> $data['email']]);
	$user = $stmt->fetch();
	error_log('HERE1');
	if (!$user) {
		$stmt = $pdo->prepare("INSERT INTO users (username, email, password, verification_token, notification) 
		VALUES (:username, :email, :password, :token, :notification)");
		$stmt->execute([
			':username' => $data['username'],
			':email' => $data['email'],
			':password' => password_hash($data['password'], PASSWORD_DEFAULT),
			':token' => bin2hex($token),
			':notification'=>1
		]);
		error_log('HERE2');
		sendMail(['type'=>"token","value"=> $token], "verification", $data['email']);
		exit (json_encode(['success'=>true, 'message'=>"a verification email was sent to ".htmlspecialchars($data['email'])]));
	}
	else if (!$user['is_verified']) { 
		sendMail(['type'=>"token","value"=> $token], "verification", $data["email"]);
		exit (json_encode(['success'=>false, 'message'=>'user already exists, verification email sent to '. htmlspecialchars($data['email'])]));
	}
	else exit (json_encode(['success'=>true, 'message'=>'user already exists']));
}
	if (isset($data['form'])) { 
		header('Content-Type: application/json');
		exit ;
	}

	include "sign_up.html";