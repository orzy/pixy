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
<?php echo $head ?>
</head>

<body>
<h1><?php echo h($title) ?></h1>

{content}<?php /* ここにページごとの内容が入る */ ?>

<?php echo $js ?>

</body>
</html>
