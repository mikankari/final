<?php
	$pagename = "マイページ";
	require("header.php");

	if(!isset($_SESSION["user_id"]) || !($user_id = $_SESSION["user_id"])){
		$error = "ログインしていません";
		require("footer.php");
		exit();
	}
	$db = mysql_connect($dbhost, $dbuser, $dbpassword);
	if(!$db || !mysql_select_db($dbname)){
		$error = "データベースに接続できませんでした";
		require("footer.php");
		exit();
	}
?>

<script type="text/javascript">
	var final = {};
	final.init = function (event){
		var logout_button = document.getElementById("logout_button");
		logout_button.addEventListener("click", function (event){
			window.location.href = "logout.php";
		}, false);
	}

	window.addEventListener("DOMContentLoaded", final.init, false);
</script>

<section>
	<h2>お知らせ</h2>
	<div>
<?php
		$query = "select * from activity_t natural join user_t"
			. " where character_id in (select character_id from character_t where user_id = $user_id) and user_id <> $user_id"
			. " order by character_id desc";
		$result = mysql_query($query);
		if(mysql_num_rows($result) !== 0){
?>
			<ul>
<?php
				while($row = mysql_fetch_array($result)){
					$query2 = "select * from character_t"
						. " where character_id = {$row["character_id"]}";
					$result2 = mysql_query($query2);
					$row2 = mysql_fetch_array($result2);
					$content = "{$row["username"]}さん";
					if($row["used"] === "1"){
						$content .= "に「{$row2["name"]}」を使われました";
					}else{
						$content .= "が「{$row2["name"]}」に★{$row["favourited"]}つの評価をしました";
					}
					date_default_timezone_set("Asia/Tokyo");
					$ago = strtotime("now") - strtotime($row["date"]);
					if($ago < 60){
						$content .= " {$ago}秒前";
					}else if($ago < 3600){
						$ago = round($ago / 60);
						$content .= " {$ago}分前";
					}else if($ago < 86400){
						$ago = round($ago / 3600);
						$content .= " {$ago}時間前";
					}else{
						$ago = round($ago / 86400);
						$content .= " {$ago}日前";
					}
?>
					<li><?= $content ?></li>
<?php
				}
?>
			</ul>
<?php
			}else{
?>
				<div>お知らせはありません</div>
<?php
			}
?>
	</div>
</section>

<section>
	<h2>つくったもの一覧</h2>
	<div>
<?php
		$query = "select * from character_t natural join user_t"
			. " where user_id = $user_id and ispublic = 1"
			. " order by character_id desc";
		$result = mysql_query($query);
		if(mysql_num_rows($result) !== 0){
			while($row = mysql_fetch_array($result)){
?>
				<a href="character.php?character_id=<?= $row["character_id"] ?>"><div class="characterbox">
					<img src="upload/<?= $row["character_id"] ?>/<?= $row["image_id"] ?>.png" alt="">
					<div class="name"><?= $row["name"] ?></div>
					<div class="user"><?= $row["username"] ?></div>
				</div></a>
<?php
			}
		}else{
?>
			<div>まだつくっていません</div>
<?php
		}
?>
	</div>
</section>

<section>
	<h2>つくったが公開していないもの一覧</h2>
	<div>
<?php
		$query = "select * from character_t natural join user_t"
			. " where user_id = $user_id and ispublic = 0"
			. " order by character_id desc";
		$result = mysql_query($query);
		if(mysql_num_rows($result) !== 0){
			while($row = mysql_fetch_array($result)){
?>
				<a href="character.php?character_id=<?= $row["character_id"] ?>"><div class="characterbox">
					<img src="upload/<?= $row["character_id"] ?>/<?= $row["image_id"] ?>.png" alt="">
					<div class="name"><?= $row["name"] ?></div>
					<div class="user"><?= $row["username"] ?></div>
				</div></a>
<?php
			}
		}else{
?>
			<div>まだつくっていません</div>
<?php
		}
?>
	</div>
</section>

<section>
	<h2>あなたの情報</h2>
	<div>
		<div class="table">
<?php
			$query = "select * from user_t"
				. " where user_id = $user_id";
			$result = mysql_query($query);
			$row = mysql_fetch_array($result);
?>
			<div><div>ユーザ名</div><div><?= $row["username"] ?></div></div>
			<div><div>パスワード</div><div>（表示されません）</div></div>
		</div>
		<button id="logout_button">ログアウト</button>
	</div>
</section>

<?php
	mysql_close($db);

	require("footer.php");
?>
