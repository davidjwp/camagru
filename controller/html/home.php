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
	$doc->loadHTMLFile('home.html');
	
	// $xpath = new DOMXPath($doc);
	// $button = $xpath->query('');
	// $doc->getElementById('profile_button')->innerHTML = "HEYYEY";
	// var_dump($button);
	echo $doc->saveHTML();
	
	var_dump($_SESSION);
	