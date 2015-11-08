<?php
/**
 *	Last update 2011/09/27
 */
?>
<!DOCTYPE html>
<html lang="ja">
<head>
<meta charset="UTF-8" />
<title><?php echo h($title) ?></title>
<link rel="stylesheet" href="http://ajax.googleapis.com/ajax/libs/yui/3/build/cssgrids/grids-min.css" />
<style>
body {
	margin: auto;
	width: 960px;
}
</style>
</head>

<body>

<h1><?php echo h($title) ?></h1>

<!-- @see http://yuilibrary.com/yui/docs/cssgrids/ -->
<div class="yui3-g">
{content}<?php /* ここにページごとの内容が入る */ ?>
</div><!-- /yui3-g -->

<script src="http://ajax.googleapis.com/ajax/libs/jquery/1/jquery.min.js"></script>
</body>
</html>
