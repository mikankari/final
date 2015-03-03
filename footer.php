<?php
	if(strpos($path, 'image.php') === false){
		if(isset($error)){
?>
			<div class="error"><?= $error ?></div>
			<div><button id="error_back">戻る</button><button id="error_top">トップへ</button></div>
			<script type="text/javascript">
				window.addEventListener("DOMContentLoaded", function (event){
					var error_back = document.getElementById("error_back");
					var error_top = document.getElementById("error_top");
					error_back.addEventListener("click", function (event){
						window.history.back();
					}, false);
					error_top.addEventListener("click", function (event){
						window.location.href = "<?= $root_path ?>";
					}, false);
				}, false);
			</script>
<?php
		}
?>
	</div>
	<div id="footer">
		<div class="copyright">(c) s1223066</div>
	</div>
</div>
</body>
</html>
<?php
}
?>
