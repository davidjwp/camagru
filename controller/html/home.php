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

	$doc = new DOMDocument;

	$doc->loadHTMLFile('home.html');
	
	$pdo = new PDO(
		"mysql:host=model;dbname=camagru;charset=utf8",
		$_SERVER['MYSQL_USER'],
		$_SERVER['MYSQL_PASSWORD']
	);

	$LIMIT = 10;

	$post_count = $pdo->query("SELECT COUNT(DISTINCT posts.id) FROM posts")->fetchColumn();
	$total_pages = ceil($post_count / $LIMIT);

	$page = isset($_GET['page']) ? $_GET['page']: 1;
	if ($page > $total_pages) $page = 1;

	$OFFSET = ($page - 1) * $LIMIT;
	$stmt = $pdo->prepare('SELECT * FROM posts ORDER BY posts.created_at DESC LIMIT :lmt OFFSET :ost');
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