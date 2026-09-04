<?php
	require_once 'functs.php';
	session_start();
	
	if (!isset($_SESSION['user'])) {
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

	//disable libxml errors from <video><canvas> because PHP no likey
	libxml_use_internal_errors(true);
	$doc->loadHTMLFile("editor.html");
	libxml_clear_errors();

	$doc->getElementById('csrf-token')->setAttribute('value', $_SESSION['csrf-token']);

	$stickers = glob('/var/www/html/Stickers/*.png');
	$target = $doc->getElementById("stickers");
	addStickers($stickers, $target, $doc);

	echo $doc->saveHTML();