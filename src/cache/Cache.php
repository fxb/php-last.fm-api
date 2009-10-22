<?php

/** A cache.
 *
 * @package	php-lastfm-api
 * @author  Felix Bruns <felixbruns@web.de>
 * @version	1.0
 */
abstract class Cache {
	/** The CachePolicy that's used.
	 *
	 * @var		CachePolicy
	 * @access	private
	 */
	private $policy;

	/** Protected constructor that sets a DefaultCachePolicy.
	 *
	 * @access	protected
	 */
	protected function __construct(){
		$this->policy = new DefaultCachePolicy();
	}

	/** Returns the CachePolicy that's used.
	 *
	 * @return	CachePolicy	A CachePolicy object.
	 * @access	public
	 * @see		CachePolicy
	 */
	public function getPolicy(){
		return $this->policy;
	}

	/** Sets the CachePolicy to be used.
	 *
	 * @param	CachePolicy	$policy	A CachePolicy object.
	 * @access	public
	 * @see		CachePolicy
	 */
	public function setPolicy(CachePolicy $policy){
		$this->policy = $policy;
	}

	/** Checks if data associated with a hash exists in the cache.
	 *
	 * @param	string	$hash	The hash of the entry to be checked.
	 * @return	boolean			true if the entry exists, otherwise false.
	 * @access	public
	 */
	public abstract function contains($hash);

	/** Loads data from the cache.
	 *
	 * @param	string	$hash	The hash of the entry to be loaded.
	 * @return	string			The cached data.
	 * @access	public
	 */
	public abstract function load($hash);

	/** Removes data from the cache.
	 *
	 * @param	string	$hash	The hash of the entry to be removed.
	 * @access	public
	 */
	public abstract function remove($hash);

	/** Stores data in the cache.
	 *
	 * @param	string	$hash		The hash of the data to be stored.
	 * @param	string	$data		The data to be stored.
	 * @param	string	$expiration	The expiration time of the data (unix timestamp).
	 * @access	public
	 */
	public abstract function store($hash, $data, $expiration);

	/** Checks if data associated with a hash is expired.
	 *
	 * @param	string	$hash	The hash of the entry to be checked.
	 * @return	boolean			true if the entry is expired, otherwise false.
	 * @access	public
	 */
	public abstract function isExpired($hash);

	/** Removes all data from the cache.
	 *
	 * @access	public
	 */
	public abstract function clear();

	/** Creates a hash from last.fm API request parameters.
	 *
	 * @param	array	$params	An associative array of last.fm API request parameters.
	 * @return	string			A calculated hexadecimal SHA1 hash.
	 *
	 * @static
	 * @access	public
	 */
	public static function createHash($params){
		$string = '';

		sort($params);

		foreach($params as $param => $value){
			$string .= $param.$value;
		}

		return sha1($string);
	}
}


