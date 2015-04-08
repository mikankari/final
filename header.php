<?php
	if($pagename !== ""){
		$pagename .= ' - ';
	}
?>
<!doctype html>
<html>
<head>
<meta charset="UTF-8">
<!--<meta name="viewport" content="width=device-width, initial-scale=1.0">-->
<link rel="stylesheet" type="text/css" href="style.css">
<title><?php echo $pagename . $appname; ?></title>
</head>

<body>
<div id="container">
<?php
	if($path !== "/"){
		if(strpos($path, 'use.php') !== false || strpos($path, 'create.php') !== false){
?>
	<div id="header" class="silent">
		<h1><a href="search.php"><?php echo $appname; ?></a></h1>
		<nav>
			<ul class="boxeslist clearfix">
				<li><a href="create.php"><div class="button"><?php echo $create_screen; ?></div></a></li>
				<li><a href="search.php"><div class="button"><?php echo $use_screen; ?></div></a></li>
				<li><a href="mypage.php"><div class="button">マイページ</div></a></li>
				<li><a href="logout.php"><div class="hidden">ログアウト</div></a></li>
			</ul>
		</nav>
	</div>
<?php
		}else{
?>
	<div id="header">
		<h1><a href="search.php"><?php echo $appname; ?></a></h1>
		<div class="searchbox">
			<form method="get" action="search.php">
				<input type="search" name="keyword">
				<input type="submit" value="検索">
			</form>
		</div>
		<nav>
			<ul class="boxeslist clearfix">
				<li><a href="create.php"><div class="button"><?php echo $create_screen; ?></div></a></li>
				<li><a href="search.php"><div class="button"><?php echo $use_screen; ?></div></a></li>
				<li><a href="mypage.php"><div class="button">マイページ</div></a></li>
				<li><a href="logout.php"><div class="hidden">ログアウト</div></a></li>
			</ul>
		</nav>
	</div>
<?php
		}
	}
?>
	<div id="main">
