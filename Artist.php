<?

class Artist extends Media {
	private $streamable;
	private $similar;
	private $tags;
	private $biography;
	private $match;
	
	public function __construct($name, $mbid, $url, array $images, $streamable,
								$listeners, $playCount, array $tags,
								array $similar, $biography, $match){
		parent::__construct($name, $mbid, $url, $images, $listeners, $playCount);
		
		$this->streamable = $streamable;
		$this->tags       = $tags;
		$this->similar    = $similar;
		$this->biography  = $biography;
		$this->match      = $match;
	}
	
	public function isStreamable(){
		return $this->streamable;
	}
	
	public function _getSimilar(){
		return $this->similar;
	}
	
	public function _getTags(){
		return $this->tags;
	}
	
	public function getBiography(){
		return $this->biography;
	}
	
	public function getMatch(){
		return $this->match;
	}
	
	public static function addTags($artist, array $tags, Session $session){
		$xml = Caller::getInstance()->signedCall('artist.addTags', array(
			'artist'  => $artist,
			'tags'    => implode(',', $tags),
			'api_key' => $session->getApiKey(),
			'sk'      => $session->getKey()
		), $session->getApiSecret(), 'POST');
		
		return $xml;
	}
	
	public static function getEvents($artist, $apiKey){
		$xml = Caller::getInstance()->call('artist.getEvents', array(
			'artist'  => $artist,
			'api_key' => $apiKey
		));
		
		$events = array();
		
		foreach($xml->children() as $event){
			$events[] = Event::fromSimpleXMLElement($event);
		}
		
		return $events;
	}
	
	public static function getInfo($artist, $mbid, $apiKey){
		$xml = Caller::getInstance()->call('artist.getInfo', array(
			'artist'  => $artist,
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
	
	public static function getSimilar($artist, $limit, $apiKey){
		$xml = Caller::getInstance()->call('artist.getEvents', array(
			'artist'  => $artist,
			'limit'   => $limit,
			'api_key' => $apiKey
		));
		
		$artists = array();
		
		foreach($xml->children() as $artist){
			$artists[] = Artist::fromSimpleXMLElement($artist);
		}
		
		return $artists;
	}
	
	public static function getTags($artist, Session $session){
		$xml = Caller::getInstance()->signedCall('artist.getTags', array(
			'artist'  => $artist,
			'api_key' => $session->getApiKey(),
			'sk'      => $session->getKey()
		), $session->getApiSecret());
		
		$tags = array();
		
		foreach($xml->children() as $tag){
			$tags[] = Tag::fromSimpleXMLElement($tag);
		}
		
		return $tags;
	}
	
	public static function getTopAlbums($artist, $apiKey){
		$xml = Caller::getInstance()->call('artist.getTopAlbums', array(
			'artist'  => $artist,
			'api_key' => $apiKey
		));
		
		$albums = array();
		
		foreach($xml->children() as $album){
			$albums[] = Album::fromSimpleXMLElement($album);
		}
		
		return $albums;
	}

	public static function getTopFans($artist, $apiKey){
		$xml = Caller::getInstance()->call('artist.getTopFans', array(
			'artist'  => $artist,
			'api_key' => $apiKey
		));
		
		$fans = array();
		
		foreach($xml->children() as $fan){
			$fans[] = User::fromSimpleXMLElement($fan);
		}
		
		return $fans;
	}
	
	public static function getTopTags($artist, $apiKey){
		$xml = Caller::getInstance()->call('artist.getTopTags', array(
			'artist'  => $artist,
			'api_key' => $apiKey
		));
		
		$tags = array();
		
		foreach($xml->children() as $tag){
			$tags[] = Tag::fromSimpleXMLElement($tag);
		}
		
		return $tags;
	}
	
	public static function getTopTracks($artist, $apiKey){
		$xml = Caller::getInstance()->call('artist.getTopTracks', array(
			'artist'  => $artist,
			'api_key' => $apiKey
		));
		
		$tracks = array();
		
		foreach($xml->children() as $track){
			$tracks[] = Track::fromSimpleXMLElement($track);
		}
		
		return $tracks;
	}
	
	public static function removeTag($artist, $tag, Session $session){
		$xml = Caller::getInstance()->signedCall('artist.removeTag', array(
			'artist'  => $artist,
			'tag'     => $tag,
			'api_key' => $session->getApiKey(),
			'sk'      => $session->getKey()
		), $session->getApiSecret(), 'POST');
		
		return $xml;
	}
	
	public static function search($artist, $limit, $page, $apiKey){
		$xml = Caller::getInstance()->call('artist.search', array(
			'artist'  => $artist,
			'limit'   => $limit,
			'page'    => $page,
			'api_key' => $apiKey
		));
		
		$artists = array();
		
		foreach($xml->artistmatches->children() as $artist){
			$artists[] = Artist::fromSimpleXMLElement($artist);
		}
		
		$opensearch = $xml->children('http://a9.com/-/spec/opensearch/1.1/');
		
		return new PaginatedResult(
			Util::toInteger($opensearch->totalResults),
			Util::toInteger($opensearch->startIndex),
			Util::toInteger($opensearch->itemsPerPage),
			$artists
		);
	}
	
	public static function share($artist, array $recipients, $message, Session $session){
		$xml = Caller::getInstance()->signedCall('artist.share', array(
			'artist'    => $artist,
			'recipient' => implode(',', $recipients),
			'message'   => $message,
			'api_key'   => $session->getApiKey(),
			'sk'        => $session->getKey()
		), $session->getApiSecret(), 'POST');
		
		return $xml;
	}
	
	public static function getPlaylist($artist, $apiKey){
		$xml = Caller::getInstance()->call('artist.getPlayerMenu', array(
			'artist'  => $artist,
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
		$tags    = array();
		$similar = array();
		
		/* NOTE: image, image_small... this sucks! */
		
		if($xml->image){
			if(count($xml->image) > 1){
				foreach($xml->image as $image){
					$images[Util::toImageType($image['size'])] = Util::toString($image);
				}
			}
			else{
				$images[Media::IMAGE_LARGE] = Util::toString($image);
			}
		}
		
		if($xml->image_small){
			$images[Media::IMAGE_SMALL] = Util::toString($xml->image_small);
		}
		
		if($xml->tags){
			foreach($xml->tags->children() as $tag){
				$tags[] = Tag::fromSimpleXMLElement($tag);
			}
		}
		
		if($xml->similar){
			foreach($xml->similar->children() as $artist){
				$similar[] = Artist::fromSimpleXMLElement($artist);
			}
		}
		
		return new Artist(
			Util::toString($xml->name),
			Util::toString($xml->mbid),
			Util::toString($xml->url),
			$images,
			Util::toInteger($xml->streamable),
			($xml->stats)?Util::toInteger($xml->stats->listeners):0,
			($xml->stats)?Util::toInteger($xml->stats->playcount):0,
			$tags,
			$similar,
			($xml->bio)?Util::toString($xml->bio->summary):"", // TODO: Biography object
			Util::toFloat($xml->match)
		);
	}
}

?>
