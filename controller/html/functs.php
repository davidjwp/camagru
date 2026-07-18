<?php
	
	function check_session($user) {
		if (!$user) {
			header('location: /index.php');
			exit;
		}
	}

	function alert($msg) { exit ("<script>alert('Error: ".$msg."');</script>");}

	function LoadPosts($doc, $posts) {
		$target = $doc->getElementById('posts');

		foreach ($posts as $post) {
			$div = $doc->createElement('div');
			$div->setAttribute('class','post');

			$img = $doc->createElement('img');
			// $img->setAttribute("src","/uploads/<?php echo htmlspecialchars($post['image_path']);");

			$p1 = $doc->createElement("p");
			// $p1->innerText = ;
			$p1->setAttribute("","");
		}
	}

	function AppendPasswordReset($doc) {
		$target = $doc->getElementById('reset_password');
		
		$form = $doc->createElement('form');
		$form->setAttribute('action', '/profile.php');
		$form->setAttribute('method', 'post');

		$input1 = $doc->createElement('input');
		$input1->setAttribute('type', 'password');
		$input1->setAttribute('name', 'password1');
		$input1->setAttribute('placeholder', 'new password');
		
		$form->appendChild($input1);
		$form->appendChild($doc->createElement('br'));

		$input2 = $doc->createElement('input');
		$input2->setAttribute('type', 'password');
		$input2->setAttribute('name', 'password2');
		$input2->setAttribute('placeholder', 'confirm new password');

		$form->appendChild($input2);
		$form->appendChild($doc->createElement('br'));
		
		$input3 = $doc->createElement('input');
		$input3->setAttribute('type', 'submit');
		$input3->setAttribute('name', 'submit');
		$input3->setAttribute('value', 'send');

		$form->appendChild($input3);
		$target->appendChild($form);
	}

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
