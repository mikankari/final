<?php
	require("initialize.php");
	$pagename = "";
	require("header.php");

	$_SESSION["session_id"] = session_id();
	$_SESSION["ticket"] = md5(uniqid(rand(), true));
	$_SESSION["remote"] = $_SERVER["REMOTE_ADDR"];
?>
<script type="text/javascript">
	var final = {};
	final.init = function (event){
		var create_button = document.getElementById("create_button");
		var use_button = document.getElementById("use_button");

		create_button.addEventListener("click", function (event){
			final.updateGotoParameter("create");
			final.showLoginForm();
		}, false);
		use_button.addEventListener("click", function (event){
			final.updateGotoParameter("search");
			final.showLoginForm();
		}, false);
	}
	final.updateGotoParameter = function (value){
		var goto = document.getElementsByName("goto");
		var i;
		for(i = 0; i < goto.length; i++){
			goto.item(i).value = value;
		}
	}
	final.showLoginForm = function (){
		var login_username = document.getElementById("login_username");
		window.location.href = "#login";
		login_username.focus();
	}
	
	window.addEventListener("DOMContentLoaded", final.init, false);
</script>
<div id="catch">
	<h1 class="catchcopy"><?php echo $appname; ?>はしゃべるキャラクターを投稿して、<br>誰かのデスクトップに共有します。</h1>
</div>
<div id="tutorial">
	<ol class="boxeslist clearfix">
		<li>
			<div class="tutorialbox">
				<img src="images/tutorial1.png" alt="">
				<div>キャラクターをつくる</div>
			</div>
		</li>
		<li>
			<div class="tutorialbox">
				<img src="images/tutorial2.png" alt="">
				<div>できたら投稿</div>
			</div>
		</li>
		<li>
			<div class="tutorialbox">
				<img src="images/tutorial3.png" alt="">
				<div>誰かが勝手に使ってくれる</div>
			</div>
		</li>
		<li>
			<div class="tutorialbox">
				<img src="images/tutorial4.png" alt="">
				<div>フィードバックがもらえる</div>
			</div>
		</li>
	</ol>
</div>
<div id="indexbuttons">
	<button id="create_button" class="bigbutton"><?php echo $create_screen; ?></button>
	<button id="use_button" class="bigbutton"><?php echo $use_screen; ?></button>
</div>
<div id="login">
	<h2><?php echo $appname; ?>へログイン</h2>
	<form method="post" action="login.php">
		<div class="table">
			<div>
				<div><label for="login_username">ユーザ名</label></div>
				<div><input type="text" name="username" id="login_username"></div>
			</div>
			<div>
				<div><label for="login_password">パスワード</label></div>
				<div><input type="password" name="password" id="login_password"></div>
			</div>
		</div>
		<div class="loginbuttons">
			<input type="hidden" name="goto" value="search">
			<input type="hidden" name="session_id" value="<?php echo $_SESSION["session_id"]; ?>">
			<input type="hidden" name="ticket" value="<?php echo $_SESSION["ticket"]; ?>">
			<input type="submit" value="ログイン" class="bigbutton">
		</div>
	</form>
</div>
<div id="signin">
	<h2><?php echo $appname; ?>に登録</h2>
	<form method="post" action="login.php">
		<div class="table">
			<div>
				<div><label for="signin_username">ユーザ名</label></div>
				<div><input type="text" name="username" id="signin_username"></div>
			</div>
			<div>
				<div><label for="signin_password">パスワード</label></div>
				<div><input type="password" name="password" id="signin_password"></div>
			</div>
			<div>
				<div><label for="signin_confirm">パスワードの確認</label></div>
				<div><input type="password" name="confirm" id="signin_confirm"></div>
			</div>
		</div>
		<div class="loginbuttons">
			<input type="hidden" name="goto" value="search">
			<input type="hidden" name="session_id" value="<?php echo $_SESSION["session_id"]; ?>">
			<input type="hidden" name="ticket" value="<?php echo $_SESSION["ticket"]; ?>">
			<input type="submit" value="登録する" class="bigbutton">
		</div>
	</form>
</div>

<?php
	require("footer.php");
	require("destroy.php");
?>
