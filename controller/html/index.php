<?php
	include 'index.html';
	echo "<h1>testing testing</h1>";
	$pdo = new PDO(
		"mysql:host=model;dbname=camagru;charset=utf8",
		"camagru_admin",
		"camagru_admin_pass"
	);
?>