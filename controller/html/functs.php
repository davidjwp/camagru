<?php
	function check_session($user){
		if (!$user) {
			header('location: /index.php');
			exit;
		}
	}