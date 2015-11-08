<?php
/**
 *  Pixy - A minimum PHP framework
 *
 *  @version 0.1.7
 *  @see     https://github.com/orzy/pixy
 *  @license The MIT license (http://www.opensource.org/licenses/mit-license.php)
 */

/**
 *  他のClassを呼び出す
 */
class Pixy {
	/**
	 *  @param	string	$path
	 *  @param	array	$params	(Optional)
	 *  @param	boolean	$reload	(Optional)
	 */
	public static function template($path, $params = array(), $reload = false) {
		PixyTemplate::start($path, $params, $reload);
	}
	/**
	 *  @param	string	$db
	 *  @param	string	$user	(Optional)
	 *  @param	string	$password	(Optional)
	 *  @param	string	$etc	(Optional)
	 *  @return	PixyDb
	 */
	public static function mysql($db, $user = 'root', $password = '', $etc = '') {
		return new PixyDb('mysql', $db, $user, $password, $etc);
	}
	/**
	 *  @param	string	$db
	 *  @param	string	$user	(Optional)
	 *  @param	string	$password	(Optional)
	 *  @param	string	$etc	(Optional)
	 *  @return	PixyDb
	 */
	public static function pgsql($db, $user = 'postgres', $password = '', $etc = '') {
		return new PixyDb('pgsql', $db, $user, $password, $etc);
	}
}
/**
 *  テンプレート読み込み
 */
class PixyTemplate {
	private static $_path;
	private static $_ob;
	private static $_reload;
	
	/**
	 *  @param	string	$path
	 *  @param	array	$params
	 *  @param	boolean	$reload
	 */
	public static function start($path, array $params, $reload) {
		self::$_path = $path;	//退避
		self::$_reload = $reload;
		extract($params);
		
		ob_start();
		require(self::$_path);
		self::$_ob = ob_get_clean();
		
		ob_start(array('PixyTemplate', 'end'));	//Callback
	}
	/**
	 *  @param	string	$content
	 *  @return	string
	 */
	public static function end($content) {
		//テンプレート内の'{content}'を実際のコンテンツに置換
		$html = str_replace('{content}', $content, self::$_ob);
		
		if (self::$_reload) {
			//CSS、JavaScript、画像ファイルのブラウザキャッシュを使わないようにする
			$pattern = '/(<(link|script|img).+(href|src)="[^?]+)"/iU';
			$html = preg_replace($pattern, '$1?' . time() . '"', $html);
		}
		
		return $html;
	}
}
/**
 *  DAO
 */
class PixyDb {
	private $_pdo;
	
	/**
	 *  MySQLとPostgreSQLに対応
	 *  @param	string	$db
	 *  @param	string	$user
	 *  @param	string	$password
	 *  @param	string	$etc
	 *  @see http://php.net/manual/ja/ref.pdo-mysql.connection.php
	 *  @see http://php.net/manual/ja/ref.pdo-pgsql.connection.php
	 */
	public function __construct($dbms, $db, $user, $password, $etc) {
		$delim = array('mysql' => ';', 'pgsql' => ' ');
		$dsn = "$dbms:dbname=$db" . $delim[$dbms] . $etc;
		
		if ($dbms === 'mysql') {
			$dsn .= ';charset=utf8';
		}
		
		$this->_pdo = new PDO(
			$dsn,
			$user,
			$password,
			array(
				PDO::ATTR_ERRMODE=> PDO::ERRMODE_EXCEPTION,
				PDO::ATTR_EMULATE_PREPARES => false,
			)
		);
		
		if ($dbms === 'mysql') {
			$this->_pdo->setAttribute(PDO::MYSQL_ATTR_USE_BUFFERED_QUERY, true);
		}
	}
	/**
	 *  @param	string	$sql
	 *  @param	mixed	$params	(Optional)
	 *  @return	PDOStatement
	 */
	public function query($sql, $params = array()) {
		try {
			$stmt = $this->_pdo->prepare($sql);
			$stmt->execute((array)$params);
		} catch (PDOException $e) {
			$msg = "\n[SQL]\n$sql\n[PARAMS]\n" . var_export($params, true);
			throw new RuntimeException($e->getMessage() . $msg);
		}
		
		$stmt->setFetchMode(PDO::FETCH_ASSOC);
		return $stmt;
	}
}
/**
 *  ClassのautoloadにPEAR形式のClass名を登録
 */
spl_autoload_register(
	create_function('$className', 'require(strtr($className, "_", "/") . ".php");')
);
/**
 *  グローバルなヘルパー関数たち
 */
if (!function_exists('h')) {
	/**
	 *  HTMLエスケープ
	 *  @param	string	$str
	 *  @return	string
	 */
	function h($str) {
		return htmlSpecialChars($str, ENT_QUOTES, 'UTF-8');
	}
}
if (!function_exists('ue')) {
	/**
	 *  URLエンコード（RFC 3986形式）
	 *  @param	mixed	$data
	 *  @param	string	$url	(Optional)
	 *  @return	string
	 */
	function ue($data, $url = '') {
		if (is_array($data)) {
			if ($url) {
				$s = $url . (strpos($url, '?') === false ? '?' : '&');
			} else {
				$s = '';
			}
			
			$s .= strtr(http_build_query($data), array('%7E' => '~', '+' => '%20'));
		} else {
			$s = strtr(rawUrlEncode($data), array('%7E' => '~'));
		}
		
		return $s;
	}
}
if (!function_exists('now')) {
	/**
	 *  現在の日時を取得する
	 *  @return	string
	 */
	function now() {
		return date('Y-m-d H:i:s');
	}
}
if (!function_exists('dump')) {
	/**
	 *  データを見やすい形で出力、またはログに書き出す
	 *  @param	mixed	$value
	 *  @param	mixed	$log	ログファイルへ書き出すかどうか or ログファイルのパス
	 */
	function dump($value, $log = false) {
		if (is_array($value) || is_object($value)) {
			$value = var_export($value, true);
		}
		
		if ($log) {
			if (is_bool($log)) {
				error_log($value);
			} else {
				error_log('[' . date('Y-m-d H:i:s') . "] $value\n", 3, $log);
			}
		} else {
			echo '<pre>' . h($value) . "</pre>\n";
		}
	}
}
