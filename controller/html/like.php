<?php 
	ob_start();
	session_start();
	require_once 'functs.php';
	
	if (!isset($_SESSION['user'])) {
		header('location: /index.php');
		exit;
	}
	
	$pdo = new PDO(
		"mysql:host=model;dbname=camagru;charset=utf8",
		"camagru_admin",
		"camagru_admin_pass"
	);
	
	$data = json_decode(file_get_contents('php://input'), true);
    error_log("post id: " . print_r($data, true));
	
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

    ob_clean();
    header('Content-Type: application/json');
    echo json_encode(['success' => true, 'like_count' => $count['like_count'], 'liked' => !$liked]);
    exit;
?>