<?php
	require("initialize.php");
	$pagename = "利用分析";
	require("header.php");

	if($user_id !== "1"){
		$error = "管理者ではありません";
		require("footer.php");
		require("destroy.php");
		exit();
	}

	$analytics = array();
	$query = "select * from activity_v natural join user_t"
		. " order by date desc, time desc";
	$result = mysql_query($query);
	for($i = 0; $row = mysql_fetch_array($result); $i++){
		if($row["favourited"] === "-1"){
			$query2 = "select * from favourited_activity_t"
				. " where favourited_id = {$row["favourited_id"]} and favourited > 0";
			$result2 = mysql_query($query2);
			$row2 = mysql_fetch_array($result2);
			$row["date"] = $row2["date"];
		}
		array_push($analytics, $row);
	}
?>
<section>
	
</section>
<table>
	<tr>
		<th>ユーザ</th>
		<th>日時</th>
		<th>キャラクター</th>
		<th>表示</th>
		<th>評価</th>
	</tr>
<?php
	foreach($analytics as $key => $value){
?>
		<tr>
			<td><?php echo $value["username"]; ?></td>
			<td><?php echo "{$value["date"]} {$value["time"]}"; ?></td>
			<td style="text-align:right;"><?php echo $value["character_id"]; ?></td>
			<td style="text-align:right;"><?php echo $value["used"]; ?></td>
			<td style="text-align:right;"><?php echo $value["favourited"]; ?></td>
		</tr>
<?php
	}
?>
</table>
<?php
	require("footer.php");
	require("destroy.php");
?>
