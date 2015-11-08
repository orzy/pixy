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

<!-- @see http://jqueryui.com/themeroller/#themeGallery -->
<link rel="stylesheet" href="http://ajax.googleapis.com/ajax/libs/jqueryui/1/themes/start/jquery-ui.css" />
</head>

<body>

<!-- @see http://jqueryui.com/docs/Theming/API -->
<div class="ui-widget">

<div class="ui-widget-header">
<h1><?php echo h($title) ?></h1>
</div><!-- /ui-widget-header -->

<div class="ui-widget-content">
{content}<?php /* ここにページごとの内容が入る */ ?>
</div><!-- /ui-widget-content -->

</div><!-- /ui-widget -->

<script src="http://ajax.googleapis.com/ajax/libs/jquery/1/jquery.min.js"></script>
<script src="http://ajax.googleapis.com/ajax/libs/jqueryui/1/jquery-ui.min.js"></script>
<script>
$(function() {
	alert("Hello jQuery UI!");
});
</script>

</body>
</html>
