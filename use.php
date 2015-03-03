<?php
	$pagename = "キャラ名";
	require("header.php");

	if(!isset($_SESSION["user_id"]) || !($user_id = $_SESSION["user_id"])){
		$error = "ログインしていません";
		require("footer.php");
		exit();
	}
	$character_id = trim($_GET["character_id"]);
	$character_id = strip_tags($character_id);
	$db = mysql_connect($dbhost, $dbuser, $dbpassword);
	if(!$db || !mysql_select_db($dbname)){
		echo "データベースに接続できません";
		require("footer.php");
		exit();
	}
	$query = "select * from character_t"
		. " where character_id = $character_id";
	$result = mysql_query($query);
	$row = mysql_fetch_array($result);
	if($row["ispublic"] === "0"){
		echo "キャラクターは非公開です";
		require("footer.php");
		exit();
	}
//	if("$user_id" === row["user_id"]){
		$query = "insert into activity_t (user_id, date, character_id, used, favourited)"
			. " values ($user_id, now(), $character_id, 1, 0)";
		$result = mysql_query($query);
		$query = "update character_t"
			. " set use_count = (select count(used) from activity_t where character_id = $character_id and used <> 0)"
			. " where character_id = $character_id";
		$result = mysql_query($query);
//	}
	$query = "select * from patterns_t natural join image_t"
		. " where character_id = $character_id";
	$result = mysql_query($query);
?>
<script type="text/javascript">
	var final = {};
	final.patterns = [];
<?php
	while($row = mysql_fetch_array($result)){
?>
		final.patterns.push({
			url: "upload/<?= $row["character_id"] ?>/<?= $row["image_id"] ?>.png",
			message: "<?= $row["message"] ?>"
		});
<?php
	}
?>
	final.current_index = 0;
	final.timer = null;
	final.init = function (event){
		var character_img = document.querySelector(".character img");
		character_img.addEventListener("load", function (event){
			final.updateMessage();
		}, false);
		final.interval();
		final.timer = window.setInterval(final.interval, 3000);
	}
	final.destroy = function (){
		window.clearInterval(final.timer);
	}
	final.interval = function (){
		var character_img = document.querySelector(".character img");
		final.current_index = Math.floor(Math.random() * final.patterns.length);
		var pattern = final.patterns[final.current_index];
		character_img.src = pattern.url;
	}
	final.updateMessage = function (){
		var character_textarea = document.querySelector(".character .balloon");
		var pattern = final.patterns[final.current_index];
		character_textarea.innerHTML = pattern.message;
	}

	window.addEventListener("DOMContentLoaded", final.init, false);
</script>
<div class="usebox">
<div class="character">
	<div id="image"><img src="" alt=""></div>
	<div id="message"><div class="balloon"></div></div>
</div>
</div>
<div class="hintbox">
<a href="use_on_native.php">使い方のヒント：PCやスマホに表示することもできます。</a>
</div>

<?php
	mysql_close($db);
	
	require("footer.php");
?>
