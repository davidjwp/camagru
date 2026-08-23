<?php
	session_start();
	require_once '../functs.php';
	
	//get doc and put down errors due to DOMDoc php ver
	$doc = new DOMDocument();
	libxml_use_internal_errors(true);
	$doc->loadHTMLFile("../editor.html");
	libxml_clear_errors();
	$stickers = glob('/var/www/html/Stickers/*.png');
	$target = $doc->getElementById("sticker-footer");
	
	if (!isset($_SESSION['user'])) {
		header('location: ../index.php');
		exit;
		}
		
		$pdo = new PDO(
			"mysql:host=model;dbname=camagru;charset=utf8",
			"camagru_admin",
			"camagru_admin_pass"
			);
			
	if (isset($_POST['selected_thumbnail'])) {
		$path = pathinfo($_SESSION['tmp_dir'] . '/' . $_POST['selected_thumbnail']);
		$extensions = ['jpeg', 'jpg', 'png'];

		if (in_array($path['extension'], $extensions)) {
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
		DOMError('no thumbnail selected', $doc);
		addStickers($stickers, $target, $doc);
		echo $doc->saveHTML();
		exit;
	}
	addStickers($stickers, $target, $doc);
	echo $doc->saveHTML();