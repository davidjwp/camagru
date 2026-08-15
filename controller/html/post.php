<?php
	require_once 'functs.php';
	session_start();

	if (!isset($_SESSION['user'])) {
		header('location: /index.php');
		exit;
	}

	if (isset($_POST["disconnect"])) {
		session_destroy();
		header("location: /index.php");
		exit;
	}

	//to keep data across POST's using session
	isset($_GET['id']) ? $_SESSION['post_id'] = $_GET['id']: null;

	isset($_SESSION['post_id']) ?  $post_id = $_SESSION['post_id']: $post_id = null;
	
	if (!$post_id) {
		header('location: /home.php');
		exit;
	}

	$doc = new DOMDocument;
	$doc->loadHTMLFile('post.html');

	$pdo = new PDO(
		"mysql:host=model;dbname=camagru;charset=utf8",
		"camagru_admin",
		"camagru_admin_pass"
	);

	$data = json_decode(file_get_contents('php://input'), true);

	if (isset($data['delete'])) {
		$stmt = $pdo->prepare("SELECT image_path FROM posts WHERE id = :post_id");
		$stmt->execute([':post_id'=>$post_id]);
		$img_path = "/var/www/html/uploads/" . $stmt->fetchColumn();
		if ($img_path)
			unlink($img_path);

		$stmt = $pdo->prepare("DELETE FROM posts WHERE id = :post_id");
		$stmt->execute([':post_id'=>$post_id]);

		echo json_encode(['success'=>true]);
		exit ;
	}

	$post_query = "SELECT posts.*, users.username, users.email, COUNT(DISTINCT likes.user_id) as like_count FROM posts 
	LEFT JOIN users ON posts.user_id = users.id LEFT JOIN likes ON posts.id = likes.post_id
	WHERE posts.id = :id
	GROUP BY posts.id ORDER BY posts.created_at DESC";

	$comments_query = "SELECT comments.*, users.username FROM comments
	LEFT JOIN users ON comments.user_id = users.id
	WHERE comments.post_id = :post_id
	ORDER BY comments.created_at ASC";

	$like_query = "SELECT * FROM likes WHERE :post_id = post_id AND :user_id = user_id";

	$stmt = $pdo->prepare($post_query);
	$stmt->execute([':id'=>$post_id]);
	$post = $stmt->fetchAll();
	if (!$post) {
		header('location: /home.php');
		exit; 
	}

	$stmt = $pdo->prepare($comments_query);
	$stmt->execute([':post_id'=>$post_id]);
	$comments = $stmt->fetchall();
	$comment_count = count($comments);

	if (isset($_POST['comment_text']) && !empty($_POST['comment_text'])) {
		$stmt = $pdo->prepare(
			"INSERT INTO comments (post_id, user_id, content) 
			VALUES (:post_id, :user_id, :content)"
		);
		$stmt->execute([
			":post_id"=>$post_id, 
			":user_id"=>$_SESSION['user']['id'], 
			":content"=>$_POST['comment_text']
			]);
			
		$stmt = $pdo->prepare("SELECT notification FROM users WHERE id = :id");
		$stmt->execute([":id"=>$_SESSION['user']['id']]);
		$notification = $stmt->fetch();
		if ($notification[0]) {
			sendMail(["type"=>'','value'=>[
				'comment_text'=>$_POST['comment_text'],
				'username'=>$_SESSION['user']['username']
				]], "new_comment", $post[0]['email']);
		}
		header('location: /post.php?id='.$post_id);
		exit;
	}

	$stmt = $pdo->prepare($like_query);
	$stmt->execute([':post_id'=>$post_id, ':user_id'=>$_SESSION['user']['id']]);
	$liked = $stmt->fetch() ? 1: 0;

	$stmt = $pdo->prepare("SELECT * FROM posts WHERE id = :post_id AND user_id = :user_id");
	$stmt->execute([':post_id'=>$post_id, ':user_id'=>$_SESSION['user']['id']]);
	$is_user = $stmt->fetch() ? 1: 0;

	post($doc, $post, $comments, $comment_count, $liked, $is_user);
	echo $doc->saveHTML();
