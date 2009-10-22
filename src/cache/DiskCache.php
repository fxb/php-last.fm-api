<?php

/** A disk cache.
 *
 * @package	php-lastfm-api
 * @author  Felix Bruns <felixbruns@web.de>
 * @version	1.0
 */
final class DiskCache extends Cache {
	/** The directory where the cache data is stored.
	 *
	 * @var		string
	 * @access	private
	 */
	private $directory;

	/** Constructor that sets up the DiskCache.
	 *
	 * @param	string	$directory	The directory to use. (Optional)
	 * @access	public
	 */
	public function __construct($directory = null){
		parent::__construct();

		if($directory != null){
			$this->directory = $directory;
		}
		else{
			$this->directory = sys_get_temp_dir().'/lastfm.cache';
		}

		if(!file_exists($this->directory)){
			@mkdir($this->directory);
		}

		if(!is_dir($this->directory)){
			$this->directory = dirname($this->directory);
		}
	}

	/** Checks if data associated with a hash exists in the cache.
	 *
	 * @param	string	$hash	The hash of the entry to be checked.
	 * @return	boolean			true if the entry exists, otherwise false.
	 * @access	public
	 */
	public function contains($hash){
		return file_exists($this->directory.'/'.$hash.'.xml');
	}

	/** Loads data from the cache.
	 *
	 * @param	string	$hash	The hash of the entry to be loaded.
	 * @return	string			The cached data.
	 * @access	public
	 */
	public function load($hash){
		return @file_get_contents($this->directory.'/'.$hash.'.xml');
	}

	/** Removes data from the cache.
	 *
	 * @param	string	$hash	The hash of the entry to be removed.
	 * @access	public
	 */
	public function remove($hash){
		@unlink($this->directory.'/'.$hash.'.xml');
		@unlink($this->directory.'/'.$hash.'.meta');
	}

	/** Stores data in the cache.
	 *
	 * @param	string	$hash		The hash of the data to be stored.
	 * @param	string	$data		The data to be stored.
	 * @param	string	$expiration	The expiration time of the data (unix timestamp).
	 * @access	public
	 */
	public function store($hash, $data, $expiration){
		file_put_contents($this->directory.'/'.$hash.'.xml', $data);
		file_put_contents($this->directory.'/'.$hash.'.meta', $expiration);
	}

	/** Removes all data from the cache.
	 *
	 * @access	public
	 */
	public function clear(){
		@rmdir($this->directory);
		@mkdir($this->directory);
	}

	/** Checks if data associated with a hash is expired.
	 *
	 * @param	string	$hash	The hash of the entry to be checked.
	 * @return	boolean			true if the entry is expired, otherwise false.
	 * @access	public
	 */
	public function isExpired($hash){
		$expiration = @file_get_contents($this->directory.'/'.$hash.'.meta');

		return (time() > intval($expiration));
	}
}


