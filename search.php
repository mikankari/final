<?php
	$pagename = "検索";
	require("header.php");

	if(!isset($_SESSION["user_id"]) || !($user_id = $_SESSION["user_id"])){
		$error = "ログインしていません";
		require("footer.php");
		exit();
	}
	$db = mysql_connect($dbhost, $dbuser, $dbpassword);
	if(!$db || !mysql_select_db($dbname)){
		echo "データベースに接続できません";
		require("footer.php");
		exit();
	}

	if(strpos($path, "keyword=") !== false){
		$keyword = trim($_GET["keyword"]);
		$keyword = strip_tags($keyword);
		if($keyword === ""){
			header("Location: $root_path/search.php");
		}
?>
		<section>
			<h2>「<?= $keyword ?>」の検索結果</h2>
			<div>
<?php
				$query = "select * from character_t natural join user_t"
					. " where (name like '%$keyword%' or description like '%$keyword%' or username like '%$keyword%') and ispublic = 1"
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
					<div class="characterbox">見つかりませんでした</div>
<?php
				}
?>
			</div>
		</section>
<?php
	}else if(strpos($path, "user_id=") !== false){
		$user_id = trim($_GET["user_id"]);
		$user_id = strip_tags($user_id);
		$query = "select * from user_t"
			. " where user_id = $user_id";
		$result = mysql_query($query);
		$row = mysql_fetch_array($result);
		$username = $row["username"];
?>
		<section>
			<h2><?= $username ?>さんがつくったもの一覧</h2>
			<div>
<?php
				$query = "select * from character_t"
					. " where user_id = $user_id and ispublic = 1"
					. " order by character_id desc";
				$result = mysql_query($query);
				if(mysql_num_rows($result) !== 0){
					while($row = mysql_fetch_array($result)){
?>
						<a href="character.php?character_id=<?= $row["character_id"] ?>"><div class="characterbox">
							<img src="upload/<?= $row["character_id"] ?>/<?= $row["image_id"] ?>.png" alt="">
							<div class="name"><?= $row["name"] ?></div>
							<div class="user"><?= $username ?></div>
						</div></a>
<?php
					}
				}else{
?>
					<div class="characterbox">見つかりませんでした</div>
<?php
				}
?>
			</div>
		</section>
<?php
	}else{
?>
		<section>
			<h2>新着</h2>
			<div>
<?php
				$query = "select * from character_t natural join user_t"
					. " where ispublic = 1"
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
					<div class="characterbox">見つかりませんでした</div>
<?php
				}
?>
			</div>
		</section>
		<section>
			<h2>よく使われています</h2>
			<div>
<?php
				$query = "select * from character_t natural join user_t"
					. " where character_id in (select character_id from activity_t where used <> 0) and ispublic = 1";
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
					<div class="characterbox">見つかりませんでした</div>
<?php
				}
?>
			</div>
		</section>
		<section>
			<h2>よく好まれています</h2>
			<div>
<?php
				$query = "select * from character_t natural join user_t"
					. " where character_id in (select character_id from activity_t where favourited <> 0) and ispublic = 1";
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
					<div class="characterbox">見つかりませんでした</div>
<?php
				}
?>
			</div>
		</section>
<?php
	}
?>

<?php
	mysql_close($db);
	
	require("footer.php");
?>
