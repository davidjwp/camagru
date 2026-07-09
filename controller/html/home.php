<?php
	session_start();

	$doc = new DOMDocument;
	$doc->loadHTMLFile('home.html');

	$doc->getElementById('profile_button')->innerHTML = "HEYYEY";
	var_dump($button);
