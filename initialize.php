<?php
	$appname = 'appname';
	$root_path = '/mmdb2/final';
	$path = str_replace($root_path, '', $_SERVER["REQUEST_URI"]);
	if($path === "index.php"){
		$path = "/";
	}
	$image_path = 'C:/xampp/htdocs/mmdb2/final/upload';
	$image_maxsize = 3145728;
	$dbname = 'final_db';
	$dbhost = 'localhost';
	$dbuser = 's1223066';
	$dbpassword = 'yome';
	$create_screen = "投稿する";
	$use_screen = "投稿一覧";

	session_start();

	if($path !== "/" && $path !== "login.php" && $path !== "logout.php"){
		if(!isset($_SESSION["user_id"]) || !($user_id = $_SESSION["user_id"])){
			require("header.php");
			$error = "ログインしていません";
			require("footer.php");
			exit();
		}
	}

	$db = mysql_connect($dbhost, $dbuser, $dbpassword);
	if(!($db && mysql_select_db($dbname))){
		require("header.php");
		$error = "データベースに接続できません";
		require("footer.php");
		exit();
	}
?>
