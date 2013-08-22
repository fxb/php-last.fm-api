<?php

/** Represents a user and provides different methods to query user information.
 *
 * @package	php-lastfm-api
 * @author  Felix Bruns <felixbruns@web.de>
 * @version	1.0
 */
class User extends Media {
	/** The users real name.
	 *
	 * @var string
	 * @access	private
	 */
	private $realName;

	/** The users language.
	 *
	 * @var string
	 * @access	private
	 */
	private $language;

	/** The users country.
	 *
	 * @var string
	 * @access	private
	 */
	private $country;

	/** The users age.
	 *
	 * @var integer
	 * @access	private
	 */
	private $age;

	/** The users gender.
	 *
	 * @var string
	 * @access	private
	 */
	private $gender;

	/** Indicates if the user is a subscriber.
	 *
	 * @var boolean
	 * @access	private
	 */
	private $subscriber;

	/** The number of playlist of this user.
	 *
	 * @var integer
	 * @access	private
	 */
	private $playlists;

	/** The last track the user played.
	 *
	 * @var Track
	 * @access	private
	 */
	private $lastTrack;

	/** Similarity match.
	 *
	 * @var float
	 * @access	private
	 */
	private $match;

	/** I have no idea. Haha. TODO.
	 *
	 * @var integer
	 * @access	private
	 */
	private $weight;

	/** Possible time periods.
	 *
	 * @var integer
	 * @access	public
	 */
	const PERIOD_OVERALL  = 'overall';
	const PERIOD_3MONTHS  = '3month';
	const PERIOD_6MONTHS  = '6month';
	const PERIOD_12MONTHS = '12month';

	/** Create an Artist object.
	 *
	 * @param string	$name		Username.
	 * @param string	$url		Last.fm URL of this user.
	 * @param string	$realName	Real name of this user.
	 * @param string	$language	Language of this user.
	 * @param string	$country	Country of this user.
	 * @param integer	$age		Age of this user.
	 * @param string	$gender		Gender of this user.
	 * @param boolean	$subscriber	Subscriber status of this user.
	 * @param integer	$playCount	Track play count of this user.
	 * @param string	$playlists	Number of playlist of this user.
	 * @param array		$images		An array of cover art images of different sizes.
	 * @param Track		$lastTrack	Last track the user played.
	 * @param float		$match		Similarity value.
	 * @param integer	$weight		Still no idea.
	 * @param integer	$registered	Registration date of this user.
	 *
	 * @access	public
	 */
	public function __construct($name, $realName, $url, $language, $country, $age, $gender,
								$subscriber, $playCount, $playlists,
								array $images, $lastTrack, $match, $weight, $registered){
		parent::__construct($name, '', $url, $images, 0, $playCount);

        $this->realName   = $realName;
		$this->language   = $language;
		$this->country    = $country;
		$this->age        = $age;
		$this->gender     = $gender;
		$this->subscriber = $subscriber;
		$this->playlists  = $playlists;
		$this->lastTrack  = $lastTrack;
		$this->match      = $match;
		$this->weight     = $weight;
		$this->registered = $registered;
	}

	/** Returns the users real name.
	 *
	 * @return	string	The users real name.
	 * @access	public
	 */
	public function getRealName(){
		return $this->realName;
	}

	/** Returns the users language.
	 *
	 * @return	string	The users language.
	 * @access	public
	 */
	public function getLanguage(){
		return $this->language;
	}

	/** Returns the users country.
	 *
	 * @return	string	The users country.
	 * @access	public
	 */
	public function getCountry(){
		return $this->country;
	}

	/** Returns the users age.
	 *
	 * @return	integer	The users age.
	 * @access	public
	 */
	public function getAge(){
		return $this->age;
	}

	/** Returns the users gender.
	 *
	 * @return	string	The users gender.
	 * @access	public
	 */
	public function getGender(){
		return $this->gender;
	}

	/** Returns if the user is a subscriber.
	 *
	 * @return	boolean	The users subscription status (true or false).
	 * @access	public
	 */
	public function isSubscriber(){
		return $this->subscriber;
	}

	/** Returns the number of playlists of this user.
	 *
	 * @return	integer	Number of playlists.
	 * @access	public
	 */
	public function getPlaylistCount(){
		return $this->playlists;
	}

	/** Returns the last played track of this user.
	 *
	 * @return	Track	A Track object.
	 * @access	public
	 */
	public function getLastTrack(){
		return $this->lastTrack;
	}

	/** Returns the similarity match of this user.
	 *
	 * @return	float	A floating-point number.
	 * @access	public
	 */
	public function getMatch(){
		return $this->match;
	}

	/** Returns whatever.
	 *
	 * @return	integer	I don't even know if it's an integer right now.
	 * @access	public
	 */
	public function getWeight(){
		return $this->weight;
	}

	/** Returns the registration date of this user.
	 *
	 * @return	integer	Registration date of this user.
	 * @access	public
	 */
	public function getRegistered(){
		return $this->registered;
	}

	/** Get a list of upcoming events that this user is attending. Easily integratable into calendars, using the iCal standard.
	 *
	 * @param	string	$user	The user to fetch the events for. (Required)
	 * @return	array			An array of Event objects.
	 *
	 * @static
	 * @access	public
	 * @throws	Error
	 */
	public static function getEvents($user){
		$xml = CallerFactory::getDefaultCaller()->call('user.getEvents', array(
			'user' => $user
		));

		$events = array();

		foreach($xml->children() as $event){
			$events[] = Event::fromSimpleXMLElement($event);
		}

		return $events;
	}

	/** Get a list of the user's friends on last.fm.
	 *
	 * @param	string	$user			The last.fm username to fetch the friends of. (Required)
	 * @param	boolean	$recentTracks	Whether or not to include information about friends' recent listening in the response. (Optional)
	 * @param	integer	$limit			An integer used to limit the number of friends returned. (Optional)
	 * @return	array					An array of User objects.
	 *
	 * @static
	 * @access	public
	 * @throws	Error
	 */
	public static function getFriends($user, $recentTracks = null, $limit = null){
		$xml = CallerFactory::getDefaultCaller()->call('user.getFriends', array(
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
	 * @param	Session	$session	A session obtained by {@link de.felixbruns.lastfm.Auth#getSession Auth::getSession} or {@link de.felixbruns.lastfm.Auth#getMobileSession Auth::getMobileSession}. (Required)
	 * @return	User				A User object.
	 *
	 * @static
	 * @access	public
	 * @throws	Error
	 */
	public static function getInfo($session){
		$xml = CallerFactory::getDefaultCaller()->signedCall('user.getInfo', array(
			'user'         => $user,
			'recenttracks' => $recenttracks,
			'limit'        => $limit
		), $session);

		return User::fromSimpleXMLElement($xml);
	}

	/** Get the last 50 tracks loved by a user.
	 *
	 * @param	string	$user	The user name to fetch the loved tracks for. (Required)
	 * @return	array			An array of Track objects.
	 *
	 * @static
	 * @access	public
	 * @throws	Error
	 */
	public static function getLovedTracks($user){
		$xml = CallerFactory::getDefaultCaller()->call('user.getLovedTracks', array(
			'user' => $user
		));

		$tracks = array();

		foreach($xml->children() as $track){
			$tracks[] = Track::fromSimpleXMLElement($track);
		}

		return $tracks;
	}

	/** Get a list of a user's neighbours on last.fm.
	 *
	 * @param	string	$user	The last.fm username to fetch the neighbours of. (Required)
	 * @param	integer	$limit	An integer used to limit the number of neighbours returned. (Optional)
	 * @return	array			An array of Track objects.
	 *
	 * @static
	 * @access	public
	 * @throws	Error
	 */
	public static function getNeighbours($user, $limit = null){
		$xml = CallerFactory::getDefaultCaller()->call('user.getNeighbours', array(
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
	 * @param	string	$user	The username to fetch the events for. (Required)
	 * @param	integer	$limit	The number of events to return per page. (Optional)
	 * @param	integer	$page	The page number to scan to. (Optional)
	 * @return	PaginatedResult	A PaginatedResult object.
	 * @see		PaginatedResult
	 *
	 * @static
	 * @access	public
	 * @throws	Error
	 */
	public static function getPastEvents($user, $limit = null, $page = null){
		$xml = CallerFactory::getDefaultCaller()->call('user.getPastEvents', array(
			'user'  => $user,
			'limit' => $limit,
			'page'  => $page
		));

		$events = array();

		foreach($xml->children() as $event){
			$events[] = Event::fromSimpleXMLElement($event);
		}

		$perPage = Util::toInteger($xml['perPage']);

		return new PaginatedResult(
			Util::toInteger($xml['total']),
			(Util::toInteger($xml['page']) - 1) * $perPage,
			$perPage,
			$events
		);
	}

	/** Get a list of a user's playlists on last.fm.
	 *
	 * @param	string	$user	The last.fm username to fetch the playlists of. (Required)
	 * @return	array			An array of Playlist objects.
	 *
	 * @static
	 * @access	public
	 * @throws	Error
	 */
	public static function getPlaylists($user){
		$xml = CallerFactory::getDefaultCaller()->call('user.getPlaylists', array(
			'user' => $user
		));

		$playlists = array();

		foreach($xml->children() as $playlist){
			$playlists[] = Playlist::fromSimpleXMLElement($playlist);
		}

		return $playlists;
	}

	/** Get a list of the recent tracks listened to by this user. Indicates now playing track if the user is currently listening.
	 *
	 * @param	string	$user	The last.fm username to fetch the recent tracks of. (Required)
	 * @param	integer	$limit	An integer used to limit the number of tracks returned. (Optional)
	 * @return	array			An array of Playlist objects.
	 *
	 * @static
	 * @access	public
	 * @throws	Error
	 */
	public static function getRecentTracks($user, $limit = null){
		$xml = CallerFactory::getDefaultCaller()->call('user.getRecentTracks', array(
			'user'  => $user,
			'limit' => $limit
		));

		$tracks = array();

		foreach($xml->children() as $track){
			$tracks[] = Track::fromSimpleXMLElement($track);
		}

		return $tracks;
	}

	/** Get Last.fm artist recommendations for a user.
	 *
	 * @param	integer	$limit	The number of events to return per page. (Optional)
	 * @param	integer	$page	The page number to scan to. (Optional)
	 * @return	PaginatedResult	A PaginatedResult object.
	 *
	 * @static
	 * @access	public
	 * @throws	Error
	 */
	public static function getRecommendedArtists($limit = null, $page = null, $session){
		$xml = CallerFactory::getDefaultCaller()->signedCall('user.getRecommendedArtists', array(
			'limit' => $limit,
			'page'  => $page
		), $session);

		$artists = array();

		foreach($xml->children() as $artist){
			$artists[] = Artist::fromSimpleXMLElement($artist);
		}

		$perPage = Util::toInteger($xml['perPage']);

		return new PaginatedResult(
			Util::toInteger($xml['total']),
			(Util::toInteger($xml['page']) - 1) * $perPage,
			$perPage,
			$artists
		);
	}

	/** Get a paginated list of all events recommended to a user by last.fm, based on their listening profile.
	 *
	 * @param	integer	$limit	The number of events to return per page. (Optional)
	 * @param	integer	$page	The page number to scan to. (Optional)
	 * @return	PaginatedResult	A PaginatedResult object.
	 *
	 * @static
	 * @access	public
	 * @throws	Error
	 */
	public static function getRecommendedEvents($limit = null, $page = null, $session){
		$xml = CallerFactory::getDefaultCaller()->signedCall('user.getRecommendedEvents', array(
			'limit' => $limit,
			'page'  => $page
		), $session);

		$events = array();

		foreach($xml->children() as $event){
			$events[] = Event::fromSimpleXMLElement($event);
		}

		$perPage = Util::toInteger($xml['perPage']);

		return new PaginatedResult(
			Util::toInteger($xml['total']),
			(Util::toInteger($xml['page']) - 1) * $perPage,
			$perPage,
			$events
		);
	}

	/** Get shouts for this user.
	 *
	 * @param	string	$user	The username to fetch shouts for. (Required)
	 * @return	array			An array of Shout objects.
	 *
	 * @static
	 * @access	public
	 * @throws	Error
	 */
	public static function getShouts($user){
		$xml = CallerFactory::getDefaultCaller()->call('user.getShouts', array(
			'user' => $user
		));

		$shouts = array();

		foreach($xml->children() as $shout){
			$shouts[] = Shout::fromSimpleXMLElement($shout);
		}

		return $shouts;
	}

	/** Get the top albums listened to by a user. You can stipulate a time period. Sends the overall chart by default.
	 *
	 * @param	string	$user	The user name to fetch top albums for. (Required)
	 * @param	integer	$period	The time period over which to retrieve top albums for. (Optional)
	 * @return	array			An array of Album objects.
	 *
	 * @static
	 * @access	public
	 * @throws	Error
	 */
	public static function getTopAlbums($user, $period = null){
		$xml = CallerFactory::getDefaultCaller()->call('user.getTopAlbums', array(
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
	 * @param	string	$user	The user name to fetch top artists for. (Required)
	 * @param	integer	$period	The time period over which to retrieve top artists for. (Optional)
	 * @return	array			An array of Artist objects.
	 *
	 * @static
	 * @access	public
	 * @throws	Error
	 */
	public static function getTopArtists($user, $period = null){
		$xml = CallerFactory::getDefaultCaller()->call('user.getTopArtists', array(
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
	 * @param	string	$user	The user name to fetch top tags for. (Required)
	 * @param	integer	$limit	Limit the number of tags returned. (Optional)
	 * @return	array			An array of Tag objects.
	 *
	 * @static
	 * @access	public
	 * @throws	Error
	 */
	public static function getTopTags($user, $limit = null){
		$xml = CallerFactory::getDefaultCaller()->call('user.getTopTags', array(
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
	 * @param	string	$user	The user name to fetch top tracks for. (Required)
	 * @param	integer	$period	The time period over which to retrieve top tracks for. (Optional)
	 * @return	array			An array of Track objects.
	 *
	 * @static
	 * @access	public
	 * @throws	Error
	 */
	public static function getTopTracks($user, $period = null){
		$xml = CallerFactory::getDefaultCaller()->call('user.getTopTracks', array(
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
	 * @param	string	$user	The last.fm username to fetch the charts of. (Required)
 	 * @param	string	$from	The date at which the chart should start from. See {@link de.felixbruns.lastfm.User#getWeeklyChartList User::getWeeklyChartList} for more. (Optional)
	 * @param	string	$to		The date at which the chart should end on. See {@link de.felixbruns.lastfm.User#getWeeklyChartList User::getWeeklyChartList} for more. (Optional)
	 * @return	array			An array of Album objects.
	 *
	 * @static
	 * @access	public
	 * @throws	Error
	 */
	public static function getWeeklyAlbumChart($user, $from = null, $to = null){
		$xml = CallerFactory::getDefaultCaller()->call('user.getWeeklyAlbumChart', array(
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
	 * @param	string	$user	The last.fm username to fetch the charts of. (Required)
 	 * @param	string	$from	The date at which the chart should start from. See {@link de.felixbruns.lastfm.User#getWeeklyChartList User::getWeeklyChartList} for more. (Optional)
	 * @param	string	$to		The date at which the chart should end on. See {@link de.felixbruns.lastfm.User#getWeeklyChartList User::getWeeklyChartList} for more. (Optional)
	 * @return	array			An array of Artist objects.
	 *
	 * @static
	 * @access	public
	 * @throws	Error
	 */
	public static function getWeeklyArtistChart($user, $from = null, $to = null){
		$xml = CallerFactory::getDefaultCaller()->call('user.getWeeklyArtistChart', array(
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
	 * @param	string	$user	The last.fm username to fetch the charts list for. (Required)
	 * @return	array			An array of from/to unix timestamp pairs.
	 *
	 * @static
	 * @access	public
	 * @throws	Error
	 */
	public static function getWeeklyChartList($user, $from = null, $to = null){
		$xml = CallerFactory::getDefaultCaller()->call('user.getWeeklyChartList', array(
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
	 * @param	string	$user	The last.fm username to fetch the charts of. (Required)
 	 * @param	string	$from	The date at which the chart should start from. See {@link de.felixbruns.lastfm.User#getWeeklyChartList User::getWeeklyChartList} for more. (Optional)
	 * @param	string	$to		The date at which the chart should end on. See {@link de.felixbruns.lastfm.User#getWeeklyChartList User::getWeeklyChartList} for more. (Optional)
	 * @return	array			An array of Track objects.
	 *
	 * @static
	 * @access	public
	 * @throws	Error
	 */
	public static function getWeeklyTrackChart($user, $from = null, $to = null){
		$xml = CallerFactory::getDefaultCaller()->call('user.getWeeklyTrackChart', array(
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
	 * @param	SimpleXMLElement	$xml	A SimpleXMLElement.
	 * @return	User						A User object.
	 *
	 * @static
	 * @access	public
	 * @internal
	 */
	public static function fromSimpleXMLElement(SimpleXMLElement $xml){
		$images = array();

		foreach($xml->image as $image){
			$images[Util::toImageType($image['size'])] = Util::toString($image);
		}

		return new User(
			Util::toString($xml->name),
			Util::toString($xml->realname),
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
			Util::toInteger($xml->weight),
			Util::toInteger($xml->registered['unixtime'])
		);
	}
}


