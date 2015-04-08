<?php
	require("initialize.php");
	header("Content-Type: application/json; charset=utf-8");

	$character_id = trim($_GET["character_id"]);
	$character_id = strip_tags($character_id);
	$used_id = trim($_GET["used_id"]);
	$used_id = strip_tags($used_id);
	$query = "insert into used_activity_t (used_id, user_id, date, time, character_id, used)"
		. " values ($used_id, $user_id, CURRENT_DATE, CURRENT_TIME, $character_id, -1)";
	$result = mysql_query($query);
	if(!$result){
		$error = "内部エラー";
	}

	$output = "";
	$output .= "[";
	if(isset($error)){
		$output .= "{error:\"$error\"}";
	}
	$output .= "]";
	echo $output;
?>
