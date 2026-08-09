<?php
	require_once 'functs.php';
	session_start();
	
	
	if (!isset($_SESSION['user']) || check_session($_SESSION['user'])) {
		header('location: /index.php');
		exit;
	}
	
	$filename = "/tmp";
	$_SESSION['tmp_images'][] = $filename;
		
	if (isset($_POST["disconnect"])) {
		session_destroy();
		header("location: /index.php");
		exit;
	}

	$doc = new DOMDocument;

	libxml_use_internal_errors(true);
	$doc->loadHTMLFile("editor.html");
	libxml_clear_errors();

	$pdo = new PDO(
		"mysql:host=model;dbname=camagru;charset=utf8",
		"camagru_admin",
		"camagru_admin_pass"
	);

	// $stickers = "/Stickers";
	$stickers = glob('/var/www/html/Stickers/*.png');
	$target = $doc->getElementById("sticker-footer");

	foreach ($stickers as $sticker) {
		$filename = basename($sticker);
		$img = $doc->createElement('img');
		$img->setAttribute('src', '/Stickers/' . $filename);
		$img->setAttribute('class', 'sticker');
		$img->setAttribute('onclick', "selectSticker(this, '/Stickers/$filename')");

		$target->appendChild($img);
		
	}
	// $stmt = $pdo->prepare("SELECT image_path FROM posts WHERE user_id = :user_id ORDER BY created_at DESC");
	// $stmt->execute([":user_id"=>$_SESSION['user']['id']]);
	// $

	// $stickers = glob();

	// $doc->loadHTMLFile("editor.html");

	echo $doc->saveHTML();