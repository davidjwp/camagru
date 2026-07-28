<?php
	
	function check_session($user) {
		if (!$user) {
			header('location: /index.php');
			exit;
		}
	}

	function post($doc, $post, $comments, $id) {
		$target = $doc->getElementById('post') ;

		$image = $doc->createElement('img') ;
		$image->setAttribute('class', 'post_image');
		$image->setAttribute('src', '/uploads/'.$post[0]['image_path']);

		$likes = $doc->createElement('div');
		$likes->setAttribute('class', 'likes');
		
		$svg = $doc->createElement('svg');
		$svg->setAttribute('fill', "#000000");
		$svg->setAttribute('width', "30px");
		$svg->setAttribute('height', "30px");
		$svg->setAttribute('viewbox', "0 0 24 24");
		$svg->setAttribute('xmlns', "http://www.w3.org/2000/svg");

		$path = $doc->createElement('path');
		$path->setAttribute('d', "M20.16,5A6.29,6.29,0,0,0,12,4.36a6.27,6.27,0,0,0-8.16,9.48l6.21,6.22a2.78,2.78,0,0,0,3.9,
		0l6.21-6.22A6.27,6.27,0,0,0,20.16,5Zm-1.41,7.46-6.21,6.21a.76.76,0,0,1-1.08,0L5.25,12.43a4.29,4.29,0,0,1,0-6,4.27,
		4.27,0,0,1,6,0,1,1,0,0,0,1.42,0,4.27,4.27,0,0,1,6,0A4.29,4.29,0,0,1,18.75,12.43Z");
		$svg->appendChild($path);
		$likes->nodeValue = strval($post[0]["like_count"]) ." likes";
		
		$com_div = $doc->createElement('div');
		$com_div->setAttribute('class', 'comments');
		// foreach ($comment as $c) {
		// 	$com = $doc->createElement('div');
		// 	$com->setAttribute('class', 'comment');
		// 	$com->nodeValue = $c['content'];
		// 	$com_div->appendChild($com);
		// }
		// $comments->
		
		$target->appendChild($image);
		$target->appendChild($svg);
		$target->appendChild($likes);
		$target->appendChild($com_div);
	}

	function DOMerror($msg, $doc) {
		$target = $doc->getElementById('upload') ;

		$err = $doc->createElement('p') ;
		$err->setAttribute('class', 'error');
		$err->nodeValue = $msg;

		$target->appendChild($err);
	}

	function alert($msg) { exit ("<script>alert('Error: ".$msg."');</script>");}

	function LoadPosts($doc, $posts) {
		$target = $doc->getElementById('posts');

		foreach ($posts as $post) {
			$div = $doc->createElement('div');
			$div->setAttribute('class', 'post');

			$a = $doc->createElement('a');
			$a->setAttribute('href', '/post.php?id=' . $post['id']);

			$img = $doc->createElement('img');
			$img->setAttribute('src', '/uploads/' . $post['image_path']);

			$a->appendChild($img);
			$div->appendChild($a);
			$target->appendChild($div);
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
