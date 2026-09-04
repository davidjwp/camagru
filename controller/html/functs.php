<?php
	
function post($doc, $post, $comments, $comment_count, $liked, $is_user) {
    $post_target = $doc->getElementById('post');
    $com_target = $doc->getElementById('comments');

    // --- Post Meta (user info) ---
    $meta = $doc->createElement('div');
    $meta->setAttribute('class', 'post-meta');

    $avatar = $doc->createElement('div');
    $avatar->setAttribute('class', 'avatar');
    $avatar->nodeValue = strtoupper(substr($post[0]['username'], 0, 1));
    $meta->appendChild($avatar);

    $username = $doc->createElement('span');
    $username->setAttribute('class', 'username');
    $username->nodeValue = $post[0]['username'];
    $meta->appendChild($username);

    $time = $doc->createElement('span');
    $time->setAttribute('class', 'post-time');
    $time->nodeValue = date('M j, Y', strtotime($post[0]['created_at']));
    $meta->appendChild($time);

    $post_target->appendChild($meta);

    // --- Image ---
    $image = $doc->createElement('img');
    $image->setAttribute('class', 'post-image');
    $image->setAttribute('src', '/uploads/' . $post[0]['image_path']);
    $post_target->appendChild($image);

    // --- Actions (likes + delete) ---
    $actions = $doc->createElement('div');
    $actions->setAttribute('class', 'post-actions');

    $like_btn = $doc->createElement('button');
    $like_btn->setAttribute('class', 'like-btn');
    $like_btn->setAttribute('id', 'like-btn');
    $like_btn->setAttribute('onclick', "toggleLike(" . $post[0]['id'] . "," . ($liked ? 'true' : 'false') . "," . $post[0]['like_count'] . ")");

    // Heart SVG
    $svg = $doc->createElementNS('http://www.w3.org/2000/svg', 'svg');
    $svg->setAttribute('class', 'heart' . ($liked ? ' liked' : ''));
    $svg->setAttribute('id', 'heart' . $post[0]['id']);
    $svg->setAttribute('width', '28');
    $svg->setAttribute('height', '28');
    $svg->setAttribute('viewBox', '0 0 24 24');
    $svg->setAttribute('fill', $liked ? '#e53e3e' : 'none');
    $svg->setAttribute('stroke', '#e53e3e');
    $svg->setAttribute('stroke-width', '2');

    $path = $doc->createElement('path');
    $path->setAttribute('d', 'M12 21.35l-1.45-1.32C5.4 15.36 2 12.28 2 8.5 2 5.42 4.42 3 7.5 3c1.74 0 3.41.81 4.5 2.09C13.09 3.81 14.76 3 16.5 3 19.58 3 22 5.42 22 8.5c0 3.78-3.4 6.86-8.55 11.54L12 21.35z');
    $svg->appendChild($path);

    $like_btn->appendChild($svg);

    $like_count = $doc->createElement('span');
    $like_count->setAttribute('class', 'like-count');
    $like_count->setAttribute('id', 'likes_counter');
    $like_count->nodeValue = $post[0]['like_count'] . ' likes';
    $like_btn->appendChild($like_count);

    $actions->appendChild($like_btn);

    if ($is_user) {
        $del = $doc->createElement('button');
        $del->setAttribute('class', 'delete-btn');
        $del->setAttribute('onclick', 'del()');
        $del->nodeValue = '🗑 Delete';
        $actions->appendChild($del);
    }

    $post_target->appendChild($actions);

    // --- Comments ---
    if ($comment_count) {
        foreach ($comments as $c) {
            $com = $doc->createElement('div');
            $com->setAttribute('class', 'comment');

            $info = $doc->createElement('div');
            $info->setAttribute('class', 'com_info');

            $user = $doc->createElement('span');
            $user->setAttribute('class', 'com_user');
            $user->nodeValue = $c['username'];
            $info->appendChild($user);

            $time = $doc->createElement('span');
            $time->setAttribute('class', 'com_time');
            $time->nodeValue = date('M j, Y g:i A', strtotime($c['created_at']));
            $info->appendChild($time);

            $content = $doc->createElement('div');
            $content->setAttribute('class', 'com_cont');
            $content->nodeValue = htmlspecialchars($c['content']);

            $com->appendChild($info);
            $com->appendChild($content);
            $com_target->appendChild($com);
        }
    } else {
        $empty = $doc->createElement('div');
        $empty->setAttribute('class', 'no-comments');
        $empty->nodeValue = 'No comments yet. Be the first!';
        $com_target->appendChild($empty);
    }
}
	function addStickers($stickers, $target, $doc) {
		foreach ($stickers as $sticker) {
			$filename = basename($sticker);
			$img = $doc->createElement('img');
			$img->setAttribute('src', '/Stickers/' . $filename);
			$img->setAttribute('class', 'sticker');
			$img->setAttribute('onclick', "selectSticker(this, '/Stickers/$filename')");

			$target->appendChild($img);
			
		}
	}

	function DOMerror($msg, $doc) {
		$target = $doc->getElementById('upload') ;

		$err = $doc->createElement('div') ;
		$err->setAttribute('class', 'error');
		$err->nodeValue = $msg;
		$target->appendChild($err);
	}

	function alert($msg) {echo "<script>alert('Error: ".$msg."');</script>";}

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