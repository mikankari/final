<?php
	require("initialize.php");
	$pagename = "$create_screen";
	require("header.php");

	if(isset($_GET["character_id"])){
		$character_id = trim($_GET["character_id"]);
		$character_id = strip_tags($character_id);
		$query = "select * from character_t"
			. " where character_id = $character_id";
		$result = mysql_query($query);
		$row = mysql_fetch_array($result);
		if($row["user_id"] !== "$user_id"){
			$error = "キャラクターを編集できる権限がありません";
			require("footer.php");
			require("destroy.php");
			exit();
		}
		$character_name = $row["name"];
		$character_description = $row["description"];
		$character_ispublic = $row["ispublic"];
	}else{
		$query = "insert into character_t (name, user_id, image_id, description, ispublic)"
			. "values ('名称未設定', $user_id, 0, '', 0)";
		$result = mysql_query($query);
		$character_id = mysql_insert_id();
		mkdir("$image_path/$character_id");
		header("Location: $root_path/create.php?character_id=$character_id");
	}

	if(isset($_POST["patterns"])){
		$patterns = trim($_POST["patterns"]);
		$patterns = strip_tags($patterns);
		if($patterns !== ""){
			$patterns = explode("\t", $patterns);
			$query = "delete from patterns_t"
				. " where character_id = $character_id";
			$result = mysql_query($query);
			foreach($patterns as $values){
				$values = explode(",", $values);
				$values[0] = str_replace("upload/$character_id/", "", $values[0]);
				$values[0] = str_replace(".png", "", $values[0]);
				$values[1] = strip_tags($values[1]);
				$query = "insert into patterns_t (character_id, image_id, message)"
					. " values ($character_id, $values[0], '$values[1]')";
				$result = mysql_query($query);
			}
		}
		if(isset($_POST["name"])){
			$name = trim($_POST["name"]);
			$name = strip_tags($name);
			$thumb = trim($_POST["thumb"]);
			$thumb = strip_tags($thumb);
			$description = trim($_POST["description"]);
			$description = strip_tags($description);
			$ispublic = trim($_POST["ispublic"]);
			$ispublic = strip_tags($ispublic);
			$query = "update character_t"
				. " set name = '$name', image_id = '$thumb', description = '$description', ispublic = $ispublic"
				. " where character_id = $character_id";
			$result = mysql_query($query);
			$goto = trim($_POST["goto"]);
			$goto = strip_tags($goto);
			if($goto !== "create"){
				header("Location: $goto.php");
			}
		}
	}
	if(isset($_FILES["upload"])){
		$upload = $_FILES["upload"];
		if(is_uploaded_file($upload["tmp_name"]) && $upload["error"] === 0){
			if($upload["type"] === "image/png" && $upload["size"] < $image_maxsize){
				$query = "insert into image_t (character_id, filename, isused)"
					. " value ($character_id, '{$upload["name"]}', 0)";
				$result = mysql_query($query);
				$image_id = mysql_insert_id();
				if($image_id){
					move_uploaded_file($upload["tmp_name"], "$image_path/$character_id/$image_id.png");
				}
			}else{
				$error = "ファイルはPNGでない、または" . ($image_maxsize / 1048576) . "MBを超えています";
				require("footer.php");
				require("destroy.php");
				exit();
			}
		}else{
			$error = "アップロードが失敗しました";
			require("footer.php");
			require("destroy.php");
			exit();
		}
	}
?>

<script type="text/javascript">
	var final = {};
	final.init = function (event){
		var image_button = document.getElementsByName("image_button"),
			upload_button = document.getElementById("upload_button");
		var add_button = document.getElementById("add_button"),
			remove_button = document.getElementById("remove_button"),
			pattern_select = document.getElementById("pattern_select");
		var save_button = document.getElementById("save_button"),
			post_button = document.getElementById("post_button"),
			cancel_button = document.getElementById("cancel_button");
		var uploadcancel_button = document.getElementById("uploadcancel_button"),
			postcancel_button = document.getElementById("postcancel_button");
		var i;
		
		for(i = 0; i < image_button.length; i++){
			image_button.item(i).addEventListener("click", function (event) {
				final.setCharacterPreview(event.currentTarget);
			}, false);
		}
		upload_button.addEventListener("click", function (event) {
			var patterns_input = document.querySelector("#upload_dialog input[name='patterns']");
			patterns_input.value = final.getPatterns();
			final.showUploadDialog(true);
		}, false);

		add_button.addEventListener("click", function (event) {
			var new_option = document.createElement("option");
			var data = final.getCharacterPreview();
			if(data.filename !== ""){
				new_option.innerHTML = data.filename + " | " + data.message;
				new_option.value = data.url + "," + data.message;
				pattern_select.appendChild(new_option);
			}
		}, false);
		remove_button.addEventListener("click", function (event) {
			var selected_option = pattern_select.options.item(pattern_select.selectedIndex);
			if (pattern_select.selectedIndex >= 0 && window.confirm("選択中のパターン\n「" + selected_option.innerHTML + "」\nを削除します。")) {
				pattern_select.removeChild(selected_option);
			}
		}, false);
		pattern_select.addEventListener("change", function (event){
			var selected_option = pattern_select.options.item(pattern_select.selectedIndex);
			final.setCharacterPreview(selected_option);
		}, false);
		
		save_button.addEventListener("click", function (event) {
			var patterns_input = document.querySelector("#post_dialog input[name='patterns']");
			var goto_input = document.querySelector("#post_dialog input[name='goto']");
			var name_input = document.querySelector("#post_dialog input[name='name']");
			var post_form = document.querySelector("#post_dialog form");
			patterns_input.value = final.getPatterns();
			goto_input.value = "create";
			if(name_input.value === "名称未設定"){
				final.showPostDialog(true);
			}else{
				post_form.submit();
			}
		}, false);
		post_button.addEventListener("click", function (event) {
			var patterns_input = document.querySelector("#post_dialog input[name='patterns']");
			var goto_input = document.querySelector("#post_dialog input[name='goto']");
			var ispublic_input = document.querySelector("#post_dialog input[name='ispublic']");
			patterns_input.value = final.getPatterns();
			goto_input.value = "mypage";
			ispublic_input.value = "1";
			final.showPostDialog(true);
		}, false);
		cancel_button.addEventListener("click", function (event) {
			if (window.confirm("終了します。\n保存内容は失われます。")) {
				window.location.href = "mypage.php";
			}
		}, false);
		
		uploadcancel_button.addEventListener("click", function (event) {
			final.showUploadDialog(false);
			event.preventDefault();
		}, false);
		postcancel_button.addEventListener("click", function (event) {
			final.showPostDialog(false);
			event.preventDefault();
		}, false);
	}
	final.getCharacterPreview = function (){
		var character_img = document.querySelector(".character img");
		var character_textarea = document.querySelector(".character textarea");
		var data = {};
		data.url = character_img.src.substring(character_img.src.indexOf("upload/<?php echo $character_id; ?>/"));
		data.filename = character_img.getAttribute("data-filename");
		data.message = character_textarea.value;
		return data;
	}
	final.setCharacterPreview = function (element){
		var character_img = document.querySelector(".character img");
		var character_textarea = document.querySelector(".character textarea");
		var url, filename, message, split;
		if(element.getAttribute("name") === "image_button"){
			url = element.getElementsByTagName("img").item(0).src;
			filename = element.getElementsByTagName("div").item(0).innerHTML;
		}else if(element.parentNode.id = "pattern_select"){
			split = element.value.split(",");
			url = split[0];
			split = element.text.split("|");
			filename = split[0];
			message = split[1];
			character_textarea.innerHTML = message;
		}else{
			url = "http://placehold.it/144x200";
			filename = "";
		}
		character_img.src = url;
		character_img.setAttribute("data-filename", filename);
		if(element.getAttribute("name") === "image_button"){
			character_textarea.focus();
			character_textarea.select();
		}
	}
	final.getPatterns = function (){
		var pattern_select = document.getElementById("pattern_select");
		var pattern_option;
		var value = "";
		for(var i = 0; i < pattern_select.childNodes.length; i++){
			pattern_option = pattern_select.childNodes.item(i);
			if(pattern_option.tagName && pattern_option.tagName.toLowerCase() === "option"){
				value += pattern_select.childNodes.item(i).value + "\t";
			}
		}
		return value;
	}
	final.showUploadDialog = function (visibility){
		var upload_dialog = document.getElementById("upload_dialog");
		upload_dialog.style.display = visibility ? "block" : "none";
	}
	final.showPostDialog = function (visibility){
		var post_dialog = document.getElementById("post_dialog");
		post_dialog.style.display = visibility ? "block" : "none";
	}
	final.destroy = function (event){
		event.returnValue = "終了しますか？\n保存内容は失われます。";
	}

	window.addEventListener("DOMContentLoaded", final.init, false);
//	window.addEventListener("beforeunload", final.destroy, false);
</script>
<div class="clearfix editbox">
	<div class="images">
<?php
		$query = "select * from image_t"
			. " where character_id = $character_id";
		$result = mysql_query($query);
		while($row = mysql_fetch_array($result)){
?>
			<div class="characterbox" name="image_button" data-value="">
				<img src="upload/<?php echo $character_id . "/" . $row["image_id"]; ?>.png" alt="">
				<div class="name"><?php echo $row["filename"]; ?></div>
			</div>
<?php
		}
?>
		<div class="characterbox" id="upload_button">
			<img src="images/upload_button.png" alt="">
			<div class="name">画像の追加</div>
		</div>
	</div>
	<div class="patterns">
		<div class="character">
			<div id="image">
				<img src="http://placehold.it/144x200" alt="" data-filename="">
			</div>
			<div id="message">
				<textarea placeholder="セリフを入力" class="balloon">とりゃーっ！</textarea>
			</div>
		</div>
		<div class="createbuttons">
			<button id="add_button">パターンに追加</button>
			<button id="remove_button">パターンから削除</button>
		</div>
		<div class="patternselect">
			<select size="4" id="pattern_select">
<?php
				$query = "select * from patterns_t natural join image_t"
					. " where character_id = $character_id";
				$result = mysql_query($query);
				while($row = mysql_fetch_array($result)){
?>
					<option value="upload/<?php echo $character_id . "/" . $row["image_id"] . ".png," . $row["message"]; ?>"><?php echo $row["filename"] . "|" . $row["message"]; ?></option>
<?php
				}
?>
			</select>
		</div>
		<div class="createbuttons">
			<button id="save_button">非公開で保存</button>
			<button id="post_button">公開して投稿</button>
			<button id="cancel_button">終了</button>
		</div>
	</div>
	<div id="upload_dialog" class="dialog">
		<h2>画像の追加</h2>
		<div class="description">PNG画像、<?php echo $image_maxsize / 1048576; ?>MBまでアップロード可能</div>
		<form method="post" enctype="multipart/form-data">
			<div>
				<input type="file" name="upload">
			</div>
			<div class="createbuttons">
				<input type="submit" value="アップロード">
				<button id="uploadcancel_button">キャンセル</button>
				<input type="hidden" name="patterns" value="">
			</div>
		</form>
	</div>
	<div id="post_dialog" class="dialog">
		<h2>キャラクターの投稿</h2>
		<div class="description">後でキャラクターページから変更できます</div>
		<form method="post">
			<div>
				<div><label>キャラクター名</label></div>
				<div><input type="text" name="name" value="<?php echo $character_name; ?>"></div>
			</div>
			<div>
				<div><label>サムネイル</label></div>
				<div><select name="thumb">
<?php
					$query = "select * from image_t"
						. " where character_id = $character_id";
					$result = mysql_query($query);
					while($row = mysql_fetch_array($result)){
?>
						<option value="<?php echo $row["image_id"]; ?>"><?php echo $row["filename"]; ?></option>
<?php
					}
?>
				</select></div>
			</div>
			<div>
				<div><label>説明文</label></div>
				<div><textarea name="description"><?php echo $character_description; ?></textarea></div>
			</div>
			<div class="createbuttons">
				<input type="submit" value="OK">
				<button id="postcancel_button">キャンセル</button>
				<input type="hidden" name="patterns" value="">
				<input type="hidden" name="ispublic" value="<?php echo $character_ispublic; ?>">
				<input type="hidden" name="goto" value="create">
			</div>
		</form>
	</div>
</div>

<?php
	require("footer.php");
	require("destroy.php");
?>
