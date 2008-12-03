<?

class Track extends Media {
	private $artist;
	private $album;
	private $duration;
	private $topTags;
	private $id;
	private $location;
	private $streamable;
	private $fullTrack;
	private $wiki;
	private $lastPlayed;
	
	public function __construct($artist, $album, $name, $mbid, $url,
								array $images, $listeners, $playCount,
								$duration, array $topTags, $id, $location,
								$streamable, $fullTrack, $wiki, $lastPlayed){
		parent::__construct($name, $mbid, $url, $images, $listeners, $playCount);
		
		$this->artist     = $artist;
		$this->album      = $album;
		$this->duration   = $duration;
		$this->topTags    = $topTags;
		$this->id         = $id;
		$this->location   = $location;
		$this->streamable = $streamable;
		$this->fullTrack  = $fullTrack;
		$this->wiki       = $wiki;
		$this->lastPlayed = $lastPlayed;
	}
	
	public function getArtist(){
		return $this->artist;
	}
	
	public function getAlbum(){
		return $this->album;
	}
	
	public function getDuration(){
		return $this->duration;
	}
	
	public function _getTopTags(){
		return $this->topTags;
	}
	
	public function getId(){
		return $this->id;
	}
	
	public function getLocation(){
		return $this->location;
	}
	
	public function isStreamable(){
		return $this->streamable;
	}
	
	public function isFullTrack(){
		return $this->fullTrack;
	}
	
	public function getWiki(){
		return $this->wiki;
	}
	
	public function getLastPlayed(){
		return $this->lastPlayed;
	}
	
	public static function addTags($artist, $track, array $tags, $session){
		$xml = Caller::getInstance()->signedCall('track.addTags', array(
			'artist'  => $artist,
			'track'   => $track,
			'tags'    => implode(',', $tags),
			'api_key' => $session->getApiKey(),
			'sk'      => $session->getKey()
		), $session->getApiSecret(), 'POST');
		
		return $xml;
	}
	
	public static function ban($artist, $track, $session){
		$xml = Caller::getInstance()->signedCall('track.ban', array(
			'artist'  => $artist,
			'track'   => $track,
			'api_key' => $session->getApiKey(),
			'sk'      => $session->getKey()
		), $session->getApiSecret(), 'POST');
		
		return $xml;
	}
	
	public static function getInfo($artist, $track, $mbid, $apiKey){
		$xml = Caller::getInstance()->call('track.getInfo', array(
			'artist'  => $artist,
			'track'   => $track,
			'mbid'    => $mbid,
			'api_key' => $apiKey
		));
		
		if($xml !== false){
			return self::fromSimpleXMLElement($xml);
		}
		else{
			return false;
		}
	}
	
	public static function getSimilar($artist, $track, $mbid, $apiKey){
		$xml = Caller::getInstance()->call('track.getSimilar', array(
			'artist'  => $artist,
			'track'   => $track,
			'mbid'    => $mbid,
			'api_key' => $apiKey
		));
		
		$tracks = array();
		
		foreach($xml->children() as $track){
			$tracks[] = Track::fromSimpleXMLElement($track);
		}
		
		return $tracks;
	}
	
	public static function getTags($artist, $track, $session){
		$xml = Caller::getInstance()->signedCall('track.getTags', array(
			'artist'  => $artist,
			'track'   => $track,
			'api_key' => $session->getApiKey(),
			'sk'      => $session->getKey()
		), $session->getApiSecret());
		
		$tags = array();
		
		foreach($xml->children() as $tag){
			$tags[] = Tag::fromSimpleXMLElement($tag);
		}
		
		return $tags;
	}
	
	public static function getTopFans($artist, $track, $mbid, $apiKey){
		$xml = Caller::getInstance()->call('track.getTopFans', array(
			'artist'  => $artist,
			'track'   => $track,
			'mbid'    => $mbid,
			'api_key' => $apiKey
		));
		
		$users = array();
		
		foreach($xml->children() as $user){
			$users[] = User::fromSimpleXMLElement($user);
		}
		
		return $users;
	}
	
	public static function getTopTags($artist, $track, $mbid, $apiKey){
		$xml = Caller::getInstance()->call('track.getTopTags', array(
			'artist'  => $artist,
			'track'   => $track,
			'mbid'    => $mbid,
			'api_key' => $apiKey
		));
		
		$tags = array();
		
		foreach($xml->children() as $tag){
			$tags[] = Tag::fromSimpleXMLElement($tag);
		}
		
		return $tags;
	}
	
	public static function love($artist, $track, $session){
		$xml = Caller::getInstance()->signedCall('track.love', array(
			'artist'  => $artist,
			'track'   => $track,
			'api_key' => $session->getApiKey(),
			'sk'      => $session->getKey()
		), $session->getApiSecret(), 'POST');
		
		return $xml;
	}
	
	public static function removeTag($artist, $track, $tag, $session){
		$xml = Caller::getInstance()->signedCall('track.removeTag', array(
			'artist'  => $artist,
			'track'   => $track,
			'tag'     => $tag,
			'api_key' => $session->getApiKey(),
			'sk'      => $session->getKey()
		), $session->getApiSecret(), 'POST');
		
		return $xml;
	}
	
	public static function search($artist, $track, $limit, $page, $apiKey){
		$xml = Caller::getInstance()->call('track.search', array(
			'artist'  => $artist,
			'track'   => $track,
			'limit'   => $limit,
			'page'    => $page,
			'api_key' => $apiKey
		));
		
		$tracks = array();
		
		foreach($xml->trackmatches->children() as $track){
			$tracks[] = Track::fromSimpleXMLElement($track);
		}
		
		$opensearch = $xml->children('http://a9.com/-/spec/opensearch/1.1/');
		
		return new PaginatedResult(
			Util::toInteger($opensearch->totalResults),
			Util::toInteger($opensearch->startIndex),
			Util::toInteger($opensearch->itemsPerPage),
			$tracks
		);
	}
	
	public static function share($artist, $track, array $recipients, $message,
								 $session){
		$xml = Caller::getInstance()->signedCall('track.share', array(
			'artist'    => $artist,
			'track'     => $track,
			'recipient' => implode(',', $recipients),
			'message'   => $message,
			'api_key'   => $session->getApiKey(),
			'sk'        => $session->getKey()
		), $session->getApiSecret(), 'POST');
		
		return $xml;
	}
	
	public static function getPlaylist($artist, $track, $apiKey){
		$xml = Caller::getInstance()->call('track.getPlayerMenu', array(
			'artist'  => $artist,
			'track'   => $track,
			'api_key' => $apiKey
		));
		
		return Playlist::fetch(
			Util::toString($xml->playlist->url),
			true,
			true,
			$apiKey
		);
	}
	
	public static function fromSimpleXMLElement(SimpleXMLElement $xml){
		$images  = array();
		$topTags = array();
		
		if(count($xml->image) > 1){
			foreach($xml->image as $image){
				$images[Util::toImageType($image['size'])] = Util::toString($image);
			}
		}
		else{
			$images[Media::IMAGE_UNKNOWN] = Util::toString($xml->image);
		}
		
		if($xml->toptags){
			foreach($xml->toptags->children() as $tag){
				$topTags[] = Tag::fromSimpleXMLElement($tag);
			}
		}
		
		if($xml->artist){
			if($xml->artist->name && $xml->artist->mbid && $xml->artist->url){
				$artist = new Artist(
					Util::toString($xml->artist->name),
					Util::toString($xml->artist->mbid),
					Util::toString($xml->artist->url),
					array(), 0, 0, 0, array(), array(), '', 0.0
				);
			}
			else{
				$artist = Util::toString($xml->artist);
			}
		}
		else if($xml->creator){
			$artist = Util::toString($xml->creator);
		}
		else{
			$artist = '';
		}
		
		if($xml->name){
			$name = Util::toString($xml->name);
		}
		else if($xml->title){
			$name = Util::toString($xml->title);
		}
		else{
			$name = '';
		}
		
		// TODO: <extension application="http://www.last.fm">
		
		return new Track(
			$artist,
			Util::toString($xml->album),
			$name,
			Util::toString($xml->mbid),
			Util::toString($xml->url),
			$images,
			Util::toInteger($xml->listeners),
			Util::toInteger($xml->playcount),
			Util::toInteger($xml->duration),
			$topTags,
			Util::toInteger($xml->id),
			Util::toString($xml->location),
			Util::toInteger($xml->streamable),
			Util::toInteger($xml->streamable['fulltrack']),
			$xml->wiki, // TODO: Wiki object
			Util::toTimestamp($xml->date)
		);
	}
}

?>
