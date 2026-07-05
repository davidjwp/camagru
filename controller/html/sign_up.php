<?php
	include ("sign_up.html");

	$ver = 0;
	if (empty($_POST['username'])) $ver |= 1;
	if (empty($_POST['password'])) $ver |= 2;
	if (empty($_POST['email'])) $ver |= 4;

	echo decbin($ver);
	echo "<b><b>$ver";
	if ($ver != 7) echo "<script>alert('Error missing Username, password or email');</script>";
	else {
		$token = random_bytes(32);
		$validate_hash = hash("sha256", $token);

		$LINK = "http://$_SERVER[HTTP_HOST]/verify_email.php?token=" . bin2hex($token);

		$to = $_POST["email"];
		$subject = "Camagru email verification";
		$message = "validate signup with this link\n\n\t$LINK";

		// echo "<script type='text/javascript'>alert('sending verification mail');</script>";
		// mail($to, $subject, $message);

		if (mail($to, $subject, $message)) {
        echo "<script>alert('Verification email sent!');</script>";
    } else {
        echo "<script>alert('Mail failed to send. Check server configuration.');</script>";
        // Log the error
        error_log("Mail failed to send to: " . $to);
    }

	}
	$_POST = [];