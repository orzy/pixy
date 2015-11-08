<?php
/**
 *	Last update 2013/08/27
 */

require('Pixy.php');


$title = 'ページタイトル';

$head = <<< 'END'	//nowdoc
<link rel="stylesheet" href="stylesheet.css" />
<style> body { padding: 0 1em; } </style>
END;

$js = <<< 'END'	//nowdoc
<script src="javascript.js"></script>
<script> alert("Hello Pixy!"); </script>
END;

//テンプレートを指定すると、ここより下はテンプレートに埋め込まれるコンテンツになる
Pixy::template('templates/simple.php', compact('title', 'head', 'js'), true);


//DB接続
$db = Pixy::mysql('db_name', 'user_name', 'password');	//MySQL
//$db = Pixy::pgsql('db_name', 'user_name', 'password');	//PostgreSQL

//SQL実行
$sql = 'SELECT * FROM table WHERE id > ?';
$params = array(123);
$rows = $db->query($sql, $params);	// SELECTの戻り値はPDOStatementオブジェクト
?>


<h2>グローバル関数の紹介</h2>

<?php
foreach ($rows as $row) {
	//HTMLエスケープ
	echo h('<' . $row['id'] . '>'), "<br />\n";
}

//URLエンコード
echo ue('URL エンコード'), "<br />\n";
echo ue(array('q' => 'キーワード'), 'http://www.google.com/search'), "<br />\n";

//システム日時を表示
echo now();

//PHPの値をpre要素でラップして見やすく表示
dump(array(1, 2, 3));

//CSS, JavaScript, 画像ファイルのURLにはキャッシュ防止のタイムスタンプを自動挿入
?>
<img src="image.gif" />
