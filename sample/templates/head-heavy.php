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
<link rel="shortcut icon" type="image/x-icon" href="/favicon.ico" />

<link rel="alternate" type="application/rss+xml" href="http://example.com/rss" title="RSS Sample" />
<link rel="alternate" type="application/atom+xml" href="http://example.com/atom" title="Atom Feed Sample" />

<link rel="stylesheet" href="stylesheet.css" />
<style>
body {
	padding: 0 1em;
}
</style>

<!--[if lt IE 9.0]>
<style>
body {
	padding: 0 2em;
}
</style>
<![endif]-->

<script>
var _gaq = _gaq || [];
_gaq.push(['_setAccount', 'UA-XXXXXXX-X']);
_gaq.push(['_trackPageview']);
_gaq.push(['_trackPageLoadTime']);
(function() {
  var ga = document.createElement('script'); ga.type = 'text/javascript'; ga.async = true;
  ga.src = ('https:' == document.location.protocol ? 'https://ssl' : 'http://www') + '.google-analytics.com/ga.js';
  var s = document.getElementsByTagName('script')[0]; s.parentNode.insertBefore(ga, s);
})();
</script>

</head>

<body>

{content}<?php /* ここにページごとの内容が入る */ ?>

<script src="http://ajax.googleapis.com/ajax/libs/jquery/1/jquery.min.js"></script>
</body>
</html>
