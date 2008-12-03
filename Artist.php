<?

/** Represents an artist and provides different methods to query artist information.
 *
 * @package	de.felixbruns.lastfm.api
 * @author  Felix Bruns <felixbruns@web.de>
 * @version	1.0
 */
class Artist extends Media {
	/** Indicates if this artist is streamable.
	 *
	 * @var boolean
	 */
	private $streamable;
	
	/** Similar artists.
	 *
	 * @var array
	 */
	private $similar;
	
	/** Artist tags.
	 *
	 * @var array
	 */
	private $tags;
	
	/** The artists biography.
	 *
	 * @var array
	 */
	private $biography;
	
	/** Stores a similarity value.
	 *
	 * @var float
	 */
	private $match;
	
	/** Create an Artist object.
	 *
	 * @param string	name		Name of this artist.
	 * @param string	mbid		MusicBrainz ID of this artist.
	 * @param string	url			Last.fm URL of this artist.
	 * @param array		images		An array of cover art images of different sizes.
	 * @param boolean	streamable	Is this artist streamable?
	 * @param integer	listeners	Number of listeners of this artist.
	 * @param integer	playCount	Play count of this artist.
	 * @param array		tags		An array of tags of this artist.
	 * @param array		similar		An array of similar artists.
	 * @param string	biography	Biography of this artist.
	 * @param float		match		Similarity value.
	 */
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
	
	/** Returns if this artists is streamable.
	 * 
	 * @return	boolean	true if this artist is streamable, otherwise false.
	 */
	public function isStreamable(){
		return $this->streamable;
	}
	
	/** Returns similar artists.
	 * 
	 * @return	array	An array of similar artists.
	 */
	public function _getSimilar(){
		return $this->similar;
	}
	
	/** Returns artist tags.
	 * 
	 * @return	array	An array of tags.
	 */
	public function _getTags(){
		return $this->tags;
	}
	
	/** Returns the artists biography.
	 * 
	 * @return	string	A biography text.
	 */
	public function getBiography(){
		return $this->biography;
	}
	
	/** Returns similarity value.
	 * 
	 * @return	float	A floating-point value from 0.0 to 1.0.
	 */
	public function getMatch(){
		return $this->match;
	}
	
	/** Add tags to this artist.
	 * 
	 * @param	string	artist		Artist name.
	 * @param	array	tags		Comma separated list of tags.
	 * @param	Session	session		A session obtained by {@link de.felixbruns.lastfm.Auth#getSession Auth::getSession} or {@link de.felixbruns.lastfm.Auth#getMobileSession Auth::getMobileSession}.
	 * @return	boolean				true on success or false on failure.
	 */
	public static function addTags($artist, array $tags, Session $session){
		$response = Caller::getInstance()->signedCall('artist.addTags', array(
			'artist' => $artist,
			'tags'   => implode(',', $tags)
		), $session, 'POST');
		
		return $response;
	}
	
	/** Get events of this artist.
	 * 
	 * @param	string	artist		Artist name.
	 * @return	array				An array of Event objects.
	 */
	public static function getEvents($artist){
		$xml = Caller::getInstance()->call('artist.getEvents', array(
			'artist' => $artist
		));
		
		$events = array();
		
		foreach($xml->children() as $event){
			$events[] = Event::fromSimpleXMLElement($event);
		}
		
		return $events;
	}
	
	/** Get artist info.
	 * 
	 * @param	string	artist		Artist name.
	 * @param	string	mbid		MusicBrainz ID.
	 * @param	string	lang		Preferred language (ISO 639 alpha-2 code). Default: 'en'.
	 * @return	mixed				An Artist object on success or false on failure.
	 */
	public static function getInfo($artist, $mbid = '', $lang = 'en'){
		$xml = Caller::getInstance()->call('artist.getInfo', array(
			'artist' => $artist,
			'mbid'   => $mbid,
			'lang'   => $lang
		));
		
		if($xml !== false){
			return Artist::fromSimpleXMLElement($xml);
		}
		else{
			return false;
		}
	}
	
	/** Get similar artists.
	 * 
	 * @param	string	artist		Artist name.
	 * @param	string	limit		Limit of artists to return.
	 * @return	array				An of Artist objects.
	 */
	public static function getSimilar($artist, $limit){
		$xml = Caller::getInstance()->call('artist.getSimilar', array(
			'artist' => $artist,
			'limit'  => $limit
		));
		
		$artists = array();
		
		foreach($xml->children() as $artist){
			$artists[] = Artist::fromSimpleXMLElement($artist);
		}
		
		return $artists;
	}
	
	/** Get artist tags.
	 * 
	 * @param	string	artist		Artist name.
	 * @param	Session	session		A session obtained by {@link de.felixbruns.lastfm.Auth#getSession Auth::getSession} or {@link de.felixbruns.lastfm.Auth#getMobileSession Auth::getMobileSession}.
	 * @return	array				An array of tags.
	 */
	public static function getTags($artist, Session $session){
		$xml = Caller::getInstance()->signedCall('artist.getTags', array(
			'artist' => $artist
		), $session);
		
		$tags = array();
		
		foreach($xml->children() as $tag){
			$tags[] = Tag::fromSimpleXMLElement($tag);
		}
		
		return $tags;
	}
	
	/** Get top albums of an artist.
	 * 
	 * @param	string	artist		Artist name.
	 * @return	array				An array of Album objects.
	 */
	public static function getTopAlbums($artist){
		$xml = Caller::getInstance()->call('artist.getTopAlbums', array(
			'artist' => $artist
		));
		
		$albums = array();
		
		foreach($xml->children() as $album){
			$albums[] = Album::fromSimpleXMLElement($album);
		}
		
		return $albums;
	}
	
	/** Get top fans of an artist.
	 * 
	 * @param	string	artist		Artist name.
	 * @return	array				An array of User objects.
	 */
	public static function getTopFans($artist){
		$xml = Caller::getInstance()->call('artist.getTopFans', array(
			'artist' => $artist
		));
		
		$fans = array();
		
		foreach($xml->children() as $fan){
			$fans[] = User::fromSimpleXMLElement($fan);
		}
		
		return $fans;
	}
	
	/** Get artist top tags.
	 * 
	 * @param	string	artist		Artist name.
	 * @return	array				An array of tags.
	 */
	public static function getTopTags($artist){
		$xml = Caller::getInstance()->call('artist.getTopTags', array(
			'artist' => $artist
		));
		
		$tags = array();
		
		foreach($xml->children() as $tag){
			$tags[] = Tag::fromSimpleXMLElement($tag);
		}
		
		return $tags;
	}
	
	/** Get top tracks of an artist.
	 * 
	 * @param	string	artist		Artist name.
	 * @return	array				An array of Track objects.
	 */
	public static function getTopTracks($artist){
		$xml = Caller::getInstance()->call('artist.getTopTracks', array(
			'artist' => $artist
		));
		
		$tracks = array();
		
		foreach($xml->children() as $track){
			$tracks[] = Track::fromSimpleXMLElement($track);
		}
		
		return $tracks;
	}
	
	/** Remove artist tag.
	 * 
	 * @param	string	artist		Artist name.
	 * @param	string	tag			Tag to remove.
	 * @param	Session	session		A session obtained by {@link de.felixbruns.lastfm.Auth#getSession Auth::getSession} or {@link de.felixbruns.lastfm.Auth#getMobileSession Auth::getMobileSession}.
	 * @return	boolean				true on success or false on failure.
	 */
	public static function removeTag($artist, $tag, Session $session){
		$response = Caller::getInstance()->signedCall('artist.removeTag', array(
			'artist' => $artist,
			'tag'    => $tag
		), $session, 'POST');
		
		return $response;
	}
	
	/** Search for an artist.
	 * 
	 * @param	string	artist		Artist name.
	 * @param	integer	limit		Limit number of results (default maximum: 30).
	 * @param	integer	page		Result page number (defaults to first page).
	 * @return	PaginatedResult		A PaginatedResult object.
	 */
	public static function search($artist, $limit, $page){
		$xml = Caller::getInstance()->call('artist.search', array(
			'artist' => $artist,
			'limit'  => $limit,
			'page'   => $page
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
	
	/** Share an artist.
	 * 
	 * @param	string	artist		Artist name.
	 * @param	array	recipients	An array of last.fm usernames or e-mail adresses (maximum: 10).
	 * @param	string	message		An optional message to send.
	 * @param	Session	session		A session obtained by {@link de.felixbruns.lastfm.Auth#getSession Auth::getSession} or {@link de.felixbruns.lastfm.Auth#getMobileSession Auth::getMobileSession}.
	 * @return	boolean				true on success or false on failure.
	 */
	public static function share($artist, array $recipients, $message = '', Session $session){
		$response = Caller::getInstance()->signedCall('artist.share', array(
			'artist'    => $artist,
			'recipient' => implode(',', $recipients),
			'message'   => $message
		), $session, 'POST');
		
		return $response;
	}
	
	/** Get artist playlist. INOFFICIAL.
	 * 
	 * @param	string	artist		Artist name.
	 * @return	mixed				A Playlist object on success or false on failure.
	 */
	public static function getPlaylist($artist){
		$xml = Caller::getInstance()->call('artist.getPlayerMenu', array(
			'artist' => $artist
		));
		
		return Playlist::fetch(Util::toString($xml->playlist->url), true, true);
	}
	
	/** Create an Artist object from a SimpleXMLElement.
	 * 
	 * @param	SimpleXMLElement	xml	A SimpleXMLElement.
	 * @return	Artist					An Artist object.
	 */
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
			Util::toBoolean($xml->streamable),
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
