<?php
	/*
	 * Custom spell checker [ speller ]
	 * https://github.com/sadreck/speller
	 *
	 * Copyright 2013, Pavel Tsakalidis [ p@vel.gr ]
	 * http://www.pavel.gr
	 *
	 * Licensed under the MIT license:
	 * http://www.opensource.org/licenses/MIT
	*/
	
	/*
		Change these so they match your server
	*/
	
	define('DB_HOST', '127.0.0.1');
	define('DB_USER', 'pavel');
	define('DB_PASS', '1234');
	define('DB_NAME', 'speller');
	
	define('SENSITIVITY', 1);
	
	$return = array('success' => false, 'correct' => false, 'message' => '', 'suggest' => array());
	
	/*
		Let's connect to the database.
	*/
	$link = @mysql_connect(DB_HOST, DB_USER, DB_PASS);
	if (!$link) {
		$return['message'] = "Could not connect to database:\n\n" . mysql_error();
		echo json_encode($return); die();
	}
	
	$q = @mysql_select_db(DB_NAME, $link);
	if (!$q) {
		$return['message'] = "Could not select database:\n\n" . mysql_error();
		echo json_encode($return); die();
	}
	
	$word = isset($_GET['word']) ? trim($_GET['word']) : '';
	if (empty($word) || is_array($word)) {
		$return['message'] = "No input word.";
		echo json_encode($return); die();
	}
	
	/*
		Let's see if the word is valid
	*/
	$q = @mysql_query("SELECT id FROM dictionary WHERE word = '". mysql_real_escape_string($word, $link) ."' LIMIT 1");
	if (!$q) {
		$return['message'] = "Query error:\n\n" . mysql_error();
		echo json_encode($return); die();
	}
	
	/*
		If the word exists do not proceed.
	*/
	if (mysql_num_rows($q) == 1) {
		$return['success'] = true;
		$return['correct'] = true;
		echo json_encode($return); die();
	}
	
	$q = @mysql_query("SELECT word FROM dictionary WHERE levenshtein('". mysql_real_escape_string($word, $link) ."', word) <= " . SENSITIVITY . " ORDER BY word");
	if (!$q) {
		$return['message'] = "Query error:\n\n" . mysql_error();
		echo json_encode($return); die();
	}
	
	while ($row = mysql_fetch_array($q)) {
		$return['suggest'][] = $row['word'];
	}
	$return['success'] = true;
	echo json_encode($return); die();