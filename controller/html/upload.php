<?php
	session_start();
	require_once 'functs.php';

	if (!isset($_SESSION['user']) || check_session($_SESSION['user'])) {
		header('location: /index.php');
		exit;
	}
	$pdo = new PDO(
		"mysql:host=model;dbname=camagru;charset=utf8",
		"camagru_admin",
		"camagru_admin_pass"
	);

	if (isset($_FILES['file-upload']["full_path"])) {
		$file = $_FILES['file-upload']["full_path"];
		echo "<br>";
		$path = pathinfo($file);
		$extensions = ['jpeg', 'jpg', 'gif'];
		//'extension' 'basename' 'filename' 'dirname'

		if ($path['extension'] )
		var_dump($path);
		$stmt = $pdo->prepare("INSERT INTO posts (user_id, image_path, created_at) 
		VALUES (:user_id, :image_path, :created_at)");
		$stmt->execute([":user_id"=>$_SESSION['user']['id'],
		":image_path"=>$file,
		":created_at"=>date("Y-m-d H:i:s")]);

		header('location: /home.php');
		exit ;
	}


	// $stmt = $pdo->prepare('SELECT posts.*, users.username, COUNT(DISTINCT likes.user_id) as like_count
	// FROM posts LEFT JOIN users ON posts.user_id = users.id LEFT JOIN likes ON posts.id = likes.post_id
	// GROUP BY posts.id ORDER BY posts.created_at DESC');
	// $stmt->execute();
	// $posts = $stmt->fetch();

	
	$doc = new DOMDocument();
	$doc->loadHTMLFile('home.html');
	
	echo $doc->saveHTML();
	// if ($posts) var_dump($posts);

	
