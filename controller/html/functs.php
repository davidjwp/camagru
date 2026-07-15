<?php
	
	function check_session($user){
		if (!$user) {
			header('location: /index.php');
			exit;
		}
	}

	function alert($msg) { exit ("<script>alert('Error: ".$msg."');</script>");}

	function sendMail($token, $type, $to) {

		$l = $type == "verification" ? "verify_email.php" : "password_reset.php";

		$LINK = "http://$_SERVER[HTTP_HOST]/$l?token=" . bin2hex($token);
		
		switch ($type) {
			case "verification":
				$subject = "Camagru email verification";
				$message = "validate signup with this link\n\n\t$LINK";
				break;
			case "password_reset":
				$subject = "Camagru password reset";
				$message = "confirm password reset with this link\n\n\t$LINK";
				break;
		}

		$result = mail($to, $subject, $message);
		if (!$result) {
			alert("Mail failed");
			error_log(error_get_last());
		}
	}
