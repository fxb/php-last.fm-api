<?

/** Represents an album and provides different methods to query album information.
 *
 * @package	de.felixbruns.lastfm.api
 * @author  Felix Bruns <felixbruns@web.de>
 * @version	1.0
 */
class Album extends Media {
	/** The artist of this album.
	 *
	 * @var mixed
	 */
	private $artist;
	
	/** The albums id.
	 *
	 * @var integer
	 */
	private $id;
	
	/** The albums release date.
	 *
	 * @var integer
	 */
	private $releaseDate;
	
	/** The albums top tags.
	 *
	 * @var array
	 */
	private $topTags;
	
	/** Create an album object.
	 *
	 * @param mixed		artist		An artist object or string.
	 * @param string	name		Name for this album.
	 * @param integer	id			ID for this album.
	 * @param string	mbid		MusicBrainz ID for this album.
	 * @param string	url			Last.fm URL for this album.
	 * @param array		images		An array of cover art images of different sizes.
	 * @param integer	listeners	Number of listeners for this album.
	 * @param integer	playCount	Play count of this album.
	 * @param integer	releaseDate	Release date of this album.
	 * @param array		topTags		An array of top tags for this album.
	 */
	public function __construct($artist, $name, $id, $mbid, $url, array $images,
								$listeners, $playCount, $releaseDate,
								array $topTags){
		parent::__construct($name, $mbid, $url, $images, $listeners, $playCount);
		
		$this->artist      = $artist;
		$this->id          = $id;
		$this->releaseDate = $releaseDate;
		$this->topTags     = $topTags;
	}
	
	/** Returns the artist of this album.
	 * 
	 * @return	mixed	An {@link de.felixbruns.lastfm.Artist Artist} object or the artists name.
	 */
	public function getArtist(){
		return $this->artist;
	}
	
	/** Returns the ID of this album.
	 * 
	 * @return	integer	The ID of this album.
	 */
	public function getId(){
		return $this->id;
	}
	
	/** Returns the release date of this album.
	 * 
	 * @return	integer	Release date of this album as a unix timestamp.
	 */
	public function getReleaseDate(){
		return $this->releasedate;
	}
	
	/** Returns the top tags of this album.
	 * 
	 * @return	array	An array of {@link de.felixbruns.lastfm.Tag Tag} objects.
	 */
	public function getTopTags(){
		return $this->topTags;
	}
	
	/** Add tags to this album.
	 * 
	 * @param	string	artist		Artist name.
	 * @param	string	album		Album name.
	 * @param	array	tags		Comma separated list of tags.
	 * @param	array	apiKey		Your API key.
	 * @param	array	apiSecret	Your API secret.
	 * @param	array	session		A session obtained by {@link de.felixbruns.lastfm.Auth#getSession Auth::getSession} or {@link de.felixbruns.lastfm.Auth#getMobileSession Auth::getMobileSession}.
	 * @return	boolean				true for success, false for failure.
	 */
	public static function addTags($artist, $album, array $tags,
								   Session $session){
		$xml = Caller::getInstance()->signedCall('album.addTags', array(
			'artist'  => $artist,
			'album'   => $album,
			'tags'    => implode(',', $tags),
			'api_key' => $session->getApiKey(),
			'sk'      => $session->getKey()
		), $session->getApiSecret(), 'POST');
		
		return $xml;
	}
	
	public static function getInfo($artist, $album, $mbid, $apiKey){
		$xml = Caller::getInstance()->call('album.getInfo', array(
			'artist'  => $artist,
			'album'   => $album,
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
	
	public static function getTags($artist, $album, Session $session){
		$xml = Caller::getInstance()->signedCall('album.getTags', array(
			'artist'  => $artist,
			'album'   => $album,
			'api_key' => $session->getApiKey(),
			'sk'      => $session->getKey()
		), $session->getApiSecret());
		
		$tags = array();
		
		foreach($xml->children() as $tag){
			$tags[] = Tag::fromSimpleXMLElement($tag);
		}
		
		return $tags;
	}
	
	public static function removeTag($artist, $album, $tag, Session $session){
		$xml = Caller::getInstance()->signedCall('album.removeTag', array(
			'artist'  => $artist,
			'album'   => $album,
			'tag'     => $tag,
			'api_key' => $session->getApiKey(),
			'sk'      => $session->getKey()
		), $session->getApiSecret(), 'POST');
		
		return $xml;
	}
	
	public static function getPlaylist($artist, $album, $apiKey){
		$xml = Caller::getInstance()->call('album.getPlayerMenu', array(
			'artist'  => $artist,
			'album'   => $album,
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
		
		/* TODO: tagcount | library.getAlbums */
		
		if($xml->mbid){
			$mbid = Util::toString($xml->mbid);
		}
		else if($xml['mbid']){
			$mbid = Util::toString($xml['mbid']);
		}
		else{
			$mbid = '';
		}
		
		foreach($xml->image as $image){
			$images[Util::toImageType($image['size'])] = Util::toString($image);
		}
		
		if($xml->toptags){
			foreach($xml->toptags->children() as $tag){
				$topTags[] = Tag::fromSimpleXMLElement($tag);
			}
		}
		
		if($xml->artist->name && $xml->artist->mbid && $xml->artist->url){
			$artist = new Artist(
				Util::toString($xml->artist->name),
				Util::toString($xml->artist->mbid),
				Util::toString($xml->artist->url),
				array(), 0, 0, 0, array(), array(), ''
			);
		}
		if($xml->artist && $xml->artist['mbid']){
			$artist = new Artist(
				Util::toString($xml->artist),
				Util::toString($xml->artist['mbid']),
				'', array(), 0, 0, 0, array(), array(), ''
			);
		}
		else{
			$artist = Util::toString($xml->artist);
		}
		
		return new Album(
			$artist,
			Util::toString($xml->name),
			Util::toInteger($xml->id),
			$mbid,
			Util::toString($xml->url),
			$images,
			Util::toInteger($xml->listeners),
			Util::toInteger($xml->playcount),
			Util::toTimestamp($xml->releasedate),
			$topTags
		);
	}
}

?>
