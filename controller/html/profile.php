<?php
	session_start();

	if (isset($_POST["disconnect"])) {
		session_destroy();
		header("location: /index.php");
		exit;	
		}
	
	include("profile.html");