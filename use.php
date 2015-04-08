<?php
	require("initialize.php");
	$pagename = "キャラ名";
	require("header.php");

	$character_id = trim($_GET["character_id"]);
	$character_id = strip_tags($character_id);
	$query = "select * from character_t"
		. " where character_id = $character_id";
	$result = mysql_query($query);
	$row = mysql_fetch_array($result);
	if($row["user_id"] !== "$user_id" && $row["ispublic"] === "0"){
		$error = "キャラクターは非公開です";
		require("footer.php");
		require("destroy.php");
		exit();
	}
// 作者自身が表示した場合
//	if("$user_id" === row["user_id"]){
		$query = "insert into used_activity_t (user_id, date, time, character_id, used)"
			. " values ($user_id, CURRENT_DATE, CURRENT_TIME, $character_id, 1)";
		$result = mysql_query($query);
//	}
?>
<script type="text/javascript">
	var final = {};
	final.patterns = null;
	final.current_index = 0;
	final.timer = null;
	final.init = function (event){
		var character_img = document.querySelector(".character img");
		var character_textarea = document.querySelector(".character .balloon");
		var loader = new XMLHttpRequest();
		character_img.addEventListener("load", function (event){
			final.updateMessage();
		}, false);
		loader.addEventListener("load", function (event){
			if(loader.readyState === 4){
				if(loader.status === 200){
					final.patterns = JSON.parse(loader.responseText);
					if(final.patterns.error){
						character_textarea.innerHTML = "読み込み失敗（" + final.patterns.error + "）";
					}else if(final.patterns.length === 0){
						character_textarea.innerHTML = "読み込み失敗（パターンが１つもありません）"
					}else{
						final.intervalMessage();
						final.timer = window.setInterval(final.intervalMessage, 6000);
					}
				}else{
					character_textarea.innerHTML = "読み込み失敗（キャラクターデータにアクセスできません）";
				}
			}
		}, false);
		loader.open("GET", "patterns.php?character_id=<?php echo $character_id; ?>", true);
		loader.send();
		character_textarea.innerHTML = "読み込み中...";
	}
	final.destroy = function (){
		window.clearInterval(final.timer);
		final.updateAnalytics();
	}
	final.intervalMessage = function (){
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
	final.updateAnalytics = function (){
		var loader = new XMLHttpRequest();
		loader.open("GET", "usedestroy.php?character_id=<?php echo $character_id; ?>&used_id=<?php echo mysql_insert_id(); ?>", false);
		loader.send();
	}

	window.addEventListener("DOMContentLoaded", final.init, false);
	window.addEventListener("beforeunload", final.destroy, false);
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
	require("footer.php");
	require("destroy.php");
?>
