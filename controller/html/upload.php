<?php
	session_start();
	require_once 'functs.php';
	
	$doc = new DOMDocument();
	$doc->loadHTMLFile('home.html');
	
	if (!isset($_SESSION['user']) || check_session($_SESSION['user'])) {
		header('location: /index.php');
		exit;
	}
	
	$pdo = new PDO(
		"mysql:host=model;dbname=camagru;charset=utf8",
		"camagru_admin",
		"camagru_admin_pass"
	);

	if (empty($_FILES)) {
		DOMerror('image size limit 10M', $doc);
		echo $doc->saveHTML();
		exit;
	}


	if (isset($_FILES['file-upload'])) {
		$path = pathinfo($_FILES['file-upload']["full_path"]);
		$extensions = ['jpeg', 'jpg', 'png'];

		if (in_array($path['extension'], $extensions)) {
			$filename = bin2hex(random_bytes(16)).'.'.$path['extension'];
			$dest = '/var/www/html/uploads/' . $filename;
			$tmp_fil = $_FILES['file-upload']['tmp_name'];

			if (!move_uploaded_file($tmp_fil, $dest)) {
				DOMerror('upload failed', $doc);
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
			echo $doc->saveHTML();
			exit ;
		}
		header('location: /home.php');
		exit ;
	}
	echo $doc->saveHTML();