<?php
	require("initialize.php");
	$pagename = 'PCやスマホで使う';
	require('header.php');
?>
<script type="text/javascript">
	var final = {};
	final.init = function (event){
		var windows_button = document.getElementById("windows_button");
		var macos_button = document.getElementById("macos_button");
		var android_button = document.getElementById("android_button");
		var ios_button = document.getElementById("ios_button");
		var browser_button = document.getElementById("browser_button");

		windows_button.addEventListener("click", function (event){
			window.location.href = "#windows";
		}, false);
		macos_button.addEventListener("click", function (event){
			window.location.href = "#macos";
		}, false);
		android_button.addEventListener("click", function (event){
			window.location.href = "#android";
		}, false);
		ios_button.addEventListener("click", function (event){
			window.location.href = "#ios";
		}, false);
		browser_button.addEventListener("click", function (event){
			window.location.href = "#browser";
		}, false);
	}
	
	window.addEventListener("DOMContentLoaded", final.init, false);
</script>
<section>
	<h2>使うためには…</h2>
	<section>
		<h3>PCで使う</h3>
		<div>
			<div class="nativebuttons">
				<button id="windows_button">Windows</button>
				<button id="macos_button">Mac OS</button>
			</div>
		</div>
	</section>
	<section>
		<h3>スマホで使う</h3>
		<div>
			<div class="nativebuttons">
				<button id="android_button">Android</button>
				<button id="ios_button">iOS</button>
			</div>
		</div>
	</section>
	<section>
		<h3>その他</h3>
		<div>
			<div class="nativebuttons">
				<button id="browser_button">ブラウザ</button>
			</div>
		</div>
	</section>
	<section id="windows">
		<h3>Windowsの場合</h3>
		<div>
			<p>作成中。Windowsサイドバーを使う予定です。</p>
		</div>
	</section>
	<section id="macos">
		<h3>Mac OSの場合</h3>
		<div>
			<p>作成中。Dashboardを使う予定です。</p>
		</div>
	</section>
	<section id="android">
		<h3>Androidの場合</h3>
		<div>
			<p>作成中。ホーム画面ウィジェットを使う予定です。</p>
		</div>
	</section>
	<section id="ios">
		<h3>iOSの場合</h3>
		<div>
			<p>作成中。通知センターウィジェットを使う予定です。</p>
		</div>
	</section>
	<section id="browser">
		<h3>ブラウザの場合</h3>
		<div>
			<p>作成中。ブラウザで開いてショートカットの作成を提案する予定です。</p>
		</div>
	</section>
</section>
<?php
	require('footer.php');
	require("destroy.php");
?>
