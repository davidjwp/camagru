<?php
	function check_session($user){
		if (!$user) {
			header('location: /index.php');
			exit;
		}
	}

	function alert($msg) { exit ("<script>alert('Error: ".$msg."');</script>");}

	function sendVerificationMail($token) {
		/*send validation link to user email*/
		$validate_hash = hash("sha256", $token);

		$LINK = "http://$_SERVER[HTTP_HOST]/verify_email.php?token=" . bin2hex($token);
		
		$to = $_POST["email"];
		$subject = "Camagru email verification";
		$message = "validate signup with this link\n\n\t$LINK";
		$result = mail($to, $subject, $message);
		if (!$result) {
			alert("Mail failed");
			error_log(error_get_last());
		}
	}
