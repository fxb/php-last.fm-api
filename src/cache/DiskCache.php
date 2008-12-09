<?

/** A disk cache.
 *
 * @package	php-lastfm-api
 * @author  Felix Bruns <felixbruns@web.de>
 * @version	1.0
 */
final class DiskCache extends Cache {
	private $directory;

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

	public function contains($hash){
		return file_exists($this->directory.'/'.$hash.'.xml');
	}

	public function load($hash){
		return @file_get_contents($this->directory.'/'.$hash.'.xml');
	}

	public function remove($hash){
		@unlink($this->directory.'/'.$hash.'.xml');
		@unlink($this->directory.'/'.$hash.'.meta');
	}

	public function store($hash, $data, $expiration){
		file_put_contents($this->directory.'/'.$hash.'.xml', $data);
		file_put_contents($this->directory.'/'.$hash.'.meta', $expiration);
	}

	public function clear(){
		@rmdir($this->directory);
		@mkdir($this->directory);
	}

	public function isExpired($hash){
		$expiration = @file_get_contents($this->directory.'/'.$hash.'.meta');

		return (time() > intval($expiration));
	}
}

?>
