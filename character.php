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
	$query = "select * from character_t natural join user_t"
		. " where character_id = $character_id";
	$result = mysql_query($query);
	if(mysql_num_rows($result)){
		$row = mysql_fetch_array($result);
		if($row["user_id"] !== $user_id && $row["ispublic"] === "0"){
			echo "キャラクターは非公開です";
			require("footer.php");
			exit();
		}
		$query2 = "select count(*) as 'use_count' from activity_t"
			. " where used <> 0 and character_id = $character_id";
		$result2 = mysql_query($query2);
		$row2 = mysql_fetch_array($result2);
		$row["use_count"] = $row2["use_count"];
		$query2 = "select avg(favourited) as 'favourite_average' from activity_t"
			. " where favourited <> 0 and character_id = $character_id";
		$result2 = mysql_query($query2);
		$row2 = mysql_fetch_array($result2);
		$row["favourite_average"] = round($row2["favourite_average"], 1);
	}else{
		echo "キャラクターは存在しません";
		require("footer.php");
		exit();
	}

	if(isset($_POST["favourite"])){
		$favourited = $_POST["favourite"];
		$favourited = strip_tags($favourited);
		$query = "insert into activity_t (user_id, date, character_id, used, favourited)"
			. " values ($user_id, CURRENT_TIMESTAMP, $character_id, 0, $favourited)";
		$result = mysql_query($query);
	}
?>
<script type="text/javascript">
	var final = {};
	final.init = function (event){
		var back_button = document.getElementById("back_button");
		var use_button = document.getElementById("use_button");
		var edit_button = document.getElementById("create_button");
		var use_image = document.getElementById("use_image");
		var favourite_image = document.getElementsByName("favourite_image");
		var mouseenterFavourite = function (event){
			final.hoverFavouriteImage(event.target, true);
		};
		var mouseoutFavourite = function (event){
			final.hoverFavouriteImage(event.target, false);
		};
		var clickFavourite = function (event){
			var reader;
			var i;
			reader = new XMLHttpRequest();
			reader.open("POST", window.location.href, true);
			reader.setRequestHeader("Content-Type", "application/x-www-form-urlencoded");
			reader.send("favourite=" + event.target.getAttribute("data-index"));
			for(i = 0; i < favourite_image.length; i++){
				favourite_image.item(i).removeEventListener("mouseenter", mouseenterFavourite);
				favourite_image.item(i).removeEventListener("mouseout", mouseoutFavourite);
				favourite_image.item(i).removeEventListener("click", clickFavourite);
			}
			final.showFeedbackMessage("評価が完了しました")
		};
		var i;
		
		back_button.addEventListener("click", function (event){
			window.history.back();
		}, false);
		use_button.addEventListener("click", function (event){
			window.location.href = "use.php?character_id=" + <?= $row["character_id"] ?>;
		}, false);
		if(edit_button){
			edit_button.addEventListener("click", function (event){
				window.location.href = "create.php?character_id=" + <?= $row["character_id"] ?>;
			}, false);
		}
		use_image.addEventListener("mouseenter", function (event){
			use_image.src = "images/use_image2.png"
		}, false);
		use_image.addEventListener("mouseout", function (event){
			use_image.src = "images/use_image1.png"
		}, false);
		use_image.addEventListener("click", function (event){
			final.showFeedbackMessage("「使う」ことでこの数字を上げられます");
		}, false);
		for(i = 0; i < favourite_image.length; i++){
			favourite_image.item(i).setAttribute("data-index", (i + 1));
			favourite_image.item(i).addEventListener("mouseenter", mouseenterFavourite, false);
			favourite_image.item(i).addEventListener("mouseout", mouseoutFavourite, false);
			favourite_image.item(i).addEventListener("click", clickFavourite, false);
		}
		final.showFeedbackMessage("");
	}
	final.hoverFavouriteImage = function (element, hover){
		var favourite_image = document.getElementsByName("favourite_image");
		var i;
		var start_index = -1;
		for(i = 0; i < favourite_image.length; i++){
			favourite_image.item(i).src = hover ? "images/favourite_image2.png" : "images/favourite_image1.png";
			if(element === favourite_image.item(i)){
				break;
			}
		}
	}
	final.showFeedbackMessage = function (message){
		var feedback_text = document.getElementById("feedback_text");
		feedback_text.innerHTML = message;
		if(message !== ""){
			feedback_text.style.visibility = "visible";
			window.setTimeout(function (){
				final.showFeedbackMessage("");
			}, 3000);
		}else{
			feedback_text.style.visibility = "hidden";
		}
	}

	window.addEventListener("DOMContentLoaded", final.init, false);
</script>
<div class="sections clearfix">
	<div class="detail_image">
		<img src="upload/<?= $row["character_id"] ?>/<?= $row["image_id"] ?>.png" alt="">
	</div>
	<div class="detailbox">
		<h2><?= $row["name"] ?></h2>
		<div class="username"><a href="search.php?user_id=<?= $row["user_id"] ?>"><?= $row["username"] ?></a></div>
		<div class="use"><img src="images/use_image1.png" id="use_image"><div><?= $row["use_count"] ?></div></div>
		<div class="favourite"><img src="images/favourite_image1.png" name="favourite_image"><img src="images/favourite_image1.png" name="favourite_image"><img src="images/favourite_image1.png" name="favourite_image"><img src="images/favourite_image1.png" name="favourite_image"><img src="images/favourite_image1.png" name="favourite_image"><div><?= $row["favourite_average"] ?></div></div>
		<div class="message"><div id="feedback_text"></div></div>
		<div class="description"><p><?= $row["description"] ?></p></div>
		<div><button id="back_button">もどる</button><button id="use_button">使う</button><?php if($row["user_id"] === $user_id){ ?><button id="create_button">編集</button><?php } ?></div>
	</div>
</div>

<?php
	mysql_close($db);
	
	require("footer.php");
?>
