<?php
/**
 *  Pixy2 - A compact PHP utility library.
 *
 *  @version 2.0.0
 *  @see     https://github.com/orzy/pixy
 *  @license The MIT license (http://www.opensource.org/licenses/mit-license.php)
 */
class Pixy2 {
	protected $_pdo = null;
	
	/**
	 *	コンストラクタ
	 */
	public function __construct() {
		// エラーハンドリング
		set_error_handler(array($this, 'beforeWarning'), E_WARNING);
		register_shutdown_function(array($this, 'beforeShutdown'));
		
		// 文字コード設定
		ini_set('mbstring.strict_detection', true);
		ini_set('default_charset', 'UTF-8');
		mb_internal_encoding('UTF-8');
		
		// 入力データの文字コード変換
		mb_convert_variables('UTF-8', 'UTF-8', $_GET);
		mb_convert_variables('UTF-8', 'UTF-8', $_POST);
		mb_convert_variables('UTF-8', 'UTF-8', $_REQUEST);
		mb_convert_variables('UTF-8', 'UTF-8', $_COOKIE);
	}
	/**
	 *	存在しないインスタンス変数へのアクセスの通知
	 *	@throws	LogicException	存在しないインスタンス変数へのアクセス
	 */
	public function __get($key) {
		throw new LogicException("'$key' not found.");
	}
	/**
	 *	存在しないインスタンス変数へのアクセスの通知
	 *	@throws	LogicException	存在しないインスタンス変数へのアクセス
	 */
	public function __set($key, $value) {
		throw new LogicException("'$key' not found.");
	}
	/**
	 *	Warning発生時の情報を増やす
	 */
	public function beforeWarning($errNo, $msg, $file, $line, $context) {
		$e = new Exception();
		$this->_errorLog('Warning', $e->getTraceAsString());
		return false;
	}
	/**
	 *	Error発生時の情報を増やす
	 */
	public function beforeShutdown(){
		$error = error_get_last();
		
		if ($error['type'] === E_ERROR) {
			$this->_errorLog('Error', $error['message']);
		}
	}
	
	protected function _errorLog($level, $msg) {
		error_log("$level [URI] " . $_SERVER['REQUEST_URI'] . "\n" . $msg);
	}
	/**
	 *	文字列の変換
	 *	@param	string	$value	変換する文字列
	 *	@param	string	$type	変換種類
	 *	@param	mixed	$param	(Optional) 変換種類によっては必要な情報
	 *	@return	string	変換後の文字列
	 *	@throws	LogicException	存在しない変換種類を指定
	 */
	public function convert($value, $type, $param = null) {
		switch ($type) {
			case 'han':
				return mb_convert_kana($value, 'askh');
			case 'zen':
				return mb_convert_kana($value, 'ASKV');
			case 'hanzen':
				return mb_convert_kana($value, 'asKV');
			case 'lower':
				return mb_strToLower($value);
			case 'upper':
				return mb_strToUpper($value);
			case 'zero':
				return str_pad($value, $param, '0', STR_PAD_LEFT);
			case 'utf8':
				if (!$param) {
					$param = 'SJIS-WIN';
				}
				
				return mb_convert_encoding($value, 'UTF-8', $param);
			case 'sjis':
				return mb_convert_encoding($value, 'SJIS-WIN');
			default:
				throw new LogicException("'$type' not found.");
		}
	}
	/**
	 *	DB接続
	 *	@param	string	$db	DB（スキーマ）
	 *	@param	string	$user	ユーザー
	 *	@param	string	$password	パスワード
	 *	@param	string	$others	(Optional) その他の接続情報（host等）
	 *	@param	string	$dbms	(Optional) DBMS（mysql or pgsql）
	 *	@return	PDO
	 */
	public function db($db, $user, $password, $others = '', $dbms = 'mysql') {
		$delim = array('mysql' => ';', 'pgsql' => ' ');
		$dsn = "$dbms:dbname=$db";
		
		if ($others) {
			$dsn .= $delim[$dbms] . $others;
		}
		
		if ($dbms === 'mysql') {
			$dsn .= ';charset=utf8';
		}
		
		$this->_pdo = new PDO($dsn, $user, $password, array(
			PDO::ATTR_ERRMODE=> PDO::ERRMODE_EXCEPTION,
			PDO::ATTR_EMULATE_PREPARES => false,
		));
		
		if ($dbms === 'mysql') {
			$this->_pdo->setAttribute(PDO::MYSQL_ATTR_USE_BUFFERED_QUERY, true);
		}
		
		return $this->_pdo;
	}
	/**
	 *	SQLの実行
	 *	@param	string	$sql	SQL
	 *	@param	mixed	$params	(Optional) プレースホルダーにセットする値、またはその配列
	 *	@return	PDOStatement
	 *	@throws	RuntimeException	DBエラー
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
	/**
	 *	HTTPSを強制
	 */
	public function https() {
		if (!isset($_SERVER['HTTPS'])) {
			header('Location: https://' . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI'], true, 301);
			exit;
		}
	}
	/**
	 *  HTTP POSTの送信
	 *	@param	string	$url	送信先のURL
	 *	@param	array	$params	送信するデータ項目
	 *	@param	mixed	$headers	(Optional) HTTPリクエストヘッダー
	 *	@return	string	HTTPレスポンスのbody
	 *	@throws	RuntimeException	HTTPレスポンスのステータスコードが200以外
	 */
	public function post($url, array $params, $headers = array()) {
		$headers = (array)$headers;
		$headers[] = 'Content-type: application/x-www-form-urlencoded';
		
		$context = stream_context_create(array('http' => array(
			'method' => 'POST',
			'header' => $headers,
			'content' => http_build_query($params),
			'ignore_errors' => true,
		)));
		
		$response = file_get_contents($url, false, $context);
		
		if (is_array($http_response_header)) {
			if (!preg_match('@^HTTP/1\\.. 200 @i', $http_response_header[0])) {
				$msg = "\n[URL]\n$url\n[PARAMS]\n" . var_export($params, true);
				throw new RuntimeException($http_response_header[0] . $msg);
			}
		}
		
		return $response;
	}
	/**
	 *	CSVダウンロード
	 *	@param	string	$fileName	送信先のURL
	 *	@param	mixed	$rows	CSVのデータ
	 *	@param	array	$headers	(Optional) ヘッダー行
	 *	@param	string	$encoding	(Optional) CSVの文字コード
	 */
	public function csv($fileName, $rows, array $headers = array(), $encoding = 'SJIS-WIN') {
		header('Content-Type: application/x-csv');
		$fileName = mb_convert_encoding($fileName, $encoding);
		header("Content-Disposition: attachment; filename=$fileName.csv");
		
		$fp = fopen('php://output', 'w');
		
		if ($headers) {
			mb_convert_variables($encoding, 'UTF-8', $headers);
			fputcsv($fp, $headers);
		}
		
		foreach ($rows as $row) {
			mb_convert_variables($encoding, 'UTF-8', $row);
			fputcsv($fp, $row);
		}
		
		fclose($fp);
		exit;
	}
	/**
	 *	セッションを開始または破棄する
	 *	@param	mixed	$setting	(Optional) 開始の場合は設定、破棄の場合はfalse
	 */
	public function session($setting = array()) {
		if ($setting === false) {
			$_SESSION = array();
			return session_destroy();
		}
		
		$setting = array_merge(array(
			'name' => 'sid',
			'gc_maxlifetime' => 60 * 60 * 24 * 3,
			'cookie_httponly' => true,
			'use_only_cookies' => true,
			'entropy_file' => '/dev/urandom',
			'entropy_length' => 32,
			'hash_function' => true,
			'hash_bits_per_character' => 5,
		), $setting);
		
		foreach ($setting as $key => $value) {
			ini_set("session.$key", $value);
		}
		
		session_start();
	}
	/**
	 *	タイムスタンプ文字列
	 *	@return	string
	 */
	public function now() {
		return date('Y-m-d H:i:s');
	}
	/**
	 *	変数のデバグ用出力
	 *	@param	mixed	$value	出力する文字列または配列、オブジェクト
	 *	@param	mixed	$log	(Optional) ログに出力する場合はtrue、またはログのパス
	 */
	public function dump($value, $log = false) {
		if (is_array($value) || is_object($value)) {
			$value = var_export($value, true);
		}
		
		if ($log) {
			if (is_bool($log)) {
				error_log($value);
			} else {
				error_log('[' . now() . "] $value\n", 3, $log);
			}
		} else {
			echo '<pre>' . h($value) . "</pre>\n";
		}
	}
}
/**
 *	HTMLエスケープ
 *	@param	string	$value
 *	@return	string
 */
function h($value) {
	return htmlSpecialChars($value, ENT_QUOTES, 'UTF-8');
}

