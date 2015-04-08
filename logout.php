<?php
	require("initialize.php");
	$pagename = "ログアウト";
	require('header.php');

	$_SESSION["user_id"] = false;
	$_SESSION["session_id"] = false;
	$_SESSION["ticket"] = false;
	$_SESSION["remote"] = false;
	session_regenerate_id();

	header("Location: $root_path/");

	require("footer.php");
	require("destroy.php");
?>
