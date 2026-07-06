<?php
	include ("sign_up.html");
	
	function alert($msg) { echo "<script>alert('Error: ".$msg."');</script>";}
	function find($str, $comp) {
		foreach ($c in $str and $cs in $comp)
			if ($c === $cs) return true;
	}

	var_dump($_POST);

	if (!isset($_POST['signup'])) {
		$ver = 0;
		if (!empty($_POST['username'])) $ver |= 1;
		if (!empty($_POST['password'])) $ver |= 2;
		if (!empty($_POST['email'])) $ver |= 4;

		if ($ver != 7) {
			alert("missing Username, password or email");}
		else {
			if (strlen($_POST['username']) < 5) 
				alert("username at least 5 char long");
			else if (strlen($_POST['password']) < 5 || 
			!str_contains($_POST['password'], "!@#$%^&*(){}-_=+?/.>,<;:ABCDEFGHIJKLMNOPQRSTUVWXYZ"))
				alert("password must contain at least one special char and one upper case");
			else if ()
			
			$pdo = new PDO(
				"mysql:host=model;dbname=camagru;charset=utf8",
				"camagru_admin",
				"camagru_admin_pass"
			);

			$sql = "SELECT * FROM users WHERE ";

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
				alert("Mail failed");
    			error_log(error_get_last());
			}

		}
	}
	// $_POST = [];