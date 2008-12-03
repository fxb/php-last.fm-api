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
	
	/** Add a track to a Last.fm user's playlist.
	 * 
	 * @param	string	id			The ID of the playlist - this is available in user.getPlaylists.
	 * @param	string	artist		The artist name that corresponds to the track to be added.
	 * @param	string	track		The track name to add to the playlist.
	 * @param	Session	session		A session obtained by {@link de.felixbruns.lastfm.Auth#getSession Auth::getSession} or {@link de.felixbruns.lastfm.Auth#getMobileSession Auth::getMobileSession}.
	 * @return	boolean				true on success or false on failure.
	 */
	public static function addTrack($id, $artist, $track, $session){
		$response = Caller::getInstance()->signedCall('playlist.addTrack', array(
			'playlistID' => $id,
			'artist'     => $artist,
			'track'      => $track
		), $session, 'POST');
		
		return $response;
	}
	
	/** Create a Last.fm playlist on behalf of a user.
	 * 
	 * @param	string	title		Title for the playlist.
	 * @param	string	description	Description for the playlist.
	 * @param	Session	session		A session obtained by {@link de.felixbruns.lastfm.Auth#getSession Auth::getSession} or {@link de.felixbruns.lastfm.Auth#getMobileSession Auth::getMobileSession}.
	 * @return	Playlist			A Playlist object or false on failure.
	 */
	public static function create($title = null, $description = null, $session){
		$xml = Caller::getInstance()->call('playlist.create', array(
			'title'       => $title,
			'description' => $description
		), $session , 'POST');
		
		if($xml !== false){
			return Playlist::fromSimpleXMLElement($xml);
		}
		else{
			return false;
		}
	}
	
	/** Fetch XSPF playlists using a last.fm playlist url.
	 * 
	 * @param	string	playlist	A lastfm protocol playlist url ('lastfm://playlist/...').
	 * @param	string	streaming	Weather to fetch a playlist for streaming.
	 * @param	string	fod			Weather to fetch a playlist with free on demand tracks.
	 * @return	Playlist			A Playlist object or false on failure.
	 */
	public static function fetch($playlist, $streaming = null, $fod = null){
		$xml = Caller::getInstance()->call('playlist.fetch', array(
			'playlistURL' => $playlist,
			'streaming'   => $streaming,
			'fod'         => $fod
		));
		
		if($xml !== false){
			return Playlist::fromSimpleXMLElement($xml);
		}
		else{
			return false;
		}
	}
	
	/** Fetch XSPF playlists using a last.fm playlist url and a session.
	 * 
	 * @param	string	playlist	A lastfm protocol playlist url ('lastfm://playlist/...').
	 * @param	string	streaming	Weather to fetch a playlist for streaming.
	 * @param	string	fod			Weather to fetch a playlist with free on demand tracks.
	 * @param	Session	session		A session obtained by {@link de.felixbruns.lastfm.Auth#getSession Auth::getSession} or {@link de.felixbruns.lastfm.Auth#getMobileSession Auth::getMobileSession}.
	 * @return	Playlist			A Playlist object or false on failure.
	 */
	public static function fetchWithSession($playlist, $streaming = null, $fod = null, $session){
		$xml = Caller::getInstance()->call('playlist.fetch', array(
			'playlistURL' => $playlist,
			'streaming'   => $streaming,
			'fod'         => $fod,
			'sk'          => $session->getKey()
		));
		
		if($xml !== false){
			return Playlist::fromSimpleXMLElement($xml);
		}
		else{
			return false;
		}
	}
	
	/** Create a Playlist object from a SimpleXMLElement.
	 * 
	 * @param	SimpleXMLElement	xml	A SimpleXMLElement.
	 * @return	Playlist				A Playlist object.
	 */
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
