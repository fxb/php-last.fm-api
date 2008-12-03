<?

/** Represents a user and provides different methods to query user information.
 *
 * @package	de.felixbruns.lastfm.api
 * @author  Felix Bruns <felixbruns@web.de>
 * @version	1.0
 */
class User extends Media {
	/** The users language.
	 *
	 * @var string
	 */
	private $language;
	
	/** The users country.
	 *
	 * @var string
	 */
	private $country;
	
	/** The users age.
	 *
	 * @var integer
	 */
	private $age;
	
	/** The users gender.
	 *
	 * @var string
	 */
	private $gender;
	
	/** Indicates if the user is a subscriber.
	 *
	 * @var boolean
	 */
	private $subscriber;
	
	/** The number of playlist of this user.
	 *
	 * @var integer
	 */
	private $playlists;
	
	/** The last track the user played.
	 *
	 * @var Track
	 */
	private $lastTrack;
	
	/** Similarity match.
	 *
	 * @var float
	 */
	private $match;
	
	/** I have no idea. Haha. TODO.
	 *
	 * @var integer
	 */
	private $weight;
	
	/** Possible time periods.
	 *
	 * @var integer
	 */
	const PERIOD_OVERALL  = 'overall';
	const PERIOD_3MONTHS  = '3month';
	const PERIOD_6MONTHS  = '6month';
	const PERIOD_12MONTHS = '12month';
	
	/** Create an Artist object.
	 *
	 * @param string	name		Username.
	 * @param string	url			Last.fm URL of this user.
	 * @param string	language	Language of this user.
	 * @param string	country		Country of this user.
	 * @param integer	age			Age of this user.
	 * @param string	gender		Gender of this user.
	 * @param boolean	subscriber	Subscriber status of this user.
	 * @param integer	playCount	Track play count of this user.
	 * @param string	playlists	Number of playlist of this user.
	 * @param array		images		An array of cover art images of different sizes.
	 * @param Track		lastTrack	Last track the user played.
	 * @param float		match		Similarity value.
	 * @param integer	weight		Still no idea.
	 */
	public function __construct($name, $url, $language, $country, $age, $gender,
								$subscriber, $playCount, $playlists,
								array $images, $lastTrack, $match, $weight){
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
	
	/** Returns the users language.
	 * 
	 * @return	string	The users language.
	 */
	public function getLanguage(){
		return $this->language;
	}
	
	/** Returns the users country.
	 * 
	 * @return	string	The users country.
	 */
	public function getCountry(){
		return $this->country;
	}
	
	/** Returns the users age.
	 * 
	 * @return	integer	The users age.
	 */
	public function getAge(){
		return $this->age;
	}
	
	/** Returns the users gender.
	 * 
	 * @return	string	The users gender.
	 */
	public function getGender(){
		return $this->gender;
	}
	
	/** Returns if the user is a subscriber.
	 * 
	 * @return	boolean	The users subscription status (true or false).
	 */
	public function isSubscriber(){
		return $this->subscriber;
	}
	
	/** Returns the number of playlists of this user.
	 * 
	 * @return	integer	Number of playlists.
	 */
	public function _getPlaylists(){
		return $this->playlists;
	}
	
	/** Returns the last played track of this user.
	 * 
	 * @return	Track	A Track object.
	 */
	public function getLastTrack(){
		return $this->lastTrack;
	}
	
	/** Returns the similarity match of this user.
	 * 
	 * @return	float	A floating-point number.
	 */
	public function getMatch(){
		return $this->match;
	}
	
	/** Returns whatever.
	 * 
	 * @return	integer	I don't even know if it's an integer right now.
	 */
	public function getWeight(){
		return $this->weight;
	}
	
	/** Get a list of upcoming events that this user is attending. Easily integratable into calendars, using the ical standard (see 'more formats' section below).
	 * 
	 * @param	string	user	The user to fetch the events for.
	 * @return	array			An array of Event objects.
	 */
	public static function getEvents($user){
		$xml = Caller::getInstance()->call('user.getEvents', array(
			'user' => $user
		));
		
		$events = array();
		
		foreach($xml->children() as $event){
			$events[] = Event::fromSimpleXMLElement($event);
		}
		
		return $events;
	}
	
	/** Get a list of the user's friends on Last.fm.
	 * 
	 * @param	string	user			The last.fm username to fetch the friends of.
	 * @param	boolean	recenttracks	Whether or not to include information about friends' recent listening in the response.
	 * @param	integer	limit			An integer used to limit the number of friends returned.
	 * @return	array					An array of User objects.
	 */
	public static function getFriends($user, $recentTracks = null, $limit = null){
		$xml = Caller::getInstance()->call('user.getFriends', array(
			'user'         => $user,
			'recenttracks' => $recenttracks,
			'limit'        => $limit
		));
		
		$friends = array();
		
		foreach($xml->children() as $friend){
			$friends[] = User::fromSimpleXMLElement($friend);
		}
		
		return $friends;
	}
	
	/** Get information about a user profile.
	 * 
	 * @param	string	user	The username in question.
	 * @param	Session	session	A session obtained by {@link de.felixbruns.lastfm.Auth#getSession Auth::getSession} or {@link de.felixbruns.lastfm.Auth#getMobileSession Auth::getMobileSession}.
	 * @return	User			A User object.
	 */
	public static function getInfo($user, $session){
		$xml = Caller::getInstance()->signedCall('user.getInfo', array(
			'user'         => $user,
			'recenttracks' => $recenttracks,
			'limit'        => $limit
		), $session);
		
		if($xml !== false){
			return User::fromSimpleXMLElement($xml);
		}
		else{
			return false;
		}
	}
	
	/** Get the last 50 tracks loved by a user.
	 * 
	 * @param	string	user	The user name to fetch the loved tracks for.
	 * @return	array			An array of Track objects.
	 */
	public static function getLovedTracks($user){
		$xml = Caller::getInstance()->call('user.getLovedTracks', array(
			'user' => $user
		));
		
		$tracks = array();
		
		foreach($xml->children() as $track){
			$tracks[] = Track::fromSimpleXMLElement($track);
		}
		
		return $tracks;
	}
	
	/** Get a list of a user's neighbours on Last.fm. 
	 * 
	 * @param	string	user	The last.fm username to fetch the neighbours of.
	 * @param	integer	limit	An integer used to limit the number of neighbours returned.
	 * @return	array			An array of Track objects.
	 */
	public static function getNeighbours($user, $limit = null){
		$xml = Caller::getInstance()->call('user.getNeighbours', array(
			'user'  => $user,
			'limit' => $limit
		));
		
		$neighbours = array();
		
		foreach($xml->children() as $neighbour){
			$neighbours[] = User::fromSimpleXMLElement($neighbour);
		}
		
		return $neighbours;
	}
	
	/** Get a paginated list of all events a user has attended in the past.
	 * 
	 * @param	string	user	The username to fetch the events for.
	 * @param	integer	limit	The number of events to return per page.
	 * @param	integer	page	The page number to scan to.
	 * @return	array			An array of Event objects.
	 */
	public static function getPastEvents($user, $limit = null, $page = null){
		$xml = Caller::getInstance()->call('user.getPastEvents', array(
			'user'  => $user,
			'limit' => $limit,
			'page'  => $page
		));
		
		$events = array();
		
		foreach($xml->children() as $event){
			$events[] = Event::fromSimpleXMLElement($event);
		}
		
		return new PaginatedResult(
			Util::toInteger($xml['total']),
			(Util::toInteger($xml['page']) - 1) *
				Util::toInteger($xml['perPage']),
			Util::toInteger($xml['perPage']),
			$events
		);
	}
	
	/** Get a list of a user's playlists on Last.fm.
	 * 
	 * @param	string	user	The last.fm username to fetch the playlists of.
	 * @return	array			An array of Playlist objects.
	 */
	public static function getPlaylists($user){
		$xml = Caller::getInstance()->call('user.getPlaylists', array(
			'user' => $user
		));
		
		$playlists = array();
		
		foreach($xml->children() as $playlist){
			$playlists[] = Playlist::fromSimpleXMLElement($playlist);
		}
		
		return $playlist;
	}
	
	/** Get a list of the recent tracks listened to by this user. Indicates now playing track if the user is currently listening.
	 * 
	 * @param	string	user	The last.fm username to fetch the recent tracks of.
	 * @param	integer	limit	An integer used to limit the number of tracks returned.
	 * @return	array			An array of Playlist objects.
	 */
	public static function getRecentTracks($user, $limit = null){
		$xml = Caller::getInstance()->call('user.getRecentTracks', array(
			'user'  => $user,
			'limit' => $limit
		));
		
		$tracks = array();
		
		foreach($xml->children() as $track){
			$tracks[] = Track::fromSimpleXMLElement($track);
		}
		
		return $tracks;
	}
	
	/** Get a paginated list of all events recommended to a user by Last.fm, based on their listening profile.
	 * 
	 * @param	string	user	The username to fetch the events for.
	 * @param	integer	limit	The number of events to return per page.
	 * @param	integer	page	The page number to scan to.
	 * @return	array			An array of Event objects.
	 */
	public static function getRecommendedEvents($user, $limit = null, $page = null){
		$xml = Caller::getInstance()->call('user.getRecommendedEvents', array(
			'user'  => $user,
			'limit' => $limit,
			'page'  => $page
		));
		
		$events = array();
		
		foreach($xml->children() as $event){
			$events[] = Event::fromSimpleXMLElement($event);
		}
		
		return new PaginatedResult(
			Util::toInteger($xml['total']),
			(Util::toInteger($xml['page']) - 1) *
				Util::toInteger($xml['perPage']),
			Util::toInteger($xml['perPage']),
			$events
		);
	}
	
	/** Get the top albums listened to by a user. You can stipulate a time period. Sends the overall chart by default.
	 * 
	 * @param	string	user	The user name to fetch top albums for.
	 * @param	integer	period	The time period over which to retrieve top albums for.
	 * @return	array			An array of Album objects.
	 */
	public static function getTopAlbums($user, $period = null){
		$xml = Caller::getInstance()->call('user.getTopAlbums', array(
			'user'   => $user,
			'period' => $period
		));
		
		$albums = array();
		
		foreach($xml->children() as $album){
			$albums[] = Album::fromSimpleXMLElement($album);
		}
		
		return $albums;
	}
	
	/** Get the top artists listened to by a user. You can stipulate a time period. Sends the overall chart by default.
	 * 
	 * @param	string	user	The user name to fetch top artists for.
	 * @param	integer	period	The time period over which to retrieve top artists for.
	 * @return	array			An array of Artist objects.
	 */
	public static function getTopArtists($user, $period = null){
		$xml = Caller::getInstance()->call('user.getTopArtists', array(
			'user'   => $user,
			'period' => $period
		));
		
		$artists = array();
		
		foreach($xml->children() as $artist){
			$artists[] = Artist::fromSimpleXMLElement($artist);
		}
		
		return $artists;
	}
	
	/** Get the top tags used by this user.
	 * 
	 * @param	string	user	The user name to fetch top tags for.
	 * @param	integer	limit	Limit the number of tags returned.
	 * @return	array			An array of Tag objects.
	 */
	public static function getTopTags($user, $limit = null){
		$xml = Caller::getInstance()->call('user.getTopTags', array(
			'user'  => $user,
			'limit' => $limit
		));
		
		$tags = array();
		
		foreach($xml->children() as $tag){
			$tags[] = Tag::fromSimpleXMLElement($tag);
		}
		
		return $tags;
	}
	
	/** Get the top tracks listened to by a user. You can stipulate a time period. Sends the overall chart by default.
	 * 
	 * @param	string	user	The user name to fetch top tracks for.
	 * @param	integer	period	The time period over which to retrieve top tracks for.
	 * @return	array			An array of Track objects.
	 */
	public static function getTopTracks($user, $period = null){
		$xml = Caller::getInstance()->call('user.getTopTracks', array(
			'user'   => $user,
			'period' => $period
		));
		
		$tracks = array();
		
		foreach($xml->children() as $track){
			$tracks[] = Track::fromSimpleXMLElement($track);
		}
		
		return $tracks;
	}
	
	/** Get an album chart for a user profile, for a given date range. If no date range is supplied, it will return the most recent album chart for this user.
	 * 
	 * @param	string	user	The last.fm username to fetch the charts of.
 	 * @param	string	from	The date at which the chart should start from. See {@link de.felixbruns.lastfm.User#getWeeklyChartList User::getWeeklyChartList} for more.
	 * @param	string	to		The date at which the chart should end on. See {@link de.felixbruns.lastfm.User#getWeeklyChartList User::getWeeklyChartList} for more.
	 * @return	array			An array of Album objects.
	 */
	public static function getWeeklyAlbumChart($user, $from = null, $to = null){
		$xml = Caller::getInstance()->call('user.getWeeklyAlbumChart', array(
			'user' => $user,
			'from' => $from,
			'to'   => $to
		));
		
		$albums = array();
		
		foreach($xml->children() as $album){
			$albums[] = Album::fromSimpleXMLElement($album);
		}
		
		return $albums;
	}
	
	/** Get an artist chart for a user profile, for a given date range. If no date range is supplied, it will return the most recent artist chart for this user.
	 * 
	 * @param	string	user	The last.fm username to fetch the charts of.
 	 * @param	string	from	The date at which the chart should start from. See {@link de.felixbruns.lastfm.User#getWeeklyChartList User::getWeeklyChartList} for more.
	 * @param	string	to		The date at which the chart should end on. See {@link de.felixbruns.lastfm.User#getWeeklyChartList User::getWeeklyChartList} for more.
	 * @return	array			An array of Artist objects.
	 */
	public static function getWeeklyArtistChart($user, $from = null, $to = null){
		$xml = Caller::getInstance()->call('user.getWeeklyArtistChart', array(
			'user' => $user,
			'from' => $from,
			'to'   => $to
		));
		
		$artists = array();
		
		foreach($xml->children() as $artist){
			$artists[] = Artist::fromSimpleXMLElement($artist);
		}
		
		return $artists;
	}
	
	/** Get a list of available charts for this user, expressed as date ranges which can be sent to the chart services.
	 * 
	 * @param	string	user	The last.fm username to fetch the charts list for.
	 * @return	array			An array of from/to unix timestamp pairs.
	 */
	public static function getWeeklyChartList($user, $from = null, $to = null){
		$xml = Caller::getInstance()->call('user.getWeeklyChartList', array(
			'user' => $user
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
	
	/** Get a track chart for a user profile, for a given date range. If no date range is supplied, it will return the most recent track chart for this user.
	 * 
	 * @param	string	user	The last.fm username to fetch the charts of.
 	 * @param	string	from	The date at which the chart should start from. See {@link de.felixbruns.lastfm.User#getWeeklyChartList User::getWeeklyChartList} for more.
	 * @param	string	to		The date at which the chart should end on. See {@link de.felixbruns.lastfm.User#getWeeklyChartList User::getWeeklyChartList} for more.
	 * @return	array			An array of Track objects.
	 */
	public static function getWeeklyTrackChart($user, $from = null, $to = null){
		$xml = Caller::getInstance()->call('user.getWeeklyTrackChart', array(
			'user' => $user,
			'from' => $from,
			'to'   => $to
		));
		
		$tracks = array();
		
		foreach($xml->children() as $track){
			$tracks[] = Track::fromSimpleXMLElement($track);
		}
		
		return $tracks;
	}
	
	/** Create a User object from a SimpleXMLElement.
	 * 
	 * @param	SimpleXMLElement	xml	A SimpleXMLElement.
	 * @return	User					A User object.
	 */
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
