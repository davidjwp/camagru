<?php
	require_once 'functs.php';
	session_start();
	
	if (!isset($_SESSION['user']) || check_session($_SESSION['user'])) {
		header('location: /index.php');
		exit;
	}

		
	if (isset($_POST["disconnect"])) {
		session_destroy();
		header("location: /index.php");
		exit;
	}
	$doc = new DOMDocument;

	$pdo = new PDO(
		"mysql:host=model;dbname=camagru;charset=utf8",
		"camagru_admin",
		"camagru_admin_pass"
	);

	if (isset($_FILE['file-upload'])) {
		echo "testestsdfsf";
	}

	$stmt = $pdo->prepare('SELECT posts.*, users.username, COUNT(DISTINCT likes.user_id) as like_count
	FROM posts LEFT JOIN users ON posts.user_id = users.id LEFT JOIN likes ON posts.id = likes.post_id
	GROUP BY posts.id ORDER BY posts.created_at DESC');
	$stmt->execute();
	$posts = $stmt->fetch();
	// if (!empty($posts)) LoadPosts($posts);
		
		$doc->loadHTMLFile('home.html');
	
	// $xpath = new DOMXPath($doc);
	// $button = $xpath->query('');
	// $doc->getElementById('profile_button')->innerHTML = "HEYYEY";
	// var_dump($button);
	echo $doc->saveHTML();
	echo "<br>";
	if (isset($_FILE)) var_dump($_FILE);
	else echo "NULL";
	echo "<br>";
	var_dump($_POST);
	// var_dump($posts_users);