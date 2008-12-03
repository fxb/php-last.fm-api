<?

/*
http://ws.audioscrobbler.com/2.0/?method=playlist.fetch&playlistURL=lastfm://playlist/artist/MGMT&api_key=b25b959554ed76058ac220b7b2e0a026
http://ws.audioscrobbler.com/2.0/?method=playlist.fetch&playlistURL=lastfm://playlist/artist/MGMT&api_key=b25b959554ed76058ac220b7b2e0a026&streaming=true
http://ws.audioscrobbler.com/2.0/?method=playlist.fetch&playlistURL=lastfm://playlist/artist/MGMT&api_key=b25b959554ed76058ac220b7b2e0a026&streaming=true&fod=true
*/

class Playlist {
	private $id;
	private $title;
	private $annotation;
	private $creator;
	private $date;
	private $tracks;
	private $size;
	private $streamable;
	
	public function __construct($id, $title, $annotation, $creator, $date,
								array $tracks, $size, $streamable){
		$this->id         = $id;
		$this->title      = $title;
		$this->annotation = $annotation;
		$this->creator    = $creator;
		$this->date       = $date;
		$this->tracks     = $tracks;
		$this->size       = $size;
		$this->streamable = $streamable;
	}
	
	public function getId(){
		return $this->id;
	}
	
	public function getTitle(){
		return $this->title;
	}
	
	public function getAnnotation(){
		return $this->annotation;
	}
	
	public function getCreator(){
		return $this->creator;
	}
	
	public function getDate(){
		return $this->date;
	}
	
	public function getTracks(){
		return $this->tracks;
	}
	
	public function getTrack($index){
		return $this->tracks[$index];
	}
	
	public function getSize(){
		return $this->size;
	}
	
	public function isStreamable(){
		return $this->streamable;
	}
	
	public static function addTrack($id, $artist, $track, $session){
		$xml = Caller::getInstance()->signedCall('playlist.addTrack', array(
			'playlistID' => $id,
			'artist'     => $artist,
			'track'      => $track,
			'api_key'    => $session->getApiKey(),
			'sk'         => $session->getKey()
		), $session->getApiSecret(), 'POST');
		
		return $xml;
	}
	
	public static function fetch($playlist, $streaming, $fod, $apiKey){
		$xml = Caller::getInstance()->call('playlist.fetch', array(
			'playlistURL' => $playlist,
			'streaming'   => $streaming,
			'fod'         => $fod, // free on demand
			'api_key'     => $apiKey // anonymous
		));
		
		if($xml !== false){
			return self::fromSimpleXMLElement($xml);
		}
		else{
			return false;
		}
	}
	
	public static function fetchWithSession($playlist, $streaming, $fod, $session){
		$xml = Caller::getInstance()->call('playlist.fetch', array(
			'playlistURL' => $playlist,
			'streaming'   => $streaming,
			'fod'         => $fod, // free on demand
			'api_key'     => $session->getApiKey(),
			'sk'          => $session->getKey() // user bound
		));
		
		if($xml !== false){
			return self::fromSimpleXMLElement($xml);
		}
		else{
			return false;
		}
	}
	
	public static function fromSimpleXMLElement(SimpleXMLElement $xml){
		$tracks = array();
		
		foreach($xml->trackList->children() as $track){
			$tracks[] = Track::fromSimpleXMLElement($track);
		}
		
		return new Playlist(
			Util::toInteger($xml->id),
			Util::toString($xml->title),
			Util::toString($xml->annotation),
			Util::toString($xml->creator),
			Util::toTimestamp($xml->date),
			$tracks,
			Util::toInteger($xml->size),
			Util::toInteger($xml->streamable)
		);
	}
}

?>
