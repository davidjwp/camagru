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
	
	$doc->loadHTMLFile('home.html');

	/*loading pages*/
	$LIMIT = 5;

	$post_count = $pdo->query("SELECT COUNT(DISTINCT posts.id) FROM posts")->fetchColumn();
	$total_pages = ceil($post_count / $LIMIT);

	$page = isset($_GET['page']) ? $_GET['page']: 1;
	
	$OFFSET = ($page - 1) * $LIMIT;
	$stmt = $pdo->prepare('SELECT posts.*, users.username, COUNT(DISTINCT likes.user_id) as like_count
	FROM posts LEFT JOIN users ON posts.user_id = users.id LEFT JOIN likes ON posts.id = likes.post_id
	GROUP BY posts.id ORDER BY posts.created_at DESC LIMIT :lmt OFFSET :ost');
	$stmt->bindValue(":lmt", $LIMIT, PDO::PARAM_INT);
	$stmt->bindValue(":ost", $OFFSET, PDO::PARAM_INT);
	$stmt->execute();
	$posts = $stmt->fetchAll();

	if (!empty($posts)) LoadPosts($doc, $posts);
	$nav = $doc->getElementById('pagination');

	if ($page > 1) {
		$prev = $doc->createElement('a');
		$prev->setAttribute('href', '/home.php?page=' . ($page - 1));
		$prev->nodeValue = '← prev';
		$nav->appendChild($prev);
	}

	if ($page < $total_pages) {
		$next = $doc->createElement('a');
		$next->setAttribute('href', '/home.php?page=' . ($page + 1));
		$next->nodeValue = 'next →';
		$nav->appendChild($next);
	}
	
	echo $doc->saveHTML();