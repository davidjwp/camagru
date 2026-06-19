<?php
	include 'sign_in.html';
	if ($_POST && ($_POST['username'] || $_POST['password'])) {
		echo "detected<br><br>";
	}
	var_dump($_SERVER);
	echo "<br><br>";
	var_dump($_POST);
	$pdo = new PDO(
		"mysql:host=model;dbname=camagru;charset=utf8",
		"camagru_admin",
		"camagru_admin_pass"
	);
?>