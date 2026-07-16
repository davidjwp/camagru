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

	$stmt = $pdo->prepare('SELECT posts.*, users.username, COUNT(DISTINCT likes.id) as like_count
	FROM posts LEFT JOIN users ON posts.user_id=users.id LEFT JOIN likes ON posts.id=likes.post_id GROUP BY posts.id');
	$stmt->execute();
	$posts_users = $stmt->fetch();

	$doc->loadHTMLFile('home.html');
	
	// $xpath = new DOMXPath($doc);
	// $button = $xpath->query('');
	// $doc->getElementById('profile_button')->innerHTML = "HEYYEY";
	// var_dump($button);
	echo $doc->saveHTML();
	var_dump($posts_users);