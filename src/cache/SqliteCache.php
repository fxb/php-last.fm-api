<?

/** A SQLite database cache.
 *
 * @package	php-lastfm-api
 * @author  Felix Bruns <felixbruns@web.de>
 * @version	1.0
 */
final class SqliteCache extends Cache {
	private $sqlite;

	public function __construct($database){
		parent::__construct();

		$this->sqlite = sqlite_open($database);

		@sqlite_query($this->sqlite,
			"CREATE TABLE cache (hash VARCAHR(40) PRIMARY KEY, xml TEXT, expiration INTEGER);"
		);
	}

	public function __destruct(){
		sqlite_close($this->sqlite);
	}

	public function contains($hash){
		$result = sqlite_query($this->sqlite,
			"SELECT 1 FROM cache WHERE hash = '".sqlite_escape_string($hash)."'"
		);

		return sqlite_num_rows($result) > 0;
	}

	public function load($hash){
		$result = sqlite_query($this->sqlite,
			"SELECT xml FROM cache WHERE hash = '".sqlite_escape_string($hash)."'"
		);

		return sqlite_fetch_string($result);
	}

	public function remove($hash){
		sqlite_exec($this->sqlite,
			"DELETE FROM cache WHERE hash = '".sqlite_escape_string($hash)."'"
		);
	}

	public function store($hash, $data, $expiration){
		sqlite_exec($this->sqlite,
			"INSERT OR REPLACE INTO cache VALUES(
			'".sqlite_escape_string($hash)."',
			'".sqlite_escape_string($data)."',
			'".intval($expiration)."'
			)"
		);
	}

	public function clear(){
		sqlite_exec($this->sqlite, "DELETE FROM cache");
	}

	public function isExpired($hash){
		$result = sqlite_query($this->sqlite,
			"SELECT expiration FROM cache WHERE hash = '".sqlite_escape_string($hash)."'"
		);

		$expiration = sqlite_fetch_string($result);

		return (time() > intval($expiration));
	}
}

?>
