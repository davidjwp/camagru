<?php
	session_start();
	require_once 'functs.php';

	check_session($_SESSION['user']);

	if (isset($_POST["disconnect"])) {
		session_destroy();
		header("location: /index.php");
		exit;
	}
	
	include("profile.html");