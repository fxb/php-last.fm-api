<?php

/** A memory cache.
 *
 * @package	php-lastfm-api
 * @author  Felix Bruns <felixbruns@web.de>
 * @version	1.0
 */
final class MemoryCache extends Cache {
	/** The array where the cache data is stored.
	 *
	 * @var		array
	 * @access	private
	 */
	private $cache;

	/** Constructor that sets up the MemoryCache.
	 *
	 * @access	public
	 */
	public function __construct(){
		parent::__construct();

		$this->cache = array();
	}

	/** Checks if data associated with a hash exists in the cache.
	 *
	 * @param	string	$hash	The hash of the entry to be checked.
	 * @return	boolean			true if the entry exists, otherwise false.
	 * @access	public
	 */
	public function contains($hash){
		return array_key_exists($hash, $this->cache);
	}

	/** Loads data from the cache.
	 *
	 * @param	string	$hash	The hash of the entry to be loaded.
	 * @return	string			The cached data.
	 * @access	public
	 */
	public function load($hash){
		return $this->cache[$hash]['data'];
	}

	/** Removes data from the cache.
	 *
	 * @param	string	$hash	The hash of the entry to be removed.
	 * @access	public
	 */
	public function remove($hash){
		unset($this->cache[$hash]);
	}

	/** Stores data in the cache.
	 *
	 * @param	string	$hash		The hash of the data to be stored.
	 * @param	string	$data		The data to be stored.
	 * @param	string	$expiration	The expiration time of the data (unix timestamp).
	 * @access	public
	 */
	public function store($hash, $data, $expiration){
		$this->cache[$hash] = array(
			'data'       => $data,
			'expiration' => $expiration
		);
	}

	/** Removes all data from the cache.
	 *
	 * @access	public
	 */
	public function clear(){
		$this->cache = array();
	}

	/** Checks if data associated with a hash is expired.
	 *
	 * @param	string	$hash	The hash of the entry to be checked.
	 * @return	boolean			true if the entry is expired, otherwise false.
	 * @access	public
	 */
	public function isExpired($hash){
		$expiration = $this->cache[$hash]['expiration'];

		return (time() > intval($expiration));
	}
}


