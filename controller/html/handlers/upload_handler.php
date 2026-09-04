<?php
	require_once '../functs.php';
	session_start();

	if (!isset($_SESSION['user'])) {
		header('location: ../index.php');
		exit;
	}

	//get doc and put down errors due to DOMDoc php ver
	$doc = new DOMDocument();
	libxml_use_internal_errors(true);
	$doc->loadHTMLFile("../editor.html");
	libxml_clear_errors();
	$stickers = glob('/var/www/html/Stickers/*.png');
	$target = $doc->getElementById("stickers");
		
	$pdo = new PDO(
		"mysql:host=model;dbname=camagru;charset=utf8",
		"camagru_admin",
		"camagru_admin_pass"
		);

	if (isset($_POST['selected_thumbnail']) && $_POST['csrf-token'] === $_SESSION['csrf-token']) {
		$filename = basename($_POST['selected_thumbnail']);
		$file_path = $_SESSION['tmp_dir'].'/'.$filename;

		if (file_exists($file_path) && filesize($file_path) >= 10000000 ) {
			DOMerror('file too big (max 10MB)', $doc);
			addStickers($stickers, $target, $doc);
			echo $doc->saveHTML();
			exit ;
		}
		else if (!file_exists($file_path)) {
			DOMerror('file not present', $doc);
			addStickers($stickers, $target, $doc);
			echo $doc->saveHTML();
			exit ;
		}

		$finfo = finfo_open(FILEINFO_MIME_TYPE);
		$mime = finfo_file($finfo, $file_path);
		$path = pathinfo($file_path);
		$allowed_mimes = ['image/jpeg', 'image/png', 'image/gif'];

		if (in_array($mime, $allowed_mimes)) {
			$filename = bin2hex(random_bytes(16)).'.'.$path['extension'];
			$dest = '/var/www/html/uploads/' . $filename;
			$tmp_fil = $_SESSION['tmp_dir'] . '/'. $_POST['selected_thumbnail'];

			if (!copy($tmp_fil, $dest)) {
				DOMerror('upload failed', $doc);
				addStickers($stickers, $target, $doc);
				echo $doc->saveHTML();
				exit ;
			}

			$stmt = $pdo->prepare("INSERT INTO posts (user_id, image_path) 
			VALUES (:user_id, :image_path)");
			$stmt->execute([
			":user_id"=>$_SESSION['user']['id'],
			":image_path"=>$filename]);
		}
		else {
			DOMerror("only jpeg, jpg and png's are supported", $doc); 
			addStickers($stickers, $target, $doc);
			echo $doc->saveHTML();
			exit ;
		}
	}
	else {
		if (isset($_POST['selected_thumbnail']) && $_POST['csrf-token'] !== $_SESSION['csrf-token'])
			DOMError('wrong csrf token', $doc);
		else 
			DOMError('no thumbnail selected', $doc);
		
		addStickers($stickers, $target, $doc);
		echo $doc->saveHTML();
		exit;
	}
	addStickers($stickers, $target, $doc);
	echo $doc->saveHTML();