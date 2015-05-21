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

	$used_activities = array();
	$usedperiod_activities = array();
	$query = "select * from used_activity_t natural join user_t"
		. " order by used_id desc, date asc, time asc";
	$result = mysql_query($query);
	while($row = mysql_fetch_array($result)){
		if($row["used"] === "-1"){
			$query2 = "select * from used_activity_t"
				. " where used_id = {$row["used_id"]} and used > 0";
			$result2 = mysql_query($query2);
			if(mysql_num_rows($result2) !== 0){
				$row2 = mysql_fetch_array($result2);
				$interval = date_diff(date_create($row2["time"]), date_create($row["time"]));
//				$row["used_id"] = "└";
				$row["date"] = "";
				$row["time"] = $interval->format("%H:%I:%S");
				array_push($usedperiod_activities, $row);
			}else{
				array_push($used_activities, $row);
			}
		}else{
			array_push($used_activities, $row);
		}
	}
	$favourited_activities = array();
	$query = "select * from favourited_activity_t natural join user_t"
		. " where favourited > 0"
		. " order by favourited_id desc, date asc, time asc";
	$result = mysql_query($query);
	while($row = mysql_fetch_array($result)){
//		if($row["favourited"] === "-1"){
//			$query2 = "select * from favourited_activity_t"
//				. " where favourited_id = {$row["favourited_id"]} and favourited > 0";
//			$result2 = mysql_query($query2);
//			if($result2){
//				$row2 = mysql_fetch_array($result2);
//				$interval = date_diff(date_create($row2["time"]), date_create($row["time"]));
//				$row["favourited_id"] = "└";
//				$row["date"] = "";
//				$row["time"] = $interval->format("%R%H:%I:%S");
//			}
//		}
		array_push($favourited_activities, $row);
	}
?>
<section>
	<h2>表示したユーザ</h2>
	<div>
		<table>
			<tr>
				<th>ID</th>
				<th>ユーザ</th>
				<th>日時</th>
				<th>キャラクター</th>
			</tr>
		<?php
			$file = fopen("logs/used_activities.csv", "w");
			foreach($used_activities as $key => $value){
		?>
				<tr>
					<td style="text-align:right;"><?php echo $value["used_id"]; ?></td>
					<td><?php echo $value["username"]; ?></td>
					<td><?php echo "{$value["date"]} {$value["time"]}"; ?></td>
					<td style="text-align:right;"><?php echo $value["character_id"]; ?></td>
				</tr>
		<?php
				fwrite($file, "{$value["used_id"]},{$value["username"]},{$value["date"]},{$value["time"]},{$value["character_id"]}\n");
			}
			fclose($file);
		?>
		</table>
	</div>
</section>
<section>
	<h2>表示した時間</h2>
	<div>
		<table>
			<tr>
				<th>ID</th>
				<th>ユーザ</th>
				<th>使用時間</th>
				<th>キャラクター</th>
			</tr>
		<?php
			$file = fopen("logs/usedperiod_activities.csv", "w");
			foreach($usedperiod_activities as $key => $value){
		?>
				<tr>
					<td style="text-align:right;"><?php echo $value["used_id"]; ?></td>
					<td><?php echo $value["username"]; ?></td>
					<td><?php echo "{$value["time"]}"; ?></td>
					<td style="text-align:right;"><?php echo $value["character_id"]; ?></td>
				</tr>
		<?php
				fwrite($file, "{$value["used_id"]},{$value["time"]},{$value["character_id"]}\n");
			}
			fclose($file);
		?>
		</table>
	</div>
</section>
<section>
	<h2>評価したユーザ</h2>
	<div>
		<table>
			<tr>
				<th>ID</th>
				<th>ユーザ</th>
				<th>日時</th>
				<th>キャラクター</th>
				<th>評価</th>
			</tr>
		<?php
			$file = fopen("logs/favourited_activities.csv", "w");
			foreach($favourited_activities as $key => $value){
		?>
				<tr>
					<td style="text-align:right;"><?php echo $value["favourited_id"]; ?></td>
					<td><?php echo $value["username"]; ?></td>
					<td><?php echo "{$value["date"]} {$value["time"]}"; ?></td>
					<td style="text-align:right;"><?php echo $value["character_id"]; ?></td>
					<td style="text-align:right;"><?php echo $value["favourited"]; ?></td>
				</tr>
		<?php
				fwrite($file, "{$value["favourited_id"]},{$value["username"]},{$value["date"]},{$value["time"]},{$value["character_id"]},{$value["favourited"]}\n");
			}
			fclose($file);
		?>

		</table>
	</div>
</section>
<?php
	require("footer.php");
	require("destroy.php");
?>
