<?

class Media {
	private $name;
	private $mbid;
	private $url;
	private $images;
	private $listeners;
	private $playCount;
	
	const IMAGE_UNKNOWN = -1;
	const IMAGE_SMALL   =  0;
	const IMAGE_MEDIUM  =  1;
	const IMAGE_LARGE   =  2;
	
	public function __construct($name, $mbid, $url, array $images, $listeners,
								$playCount){
		$this->name      = $name;
		$this->mbid      = $mbid;
		$this->url       = $url;
		$this->images    = $images;
		$this->listeners = $listeners;
		$this->playCount = $playCount;
	}
	
	public function getName(){
		return $this->name;
	}
	
	public function getMbid(){
		return $this->mbid;
	}
	
	public function getUrl(){
		return $this->url;
	}
	
	public function getImage($size){
		return $this->images[$size];
	}
	
	public function getListeners(){
		return $this->listeners;
	}
	
	public function getPlayCount(){
		return $this->playCount;
	}
}

?>
