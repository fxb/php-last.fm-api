<?php

/** A SQLite database cache.
 *
 * @package	php-lastfm-api
 * @author  Felix Bruns <felixbruns@web.de>
 * @version	1.0
 */
final class SqliteCache extends Cache {
	/** The SQLite database handle.
	 *
	 * @var		string
	 * @access	private
	 */
	private $sqlite;

	/** Constructor that sets up the SqliteCache.
	 *
	 * @param	string	$database	The database file to use.
	 * @access	public
	 */
	public function __construct($database){
		parent::__construct();

		$this->sqlite = sqlite_open($database);

		@sqlite_query($this->sqlite,
			"CREATE TABLE cache (hash VARCAHR(40) PRIMARY KEY, xml TEXT, expiration INTEGER);"
		);
	}

	/** Destructor that closes the SQLite database.
	 *
	 * @access	public
	 */
	public function __destruct(){
		sqlite_close($this->sqlite);
	}

	/** Checks if data associated with a hash exists in the cache.
	 *
	 * @param	string	$hash	The hash of the entry to be checked.
	 * @return	boolean			true if the entry exists, otherwise false.
	 * @access	public
	 */
	public function contains($hash){
		$result = sqlite_query($this->sqlite,
			"SELECT 1 FROM cache WHERE hash = '".sqlite_escape_string($hash)."'"
		);

		return sqlite_num_rows($result) > 0;
	}

	/** Loads data from the cache.
	 *
	 * @param	string	$hash	The hash of the entry to be loaded.
	 * @return	string			The cached data.
	 * @access	public
	 */
	public function load($hash){
		$result = sqlite_query($this->sqlite,
			"SELECT xml FROM cache WHERE hash = '".sqlite_escape_string($hash)."'"
		);

		return sqlite_fetch_string($result);
	}

	/** Removes data from the cache.
	 *
	 * @param	string	$hash	The hash of the entry to be removed.
	 * @access	public
	 */
	public function remove($hash){
		sqlite_exec($this->sqlite,
			"DELETE FROM cache WHERE hash = '".sqlite_escape_string($hash)."'"
		);
	}

	/** Stores data in the cache.
	 *
	 * @param	string	$hash		The hash of the data to be stored.
	 * @param	string	$data		The data to be stored.
	 * @param	string	$expiration	The expiration time of the data (unix timestamp).
	 * @access	public
	 */
	public function store($hash, $data, $expiration){
		sqlite_exec($this->sqlite,
			"INSERT OR REPLACE INTO cache VALUES(
			'".sqlite_escape_string($hash)."',
			'".sqlite_escape_string($data)."',
			'".intval($expiration)."'
			)"
		);
	}

	/** Removes all data from the cache.
	 *
	 * @access	public
	 */
	public function clear(){
		sqlite_exec($this->sqlite, "DELETE FROM cache");
	}

	/** Checks if data associated with a hash is expired.
	 *
	 * @param	string	$hash	The hash of the entry to be checked.
	 * @return	boolean			true if the entry is expired, otherwise false.
	 * @access	public
	 */
	public function isExpired($hash){
		$result = sqlite_query($this->sqlite,
			"SELECT expiration FROM cache WHERE hash = '".sqlite_escape_string($hash)."'"
		);

		$expiration = sqlite_fetch_string($result);

		return (time() > intval($expiration));
	}
}


