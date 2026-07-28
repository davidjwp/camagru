<?php
	require_once 'functs.php';
	session_start();

	if (isset($_POST["disconnect"])) {
		session_destroy();
		header("location: /index.php");
		exit;
	}

	if (isset($_GET['id'])) $_POST['id'] = $_GET['id'];
	
	if (!isset($_SESSION['user']) || check_session($_SESSION['user'])) {
		header('location: /index.php');
		exit;
	}

	$doc = new DOMDocument;
	$doc->loadHTMLFile('post.html');

	$pdo = new PDO(
		"mysql:host=model;dbname=camagru;charset=utf8",
		"camagru_admin",
		"camagru_admin_pass"
		);

	if (isset($_POST['comment_text'])) {
		$stmt = $pdo->prepare("SELECT * FROM comments WHERE post_id = :post_id");
		$stmt->execute();
	}


	$post_query = "SELECT posts.*, users.username, COUNT(DISTINCT likes.user_id) as like_count FROM posts 
	LEFT JOIN users ON posts.user_id = users.id LEFT JOIN likes ON posts.id = likes.post_id
	WHERE posts.id = :id
	GROUP BY posts.id ORDER BY posts.created_at DESC";

	$comments_query = "SELECT comments.*, users.username FROM comments
	LEFT JOIN users ON comments.user_id = users.id
	WHERE comments.post_id = :post_id
	ORDER BY comments.created_at ASC";

	$comment_count = "SELECT COUNT(DISTINCT id) as comment_count FROM comments";
	
	$stmt = $pdo->prepare($post_query);
	$stmt->execute([':id'=>$_GET['id']]);
	$post = $stmt->fetchAll();
	if (!$post) {
		DOMerror("Error pulling post", $doc);
		echo $doc->saveHTML();
		exit; 
	}

	$stmt = $pdo->prepare($comments_query);
	$stmt->execute([':post_id'=>$_POST['id']]);
	$comments = $stmt->fetch();

	$stmt = $pdo->prepare($comment_count);
	$stmt->execute();
	$comment_count = $stmt->fetch();
	// var_dump($comments);
	if ($comments) $comments['comment_count'] = $comment_count ?? (int)0;

	// var_dump($post[0]);
	var_dump($post[0]['image_path']);
	post($doc, $post, $comments, $_POST['id']);
	echo $doc->saveHTML();
