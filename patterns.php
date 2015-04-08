<?php
	require("initialize.php");
	header("Content-Type: application/json; charset=utf-8");

	$character_id = trim($_GET["character_id"]);
	$character_id = strip_tags($character_id);
	$query = "select * from character_t natural join user_t"
		. " where character_id = $character_id";
	$result = mysql_query($query);
	if(mysql_num_rows($result)){
		$row = mysql_fetch_array($result);
		if($row["user_id"] !== "$user_id" && $row["ispublic"] === "0"){
			$error = "キャラクターは非公開です";
		}
	}else{
		$error = "キャラクターは存在しません";
	}

	$output = "";
	if(!isset($error)){
		$output .= "[";
		$query = "select * from patterns_t natural join image_t"
			. " where character_id = $character_id";
		$result = mysql_query($query);
		if(mysql_num_rows($result)){
			while($row = mysql_fetch_array($result)){
				$output .= "{\"url\":\"upload/{$row["character_id"]}/{$row["image_id"]}.png\", "
					. "\"message\":\"{$row["message"]}\"},";
			}
			$output = substr($output, 0, strlen($output) - 1);
		}
		$output .= "]";
	}else{
		$output .= "{error:\"$error\"}";
	}
	echo $output;

	require("destroy.php");
?>
