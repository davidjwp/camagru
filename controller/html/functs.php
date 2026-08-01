<?php
	
	function check_session($user) {
		if (!$user) {
			header('location: /index.php');
			exit;
		}
	}

	function post($doc, $post, $comments, $comment_count, $liked) {
		$post_target = $doc->getElementById('post') ;
		$com_target = $doc->getElementById('comments');

		$image = $doc->createElement('img') ;
		$image->setAttribute('class', 'post_image');
		$image->setAttribute('src', '/uploads/'.$post[0]['image_path']);

		$likes = $doc->createElement('div');
		$likes_counter = $doc->createElement('span');
		$likes_counter->nodeValue = strval($post[0]["like_count"]) ." likes";
		$likes->setAttribute('class', 'like');
		$likes_counter->setAttribute('id', 'likes_counter');
		
		//heart svg
		$svg = $doc->createElement('svg');
		$liked ? $svg->setAttribute('fill', "#ff0000"): $svg->setAttribute('fill', "#000000");;
		$svg->setAttribute('width', "30px");
		$svg->setAttribute('height', "30px");
		$svg->setAttribute('viewbox', "0 0 24 24");
		$svg->setAttribute('xmlns', "http://www.w3.org/2000/svg");
		$svg->setAttribute('onclick', "toggleLike(".$post[0]['id'].",".$liked.",".$post[0]["like_count"].")");
		$svg->setAttribute('class', "heart");
		$svg->setAttribute('id', "heart".$post[0]['id']);
		
		$path = $doc->createElement('path');
		$path->setAttribute('d', "M12 21.35l-1.45-1.32C5.4 15.36 2 12.28 2 8.5 2 5.42 4.42 3 7.5 3c1.74 0 
		3.41.81 4.5 2.09C13.09 3.81 14.76 3 16.5 3 19.58 3 22 5.42 22 8.5c0 3.78-3.4 6.86-8.55 11.54L12 21.35z");
		$svg->appendChild($path);
		
		$likes->appendChild($likes_counter);
		$likes->appendChild($svg);

		//creating comments
		$com_div = $doc->createElement('div');
		$com_div->setAttribute('class', 'comments');
		if ($comment_count) {
			foreach ($comments as $c) {
				$com = $doc->createElement('div');
				$com_cont = $doc->createElement('div');
				$com_cont->nodeValue = $c['content'];
				$com->setAttribute('class', 'comment');

				$com_info = $doc->createElement('div');
				$com_info->setAttribute('class', 'com_info');

				$com_time = $doc->createElement('div');
				$com_time->setAttribute('class', 'com_time');
				$com_time->nodeValue = $c['created_at'];
				
				$com_user = $doc->createElement('div');

				$com_user->setAttribute('class', 'com_user');
				$com_user->nodeValue = $c['username'];

				$com_info->appendChild($com_user);
				$com_info->appendChild($com_time);
				$com->appendChild($com_info);
				$com->appendChild($com_cont);
				$com_div->appendChild($com);
			}
		}
		
		$post_target->appendChild($image);
		$post_target->appendChild($likes);
		$com_target->appendChild($com_div);
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

	function sendMail($data, $type, $to) {

		$l = $type == "verification" ? "verify_email.php" : "password_reset.php";

		if ($data['type'] === 'token')
			$LINK = "http://$_SERVER[HTTP_HOST]/$l?token=" . bin2hex($data['value']);
		
		switch ($type) {
			case "verification":
				$subject = "Camagru email verification";
				$message = "validate signup with this link\n\n\t$LINK";
				break;
			case "password_reset":
				$subject = "Camagru password reset";
				$message = "confirm password reset with this link\n\n\t$LINK";
				break;
			case "new_comment":
				$subject = "new comment";
				$message = "you've received a new comment from ". 
				$data['value']['username']."\n\n".$data['value']['comment_text'];
		}

		$result = mail($to, $subject, $message);
		if (!$result) {
			alert("Mail failed");
			error_log(error_get_last());
		}
	}
