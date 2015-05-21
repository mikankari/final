<?php
	require("initialize.php");
	$pagename = "キャラ名";
	require("header.php");

	$character_id = trim($_GET["character_id"]);
	$character_id = strip_tags($character_id);	
	$query = "select * from character_t natural join user_t"
		. " where character_id = $character_id";
	$result = mysql_query($query);
	if(mysql_num_rows($result)){
		$row = mysql_fetch_array($result);
		if($row["user_id"] !== "$user_id" && $row["ispublic"] === "0"){
			$error = "キャラクターは非公開です";
			require("footer.php");
			require("destroy.php");
			exit();
		}
		$query2 = "select *, '' as 'use_count' from used_activity_t"
			. " where used > 0 and character_id = $character_id"
			. " group by user_id, date, character_id";
		$result2 = mysql_query($query2);
		$row2 = mysql_fetch_array($result2);
		$row["use_count"] = /*$row2["use_count"]*/mysql_num_rows($result2);
		$query2 = "select avg(favourited) as 'favourite_average' from favourited_activity_t"
			. " where favourited > 0 and character_id = $character_id";
		$result2 = mysql_query($query2);
		$row2 = mysql_fetch_array($result2);
		$row["favourite_average"] = round($row2["favourite_average"], 1);
	}else{
		$error = "キャラクターは存在しません";
		require("footer.php");
		require("destroy.php");
		exit();
	}

	$query2 = "select * from favourited_activity_t"
		. " where user_id = $user_id and date = CURRENT_DATE and character_id = $character_id";
	$result2 = mysql_query($query2);
	if(mysql_num_rows($result)){
		$row2 = mysql_fetch_array($result2);
		$isfavourited = $row2["favourited"];
	}else{
		$isfavourited = false;
	}
	if(isset($_POST["favourite"])){
		if(!$isfavourited){
			$favourited = $_POST["favourite"];
			$favourited = strip_tags($favourited);
			$query = "insert into favourited_activity_t (user_id, date, time, character_id, favourited)"
				. " values ($user_id, CURRENT_DATE, CURRENT_TIME, $character_id, $favourited)";
			$result = mysql_query($query);
		}else{
			$query = "insert into favourited_activity_t (favourited_id, user_id, date, time, character_id, favourited)"
				. " values ($isfavourited, $user_id, CURRENT_DATE, CURRENT_TIME, $character_id, -1)";
			$result = mysql_query($query);
		}
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
			var loader;
			var i;
			loader = new XMLHttpRequest();
			loader.open("POST", window.location.href, true);
			loader.setRequestHeader("Content-Type", "application/x-www-form-urlencoded");
			loader.send("favourite=" + event.target.getAttribute("data-index"));
			for(i = 0; i < favourite_image.length; i++){
				favourite_image.item(i).removeEventListener("mouseover", mouseenterFavourite);
				favourite_image.item(i).removeEventListener("mouseout", mouseoutFavourite);
				favourite_image.item(i).removeEventListener("click", clickFavourite);
			}
			final.showFeedbackMessage("評価が完了しました");
		};
		var clickFavouriteFailed = function (event){
			var loader;
			loader = new XMLHttpRequest();
			loader.open("POST", window.location.href, true);
			loader.setRequestHeader("Content-Type", "application/x-www-form-urlencoded");
			loader.send("favourite=" + event.target.getAttribute("data-index"));
			final.showFeedbackMessage("再評価は１日おきにできます");
		}
		var i;
		
		back_button.addEventListener("click", function (event){
			window.history.back();
		}, false);
		use_button.addEventListener("click", function (event){
			window.location.href = "use.php?character_id=" + <?php echo $row["character_id"]; ?>;
		}, false);
		if(edit_button){
			edit_button.addEventListener("click", function (event){
				window.location.href = "create.php?character_id=" + <?php echo $row["character_id"]; ?>;
			}, false);
		}
		use_image.addEventListener("mouseenter", function (event){
			use_image.src = "images/use_image2.png"
		}, false);
		use_image.addEventListener("mouseout", function (event){
			use_image.src = "images/use_image1.png"
		}, false);
		use_image.addEventListener("click", function (event){
			final.showFeedbackMessage("このキャラクターが表示された回数です");
		}, false);
		for(i = 0; i < favourite_image.length; i++){
<?php
			if(!$isfavourited){
?>
				favourite_image.item(i).setAttribute("data-index", (i + 1));
				favourite_image.item(i).addEventListener("mouseover", mouseenterFavourite, false);
				favourite_image.item(i).addEventListener("mouseout", mouseoutFavourite, false);
				favourite_image.item(i).addEventListener("click", clickFavourite, false);
<?php
			}else{
?>
				favourite_image.item(i).setAttribute("data-index", (i + 1));
				favourite_image.item(i).addEventListener("click", clickFavouriteFailed, false);
<?php
			}
?>
		}
<?php
		if($isfavourited){
?>
			final.hoverFavouriteImage(favourite_image.item(<?php echo $isfavourited - 1; ?>), true);
<?php
		}
?>
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
		<img src="upload/<?php echo $row["character_id"]. "/" . $row["image_id"]; ?>.png" alt="">
	</div>
	<div class="detailbox">
		<h2><?php echo $row["name"]; ?></h2>
		<div class="username"><a href="search.php?user_id=<?php echo $row["user_id"]; ?>"><?php echo $row["username"]; ?></a></div>
		<div class="use"><img src="images/use_image1.png" id="use_image"><div><?php echo $row["use_count"]; ?></div></div>
		<div class="favourite"><img src="images/favourite_image1.png" name="favourite_image"><img src="images/favourite_image1.png" name="favourite_image"><img src="images/favourite_image1.png" name="favourite_image"><img src="images/favourite_image1.png" name="favourite_image"><img src="images/favourite_image1.png" name="favourite_image"><div><?php echo $row["favourite_average"]; ?></div></div>
		<div class="message"><div id="feedback_text"></div></div>
		<div class="description"><p><?php echo $row["description"]; ?></p></div>
		<div><button id="back_button">もどる</button><button id="use_button">表示する</button><?php if($row["user_id"] === "$user_id"){ ?><button id="create_button">編集</button><?php } ?></div>
	</div>
</div>
<?php
	require("footer.php");
	require("destroy.php");
?>
