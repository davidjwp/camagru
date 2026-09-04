<?php 
	require_once 'functs.php';
	session_start();
	
	if (!isset($_SESSION['user'])) {
		header('Content-Type: application/json');
		header('location: /index.php');
		exit;
	}
	
	$pdo = new PDO(
		"mysql:host=model;dbname=camagru;charset=utf8",
		"camagru_admin",
		"camagru_admin_pass"
	);
	
	$data = json_decode(file_get_contents('php://input'), true);

	if (isset($data['csrf_token']) && $data['csrf_token'] === $_SESSION['csrf-token']) {
		$like = "INSERT INTO likes (post_id, user_id) VALUES (:post_id, :user_id)";
		$unlike = "DELETE FROM likes WHERE :post_id = post_id AND :user_id = user_id";
		
		$check_like = "SELECT * FROM likes WHERE :post_id = post_id AND :user_id = user_id";
		$stmt = $pdo->prepare($check_like);
		$stmt->execute([":post_id"=>$data['id'], ":user_id"=>$_SESSION['user']['id']]);
		$liked = $stmt->fetch();
		
		if (!$liked) {
			$stmt = $pdo->prepare($like);
			$stmt->execute([":post_id"=>$data['id'], ":user_id"=>$_SESSION['user']['id']]);
		}
		else {
			$stmt = $pdo->prepare($unlike);
			$stmt->execute([":post_id"=>$data['id'], ":user_id"=>$_SESSION['user']['id']]);
		}

		$count_stmt = $pdo->prepare("SELECT COUNT(*) as like_count FROM likes WHERE post_id = :post_id");
		$count_stmt->execute([":post_id" => $data['id']]);
		$count = $count_stmt->fetch();

		exit (json_encode(['success' => true, 'like_count' => $count['like_count'], 'liked' => !$liked]));
	}
?>