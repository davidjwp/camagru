<?php
	include ("sign_up.html");
	
	var_dump($_POST);

	if (!isset($_POST['signup'])) {
		$ver = 0;
		if (!empty($_POST['username'])) $ver |= 1;
		if (!empty($_POST['password'])) $ver |= 2;
		if (!empty($_POST['email'])) $ver |= 4;
		
		echo decbin($ver);
		echo "<b><b>$ver";
		if ($ver != 7) echo "<script>alert('Error missing Username, password or email');</script>";
		else {

			$pdo = new PDO(
				"mysql:host=model;dbname=camagru;charset=utf8",
				"camagru_admin",
				"camagru_admin_pass"
			);

			$token = random_bytes(32);
			$validate_hash = hash("sha256", $token);
			
			$LINK = "http://$_SERVER[HTTP_HOST]/verify_email.php?token=" . bin2hex($token);
			
			$to = $_POST["email"];
			$subject = "Camagru email verification";
			$message = "validate signup with this link\n\n\t$LINK";
			
			// echo "<script type='text/javascript'>alert('sending verification mail');</script>";
			// mail($to, $subject, $message);
			$result = mail($to, $subject, $message);
			if (!$result) {
				echo "<script>alert('Mail failed');</script>";
    			error_log(error_get_last());
			}

		}
	}
	// $_POST = [];