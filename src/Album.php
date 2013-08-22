<?php

/** Represents an album and provides different methods to query album information.
 *
 * @package	php-lastfm-api
 * @author  Felix Bruns <felixbruns@web.de>
 * @version	1.0
 */
class Album extends Media {
	/** Artist of this album.
	 *
	 * @var		mixed
	 * @access	private
	 */
	private $artist;

	/** Album ID.
	 *
	 * @var		integer
	 * @access	private
	 */
	private $id;

	/** Album release date.
	 *
	 * @var		integer
	 * @access	private
	 */
	private $releaseDate;

	/** Album top tags.
	 *
	 * @var		array
	 * @access	private
	 */
	private $topTags;

	/** Create an album object.
	 *
	 * @param mixed		$artist			An artist object or string.
	 * @param string	$name			Name of this album.
	 * @param integer	$id				ID of this album.
	 * @param string	$mbid			MusicBrainz ID of this album.
	 * @param string	$url			Last.fm URL of this album.
	 * @param array		$images			An array of cover art images of different sizes.
	 * @param integer	$listeners		Number of listeners of this album.
	 * @param integer	$playCount		Play count of this album.
	 * @param integer	$releaseDate	Release date of this album.
	 * @param array		$topTags		An array of top tags of this album.
	 *
	 * @access	public
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
	 * @access	public
	 * @see		Artist
	 */
	public function getArtist(){
		return $this->artist;
	}

	/** Returns the ID of this album.
	 *
	 * @return	integer	The ID of this album.
	 * @access	public
	 */
	public function getId(){
		return $this->id;
	}

	/** Returns the release date of this album.
	 *
	 * @return	integer	Release date of this album.
	 * @access	public
	 */
	public function getReleaseDate(){
		return $this->releaseDate;
	}

	/** Returns the top tags of this album.
	 *
	 * @return	array	An array of {@link de.felixbruns.lastfm.Tag Tag} objects.
	 * @access	public
	 * @see		Tag
	 */
	public function getTopTags(){
		return $this->topTags;
	}

	/** Tag an album using a list of user supplied tags.
	 *
	 * @param	string	$artist		The artist name in question. (Required)
	 * @param	string	$album		The album name in question. (Required)
	 * @param	array	$tags		An array of user supplied tags to apply to this album. Accepts a maximum of 10 tags. (Required)
	 * @param	Session	$session	A session obtained by {@link de.felixbruns.lastfm.Auth#getSession Auth::getSession} or {@link de.felixbruns.lastfm.Auth#getMobileSession Auth::getMobileSession}. (Required)
	 *
	 * @static
	 * @access	public
	 * @throws	Error
	 */
	public static function addTags($artist, $album, array $tags,
								   Session $session){
		CallerFactory::getDefaultCaller()->signedCall('album.addTags', array(
			'artist' => $artist,
			'album'  => $album,
			'tags'   => implode(',', $tags)
		), $session, 'POST');
	}

	/** Get the metadata for an album on last.fm using the album name or a MusicBrainz ID. See playlist.fetch on how to get the album playlist.
	 *
	 * @param	string	$artist	The artist name in question. (Optional)
	 * @param	string	$album	The album name in question. (Optional)
	 * @param	string	$mbid	The MusicBrainz ID for the album. (Optional)
	 * @param	string	$lang	The language to return the biography in, expressed as an ISO 639 alpha-2 code. (Optional)
	 * @return	Album			An Album object.
	 *
	 * @static
	 * @access	public
	 * @throws	Error
	 */
	public static function getInfo($artist, $album, $mbid = null, $lang = null){
		$xml = CallerFactory::getDefaultCaller()->call('album.getInfo', array(
			'artist' => $artist,
			'album'  => $album,
			'mbid'   => $mbid,
			'lang'   => $lang
		));

		return Album::fromSimpleXMLElement($xml);
	}

	/** Get the tags applied by an individual user to an album on last.fm.
	 *
	 * @param	string	$artist		The artist name in question. (Required)
	 * @param	string	$album		The album name in question. (Required)
	 * @param	Session	$session	A session obtained by {@link de.felixbruns.lastfm.Auth#getSession Auth::getSession} or {@link de.felixbruns.lastfm.Auth#getMobileSession Auth::getMobileSession}. (Required)
	 * @return	array				An array of Tag objects.
	 * @see		Tag
	 *
	 * @static
	 * @access	public
	 * @throws	Error
	 */
	public static function getTags($artist, $album, Session $session){
		$xml = CallerFactory::getDefaultCaller()->signedCall('album.getTags', array(
			'artist' => $artist,
			'album'  => $album
		), $session);

		$tags = array();

		foreach($xml->children() as $tag){
			$tags[] = Tag::fromSimpleXMLElement($tag);
		}

		return $tags;
	}

	/** Remove a user's tag from an album.
	 *
	 * @param	string	$artist		The artist name in question. (Required)
	 * @param	string	$album		The album name in question. (Required)
	 * @param	string	$tag		A single user tag to remove from this album. (Required)
	 * @param	Session	$session	A session obtained by {@link de.felixbruns.lastfm.Auth#getSession Auth::getSession} or {@link de.felixbruns.lastfm.Auth#getMobileSession Auth::getMobileSession}. (Required)
	 *
	 * @static
	 * @access	public
	 * @throws	Error
	 */
	public static function removeTag($artist, $album, $tag, Session $session){
		CallerFactory::getDefaultCaller()->signedCall('album.removeTag', array(
			'artist' => $artist,
			'album'  => $album,
			'tag'    => $tag
		), $session, 'POST');
	}

	/** Search for an album by name. Returns album matches sorted by relevance.
	 *
	 * @param	string	$album	The album name in question. (Required)
	 * @param	integer	$limit	Limit the number of albums returned at one time. Default (maximum) is 30. (Optional)
	 * @param	integer	$page	Scan into the results by specifying a page number. Defaults to first page. (Optional)
	 * @return	PaginatedResult	A PaginatedResult object.
	 * @see		PaginatedResult
	 *
	 * @static
	 * @access	public
	 * @throws	Error
	 */
	public static function search($album, $limit = null, $page = null){
		$xml = CallerFactory::getDefaultCaller()->call('album.search', array(
			'album' => $album,
			'limit' => $limit,
			'page'  => $page
		));

		$albums = array();

		foreach($xml->albummatches->children() as $album){
			$artists[] = Album::fromSimpleXMLElement($album);
		}

		$opensearch = $xml->children('http://a9.com/-/spec/opensearch/1.1/');

		return new PaginatedResult(
			Util::toInteger($opensearch->totalResults),
			Util::toInteger($opensearch->startIndex),
			Util::toInteger($opensearch->itemsPerPage),
			$albums
		);
	}

	/** Get an album playlist for streaming. INOFFICIAL.
	 *
	 * @param	string	$artist	The artist name in question. (Required)
	 * @param	string	$album	The album name in question. (Required)
	 * @return	Playlist		A Playlist object.
	 * @see		Playlist
	 *
	 * @static
	 * @access	public
	 * @throws	Error
	 */
	public static function getPlaylist($artist, $album){
		$xml = CallerFactory::getDefaultCaller()->call('album.getPlayerMenu', array(
			'artist' => $artist,
			'album'  => $album
		));

		return Playlist::fetch(Util::toString($xml->playlist->url), true, true);
	}

	/** Create an Album object from a SimpleXMLElement object.
	 *
	 * @param	SimpleXMLElement	$xml	A SimpleXMLElement object.
	 * @return	Album						An Album object.
	 *
	 * @static
	 * @access	public
	 * @internal
	 */
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
				array(), 0, 0, 0, array(), array(), '', 0.0
			);
		}
		else if($xml->artist && $xml->artist['mbid']){
			$artist = new Artist(
				Util::toString($xml->artist),
				Util::toString($xml->artist['mbid']),
				'', array(), 0, 0, 0, array(), array(), '', 0.0
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


