<?php
/**
 *	Last update 2011/09/27
 */

// @see http://jquerymobile.com/
$jQueryMobilePath = 'http://code.jquery.com/mobile/1.0b3/jquery.mobile-1.0b3.min';
?>
<!DOCTYPE html>
<html lang="ja">
<head>
<meta charset="UTF-8" />
<meta name="viewport" content="width=device-width, initial-scale=1" />
<title><?php echo h($title) ?></title>
<link rel="stylesheet" href="<?php echo $jQueryMobilePath ?>.css" />
<script src="http://ajax.googleapis.com/ajax/libs/jquery/1/jquery.min.js"></script>
<script src="<?php echo $jQueryMobilePath ?>.js"></script>
</head>
<body>
<div data-role="page">

<div data-role="header">
<h1><?php echo h($title) ?></h1>
</div><!-- /header -->

<div data-role="content">
{content}<?php /* ここにページごとの内容が入る */ ?>
</div><!-- /content -->

<div data-role="footer">
<div class="ui-title"> &copy; Pixy </div>
</div><!-- /footer -->

</div><!-- /page -->
</body>
</html>
