<?php
	require("initialize.php");
	$pagename = "ログインしています…";
	require("header.php");

	$username = trim($_POST["username"]);
	if(strpos($username, "<") !== false || strpos($username, ">") !== false){
		$error = "ユーザ名に使用できない記号が含まれています";
		require("footer.php");
		require("destroy.php");
		exit();
	}
	$username = strip_tags($username);
	$password = trim($_POST["password"]);
	$password = strip_tags($password);
	$goto = trim($_POST["goto"]);
	$goto = strip_tags($goto);

	if($username == "" || $password == ""){
		$error = "ユーザ名またはパスワードが空欄です";
		require("footer.php");
		require("destroy.php");
		exit();
	}

	Function GetEscaped($s) {
		if (get_magic_quotes_gpc()) $s=stripslashes($s);   // magic_quotes_gpc = Onの場合はまずエスケープされた文字をもとに戻しておく（H25）
		return addslashes($s);
	}

	$session_id = GetEscaped(trim($_POST["session_id"]));
	$session_id = strip_tags($session_id);
	$ticket = GetEscaped(trim($_POST["ticket"]));
	$ticket = strip_tags($ticket);
	$remote = $_SERVER["REMOTE_ADDR"];

	if((empty($session_id) || empty($ticket) || empty($remote))
	  || ($_SESSION["session_id"] != $session_id || $_SESSION["ticket"] != $ticket || $_SESSION["remote"] != $remote)){
		$error = "トップページからログインしてください";
		require("footer.php");
		require("destroy.php");
		exit();
	}

	if(isset($_POST["confirm"])){
		$confirm = trim($_POST["confirm"]);
		$confirm = strip_tags($confirm);
		if($password !== $confirm){
			$error = "パスワードとパスワードの確認が一致しません";
			require("footer.php");
			require("destroy.php");
			exit();
		}
		$password = sha1($password);
		$query = "insert into user_t (username, password)"
			. " values ('$username', '$password')";
		$result = mysql_query($query);
		if(!$result){
			$error = "ユーザ名が既に使われています";
			require("footer.php");
			require("destroy.php");
			exit();
		}
		session_regenerate_id();
		$_SESSION["user_id"] = mysql_insert_id();
		header("Location: $root_path/$goto.php");
	}else{
		$password = sha1($password);
		$query = "select * from user_t"
			. " where username = '$username' and password = '$password'";
		$result = mysql_query($query);
		$row = mysql_fetch_array($result);
		if(mysql_num_rows($result) !== 0){
			session_regenerate_id();
			$_SESSION["user_id"] = $row["user_id"];
			header("Location: $root_path/$goto.php");
		}else{
			$error = "ログインできません。ユーザ名またはパスワードが違います";
			require("footer.php");
			require("destroy.php");
			exit();
		}
	}

	require("footer.php");
	require("destroy.php");
?>
