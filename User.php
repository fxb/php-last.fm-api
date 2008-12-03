<?

class User extends Media {
	private $language;
	private $country;
	private $age;
	private $gender;
	private $subscriber;
	private $playlists;
	private $lastTrack;
	private $match;
	private $weight;
	
	const PERIOD_OVERALL  = 'overall';
	const PERIOD_3MONTHS  = '3month';
	const PERIOD_6MONTHS  = '6month';
	const PERIOD_12MONTHS = '12month';
	
	public function __construct($name, $url, $language, $country, $age, $gender,
								$subscriber, $playCount, $playlists, $images,
								$lastTrack, $match, $weight){
		parent::__construct($name, '', $url, $images, 0, $playCount);
		
		$this->language   = $language;
		$this->country    = $country;
		$this->age        = $age;
		$this->gender     = $gender;
		$this->subscriber = $subscriber;
		$this->playlists  = $playlists;
		$this->lastTrack  = $lastTrack;
		$this->match      = $match;
		$this->weight     = $weight;
	}
	
	public function getLanguage(){
		return $this->language;
	}
	
	public function getCountry(){
		return $this->country;
	}
	
	public function getAge(){
		return $this->age;
	}
	
	public function getGender(){
		return $this->gender;
	}
	
	public function isSubscriber(){
		return $this->subscriber;
	}
	
	public function getPlaylists(){
		return $this->playlists;
	}
	
	public function getLastTrack(){
		return $this->lastTrack;
	}
	
	public function getMatch(){
		return $this->match;
	}
	
	public function getWeight(){
		return $this->weight;
	}
	
	public static function getEvents($user, $apiKey){
		$xml = Caller::getInstance()->call('user.getEvents', array(
			'user'    => $user,
			'api_key' => $apiKey
		));
		
		$events = array();
		
		foreach($xml->children() as $event){
			$events[] = Event::fromSimpleXMLElement($event);
		}
		
		return $events;
	}
	
	public static function getFriends($user, $recentTracks, $limit, $apiKey){
		$xml = Caller::getInstance()->call('user.getFriends', array(
			'user'         => $user,
			'recenttracks' => $recenttracks,
			'limit'        => $limit,
			'api_key'      => $apiKey
		));
		
		$friends = array();
		
		foreach($xml->children() as $friend){
			$friends[] = User::fromSimpleXMLElement($friend);
		}
		
		return $friends;
	}
	
	public static function getInfo($user, $session){
		$xml = Caller::getInstance()->signedCall('user.getInfo', array(
			'user'         => $user,
			'recenttracks' => $recenttracks,
			'limit'        => $limit,
			'api_key'      => $session->getApiKey(),
			'sk'           => $session->getKey()
		), $session->getApiSecret());
		
		if($xml !== false){
			return self::fromSimpleXMLElement($xml);
		}
		else{
			return false;
		}
	}
	
	public static function getLovedTracks($user, $apiKey){
		$xml = Caller::getInstance()->call('user.getLovedTracks', array(
			'user'    => $user,
			'api_key' => $apiKey
		));
		
		$tracks = array();
		
		foreach($xml->children() as $track){
			$tracks[] = Track::fromSimpleXMLElement($track);
		}
		
		return $tracks;
	}
	
	public static function getNeighbours($user, $limit, $apiKey){
		$xml = Caller::getInstance()->call('user.getNeighbours', array(
			'user'    => $user,
			'limit'   => $limit,
			'api_key' => $apiKey
		));
		
		$neighbours = array();
		
		foreach($xml->children() as $neighbour){
			$neighbours[] = User::fromSimpleXMLElement($neighbour);
		}
		
		return $neighbours;
	}
	
	public static function getPastEvents($user, $limit, $page, $apiKey){
		$xml = Caller::getInstance()->call('user.getPastEvents', array(
			'user'    => $user,
			'limit'   => $limit,
			'page'    => $page,
			'api_key' => $apiKey
		));
		
		$events = array();
		
		foreach($xml->children() as $event){
			$events[] = Event::fromSimpleXMLElement($event);
		}
		
		return $event;
	}
	
	public static function getPlaylists($user, $apiKey){
		$xml = Caller::getInstance()->call('user.getPlaylists', array(
			'user'    => $user,
			'api_key' => $apiKey
		));
		
		$playlists = array();
		
		foreach($xml->children() as $playlist){
			$playlists[] = Playlist::fromSimpleXMLElement($playlist);
		}
		
		return $playlist;
	}
	
	public static function getRecentTracks($user, $limit, $apiKey){
		$xml = Caller::getInstance()->call('user.getRecentTracks', array(
			'user'    => $user,
			'limit'   => $limit,
			'api_key' => $apiKey
		));
		
		$tracks = array();
		
		foreach($xml->children() as $track){
			$tracks[] = Track::fromSimpleXMLElement($track);
		}
		
		return $tracks;
	}
	
	public static function getTopAlbums($user, $period, $apiKey){
		$xml = Caller::getInstance()->call('user.getTopAlbums', array(
			'user'    => $user,
			'period'  => $period,
			'api_key' => $apiKey
		));
		
		$albums = array();
		
		foreach($xml->children() as $album){
			$albums[] = Album::fromSimpleXMLElement($album);
		}
		
		return $albums;
	}
	
	public static function getTopArtists($user, $period, $apiKey){
		$xml = Caller::getInstance()->call('user.getTopArtists', array(
			'user'    => $user,
			'period'  => $period,
			'api_key' => $apiKey
		));
		
		$artists = array();
		
		foreach($xml->children() as $artist){
			$artists[] = Artist::fromSimpleXMLElement($artist);
		}
		
		return $artists;
	}
	
	public static function getTopTags($user, $limit, $apiKey){
		$xml = Caller::getInstance()->call('user.getTopTags', array(
			'user'    => $user,
			'limit'   => $limit,
			'api_key' => $apiKey
		));
		
		$tags = array();
		
		foreach($xml->children() as $tag){
			$tags[] = Tag::fromSimpleXMLElement($tag);
		}
		
		return $tags;
	}
	
	public static function getTopTracks($user, $period, $apiKey){
		$xml = Caller::getInstance()->call('user.getTopTracks', array(
			'user'    => $user,
			'period'  => $period,
			'api_key' => $apiKey
		));
		
		$tracks = array();
		
		foreach($xml->children() as $track){
			$tracks[] = Track::fromSimpleXMLElement($track);
		}
		
		return $tracks;
	}
	
	public static function getWeeklyAlbumChart($user, $from, $to, $apiKey){
		$xml = Caller::getInstance()->call('user.getWeeklyAlbumChart', array(
			'user'   => $user,
			'from'    => $from,
			'to'      => $to,
			'api_key' => $apiKey
		));
		
		$albums = array();
		
		foreach($xml->children() as $album){
			$albums[] = Album::fromSimpleXMLElement($album);
		}
		
		return $albums;
	}
	
	public static function getWeeklyArtistChart($user, $from, $to, $apiKey){
		$xml = Caller::getInstance()->call('user.getWeeklyArtistChart', array(
			'user'    => $user,
			'from'    => $from,
			'to'      => $to,
			'api_key' => $apiKey
		));
		
		$artists = array();
		
		foreach($xml->children() as $artist){
			$artists[] = Artist::fromSimpleXMLElement($artist);
		}
		
		return $artists;
	}
	
	public static function getWeeklyChartList($user, $from, $to, $apiKey){
		$xml = Caller::getInstance()->call('user.getWeeklyChartList', array(
			'user'    => $user,
			'api_key' => $apiKey
		));
		
		$chartList = array();
		
		foreach($xml->children() as $chart){
			$chartList[] = array(
				'from' => Util::toInteger($chart['from']),
				'to'   => Util::toInteger($chart['to']),
			);
		}
		
		return $chartList;
	}
	
	public static function getWeeklyTrackChart($user, $from, $to, $apiKey){
		$xml = Caller::getInstance()->call('user.getWeeklyTrackChart', array(
			'user'    => $user,
			'from'    => $from,
			'to'      => $to,
			'api_key' => $apiKey
		));
		
		$tracks = array();
		
		foreach($xml->children() as $track){
			$tracks[] = Track::fromSimpleXMLElement($track);
		}
		
		return $tracks;
	}
	
	public static function fromSimpleXMLElement(SimpleXMLElement $xml){
		$images = array();
		
		foreach($xml->image as $image){
			$images[Util::toImageType($image['size'])] = Util::toString($image);
		}
		
		return new User(
			Util::toString($xml->name),
			Util::toString($xml->url),
			Util::toString($xml->lang),
			Util::toString($xml->country),
			Util::toInteger($xml->age),
			Util::toString($xml->gender),
			Util::toInteger($xml->subscriber),
			Util::toInteger($xml->playcount),
			Util::toInteger($xml->playlists),
			$images,
			($xml->recenttrack)?
				Track::fromSimpleXMLElement($xml->recenttrack):null,
			Util::toFloat($xml->match),
			Util::toInteger($xml->weight)
		);
	}
}

?>
