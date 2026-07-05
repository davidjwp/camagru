<?php
	include 'sign_in.html';
	
	$pdo = new PDO(
		"mysql:host=model;dbname=camagru;charset=utf8",
		"camagru_admin",
		"camagru_admin_pass"
	);
	// if (isset($_POST['username']) && isset($_POST['password'])) {
	// 	$username = $_POST['username'];
	// 	$password = $_POST['password'];

	// 	$pdo->prepare();

	// 	try {
	
	// 	}
	// 	catch (PDOException $e) {
	// 		echo $e->getMessage();
	// 	}
	// }
	// var_dump($_POST);