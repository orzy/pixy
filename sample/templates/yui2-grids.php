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
<link rel="stylesheet" href="http://ajax.googleapis.com/ajax/libs/yui/2/build/grids/grids-min.css" />
</head>

<!-- @see http://developer.yahoo.com/yui/grids/builder/ -->
<body id="doc4">

<div id="hd">
<h1><?php echo h($title) ?></h1>
</div><!-- #hd -->

<div id="bd">
{content}<?php /* ここにページごとの内容が入る */ ?>
</div><!-- #bd -->

<div id="ft">
&copy; Pixy
</div><!-- #ft -->

<script src="http://ajax.googleapis.com/ajax/libs/jquery/1/jquery.min.js"></script>
</body>
</html>
