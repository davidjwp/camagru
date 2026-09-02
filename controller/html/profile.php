<?php
	require_once 'functs.php';
	session_start();
	session_regenerate_id(true);
	
	if (!isset($_SESSION['user'])) {
		header('location: /index.php');
		exit;
	}

	$pdo = new PDO(
		"mysql:host=model;dbname=camagru;charset=utf8",
		"camagru_admin",
		"camagru_admin_pass"
	);

	if (isset($_POST["disconnect"])) {
		session_destroy();

		header("location: /index.php");
		exit;
	}

	$data = json_decode(file_get_contents('php://input'), true);

	$doc = new DOMDocument();
	$doc->loadHTMLFile('profile.html');

	if (!isset($data['csrf_token'])) {
		$_SESSION['csrf-token'] = bin2hex(random_bytes(32));
	}
	
	$doc->getElementById('csrf-token1')->setAttribute('value', $_SESSION['csrf-token']);
	$doc->getElementById('csrf-token2')->setAttribute('value', $_SESSION['csrf-token']);

	$user = $_SESSION["user"];
	
	/*check username or email then change them*/
	$change = ['',''];
	if (!empty($data["username"])) {
		if (strlen($data['username']) < 5 || strlen($data['username']) > 20) 
			exit (json_encode(['success'=> false, 'message'=>'username must be between 5 and 20 characters long']));
		$change[0] = 'username';
	}
	if (!empty($data["email"])) {
		if (!filter_var($data['email'], FILTER_VALIDATE_EMAIL)) 
			exit (json_encode(['success'=> false, 'message'=>'invalid email address']));
		$change[1] = 'email';
	}

	if (isset($data['csrf_token']) && $data['csrf_token'] === $_SESSION['csrf-token']) {
		foreach ($change as $ch) {
			if (!empty($ch)) {
				$stmt = $pdo->prepare("UPDATE users SET $ch = :$ch WHERE id = :id");
				$stmt->execute([":$ch"=> $data[$ch], ':id'=>$user['id']]);
				$_SESSION['user'][$ch] = $data[$ch];
				}
			}
		
		if (!empty($change[0]) || !empty($change[1]))
			exit (json_encode(['success'=>true, 'message'=>'update successful']));
	}

	if (!empty($data["password1"]) && !empty($data["password2"])) {
		if (strlen($data['password1']) < 5 || strlen($data['password1']) > 20 ||
		!preg_match('/[!@#$%^&*(){}\-_=+?\/.>,<;:]/', $data['password1']) ||
		!preg_match('/[A-Z]/', $data['password1'])) {
			echo json_encode(['success'=> false, 'message'=>'password needs to be at least 5 and max 20 characters long and contain at least one uppercase and special character']);
			exit ;
		}
		else if ($data['password1'] !== $data['password2']) {
			echo json_encode(['success'=>false, 'message'=> 'passwords dont match']);
			exit ;
		}
		else {
			$stmt = $pdo->prepare("UPDATE users SET password = :password WHERE id = :id");
			$stmt->execute([
				':password' => password_hash($data['password1'], PASSWORD_DEFAULT), 
				':id' => $user['id']
				]);
			exit (json_encode(['success'=>true, 'message'=>'update successful']));
		}
	}
	
	//fetch notification status and add input element
	$doc->getElementById('welcome_header')->nodeValue = "Welcome ". $user['username'];
	$doc->getElementById('email_display')->nodeValue = $user['email'];

	$stmt = $pdo->prepare("SELECT notification FROM users WHERE id = :id");
	$stmt->execute([":id"=>$_SESSION['user']['id']]);
	$notification = $stmt->fetch()['notification'] ? 1: 0;

	$target = $doc->getElementById('toggle');

	$toggle = $doc->createElement('input');
	$toggle->setAttribute('type', 'checkbox');
	$toggle->setAttribute('id', 'notif-toggle');
	if (!$notification) $toggle->removeAttribute('checked'); 
	else $toggle->setAttribute('checked', 'checked');

	$span = $doc->createElement('span');
	$span->setAttribute('class', 'slider');

	$target->appendChild($toggle);
	$target->appendChild($span);

	//get json from toggle POST fetch and update notification accordingly
	if (isset($data['checked'])) {
		$notification = $data['notification'] ? 1 : 0;
		$stmt = $pdo->prepare("UPDATE users SET notification = :notification WHERE id = :id");
		$stmt->execute([":notification"=>$notification, ":id"=>$user['id']]);
		exit (json_encode(['success'=>true, 'message'=>'notification updated']));
	}

	echo $doc->saveHTML();