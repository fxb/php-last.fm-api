<?php

/** Represents an artist and provides different methods to query artist information.
 *
 * @package	php-lastfm-api
 * @author  Felix Bruns <felixbruns@web.de>
 * @version	1.0
 */
class Artist extends Media {
	/** Artist is streamable.
	 *
	 * @var		boolean
	 * @access	private
	 */
	private $streamable;

	/** Similar artists.
	 *
	 * @var		array
	 * @access	private
	 */
	private $similar;

	/** Artist tags.
	 *
	 * @var		array
	 * @access	private
	 */
	private $tags;

	/** The artists biography.
	 *
	 * @var		array
	 * @access	private
	 */
	private $biography;

	/** Stores a similarity value.
	 *
	 * @var		float
	 * @access	private
	 */
	private $match;

	/** Create an Artist object.
	 *
	 * @param string	$name		Name of this artist.
	 * @param string	$mbid		MusicBrainz ID of this artist.
	 * @param string	$url		Last.fm URL of this artist.
	 * @param array		$images		An array of cover art images of different sizes.
	 * @param boolean	$streamable	Is this artist streamable?
	 * @param integer	$listeners	Number of listeners of this artist.
	 * @param integer	$playCount	Play count of this artist.
	 * @param array		$tags		An array of tags of this artist.
	 * @param array		$similar	An array of similar artists.
	 * @param string	$biography	Biography of this artist.
	 * @param float		$match		Similarity value.
	 *
	 * @access	public
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
	 * @access	public
	 */
	public function isStreamable(){
		return $this->streamable;
	}

	/** Returns similar artists.
	 *
	 * @return	array	An array of similar artists.
	 * @access	public
	 * @see		getSimilar
	 */
	public function getSimilarArtists(){
		return $this->similar;
	}

	/** Returns artist tags.
	 *
	 * @return	array	An array of Tag objects.
	 * @access	public
	 * @see		Tag
	 */
	public function getArtistTags(){
		return $this->tags;
	}

	/** Returns the artists biography.
	 *
	 * @return	string	A biography text.
	 * @access	public
	 */
	public function getBiography(){
		return $this->biography;
	}

	/** Returns similarity value.
	 *
	 * @return	float	A floating-point value from 0.0 to 1.0.
	 * @access	public
	 */
	public function getMatch(){
		return $this->match;
	}

	/** Tag an artist with one or more user supplied tags.
	 *
	 * @param	string	$artist		The artist name in question. (Required)
	 * @param	array	$tags		An array of user supplied tags to apply to this artist. Accepts a maximum of 10 tags. (Required)
	 * @param	Session	$session	A session obtained by {@link de.felixbruns.lastfm.Auth#getSession Auth::getSession} or {@link de.felixbruns.lastfm.Auth#getMobileSession Auth::getMobileSession}. (Required)
	 *
	 * @static
	 * @access	public
	 * @throws	Error
	 */
	public static function addTags($artist, array $tags, Session $session){
		CallerFactory::getDefaultCaller()->signedCall('artist.addTags', array(
			'artist' => $artist,
			'tags'   => implode(',', $tags)
		), $session, 'POST');
	}

	/** Get a list of upcoming events for this artist. Easily integratable into calendars, using the ical standard (see feeds section below).
	 *
	 * @param	string	$artist	The artist name in question. (Required)
	 * @return	array			An array of Event objects.
	 * @see		Event
	 *
	 * @static
	 * @access	public
	 * @throws	Error
	 */
	public static function getEvents($artist){
		$xml = CallerFactory::getDefaultCaller()->call('artist.getEvents', array(
			'artist' => $artist
		));

		$events = array();

		foreach($xml->children() as $event){
			$events[] = Event::fromSimpleXMLElement($event);
		}

		return $events;
	}

	/** Get the metadata for an artist on last.fm. Includes biography.
	 *
	 * @param	string	$artist	The artist name in question. (Optional)
	 * @param	string	$mbid	The MusicBrainz ID for the artist. (Optional)
	 * @param	string	$lang	The language to return the biography in, expressed as an ISO 639 alpha-2 code. (Optional)
	 * @return	Artist			An Artist object.
	 *
	 * @static
	 * @access	public
	 * @throws	Error
	 */
	public static function getInfo($artist, $mbid = null, $lang = null){
		$xml = CallerFactory::getDefaultCaller()->call('artist.getInfo', array(
			'artist' => $artist,
			'mbid'   => $mbid,
			'lang'   => $lang
		));

		return Artist::fromSimpleXMLElement($xml);
	}

	/** Get shouts for this artist.
	 *
	 * @param	string	$artist	The artist name in question. (Required)
	 * @return	array			An array of Shout objects.
	 *
	 * @static
	 * @access	public
	 * @throws	Error
	 */
	public static function getShouts($artist){
		$xml = CallerFactory::getDefaultCaller()->call('artist.getShouts', array(
			'artist' => $artist
		));

		$shouts = array();

		foreach($xml->children() as $shout){
			$shouts[] = Shout::fromSimpleXMLElement($shout);
		}

		return $shouts;
	}

	/** Get all the artists similar to this artist.
	 *
	 * @param	string	$artist	The artist name in question. (Required)
	 * @param	string	$limit	Limit the number of similar artists returned. (Optional)
	 * @return	array			An array of Artist objects.
	 * @see		getSimilarArtists
	 *
	 * @static
	 * @access	public
	 * @throws	Error
	 */
	public static function getSimilar($artist, $limit = null){
		$xml = CallerFactory::getDefaultCaller()->call('artist.getSimilar', array(
			'artist' => $artist,
			'limit'  => $limit
		));

		$artists = array();

		foreach($xml->children() as $artist){
			$artists[] = Artist::fromSimpleXMLElement($artist);
		}

		return $artists;
	}

	/** Get the tags applied by an individual user to an artist on last.fm.
	 *
	 * @param	string	$artist		The artist name in question. (Required)
	 * @param	Session	$session	A session obtained by {@link de.felixbruns.lastfm.Auth#getSession Auth::getSession} or {@link de.felixbruns.lastfm.Auth#getMobileSession Auth::getMobileSession}. (Required)
	 * @return	array				An array of tags.
	 * @see		Tag
	 *
	 * @static
	 * @access	public
	 * @throws	Error
	 */
	public static function getTags($artist, Session $session){
		$xml = CallerFactory::getDefaultCaller()->signedCall('artist.getTags', array(
			'artist' => $artist
		), $session);

		$tags = array();

		foreach($xml->children() as $tag){
			$tags[] = Tag::fromSimpleXMLElement($tag);
		}

		return $tags;
	}

	/** Get the top albums for an artist on last.fm, ordered by popularity.
	 *
	 * @param	string	$artist	The artist name in question. (Required)
	 * @return	array			An array of Album objects.
	 * @see		Album
	 *
	 * @static
	 * @access	public
	 * @throws	Error
	 */
	public static function getTopAlbums($artist){
		$xml = CallerFactory::getDefaultCaller()->call('artist.getTopAlbums', array(
			'artist' => $artist
		));

		$albums = array();

		foreach($xml->children() as $album){
			$albums[] = Album::fromSimpleXMLElement($album);
		}

		return $albums;
	}

	/** Get the top fans for an artist on last.fm, based on listening data.
	 *
	 * @param	string	$artist	The artist name in question. (Required)
	 * @return	array			An array of User objects.
	 * @see		User
	 *
	 * @static
	 * @access	public
	 * @throws	Error
	 */
	public static function getTopFans($artist){
		$xml = CallerFactory::getDefaultCaller()->call('artist.getTopFans', array(
			'artist' => $artist
		));

		$fans = array();

		foreach($xml->children() as $fan){
			$fans[] = User::fromSimpleXMLElement($fan);
		}

		return $fans;
	}

	/** Get the top tags for an artist on last.fm, ordered by popularity.
	 *
	 * @param	string	$artist	The artist name in question. (Required)
	 * @return	array			An array of Tag objects.
	 * @see		Tag
	 *
	 * @static
	 * @access	public
	 * @throws	Error
	 */
	public static function getTopTags($artist){
		$xml = CallerFactory::getDefaultCaller()->call('artist.getTopTags', array(
			'artist' => $artist
		));

		$tags = array();

		foreach($xml->children() as $tag){
			$tags[] = Tag::fromSimpleXMLElement($tag);
		}

		return $tags;
	}

	/** Get the top tracks by an artist on last.fm, ordered by popularity.
	 *
	 * @param	string	$artist	The artist name in question. (Required)
	 * @return	array			An array of Track objects.
	 * @see		Track
	 *
	 * @static
	 * @access	public
	 * @throws	Error
	 */
	public static function getTopTracks($artist){
		$xml = CallerFactory::getDefaultCaller()->call('artist.getTopTracks', array(
			'artist' => $artist
		));

		$tracks = array();

		foreach($xml->children() as $track){
			$tracks[] = Track::fromSimpleXMLElement($track);
		}

		return $tracks;
	}

	/** Remove a user's tag from an artist.
	 *
	 * @param	string	$artist		The artist name in question. (Required)
	 * @param	string	$tag		A single user tag to remove from this artist. (Required)
	 * @param	Session	$session	A session obtained by {@link de.felixbruns.lastfm.Auth#getSession Auth::getSession} or {@link de.felixbruns.lastfm.Auth#getMobileSession Auth::getMobileSession}. (Required)
	 *
	 * @static
	 * @access	public
	 * @throws	Error
	 */
	public static function removeTag($artist, $tag, Session $session){
		CallerFactory::getDefaultCaller()->signedCall('artist.removeTag', array(
			'artist' => $artist,
			'tag'    => $tag
		), $session, 'POST');
	}

	/** Search for an artist by name. Returns artist matches sorted by relevance.
	 *
	 * @param	string	$artist	The artist name in question. (Required)
	 * @param	integer	$limit	Limit the number of artists returned at one time. Default (maximum) is 30. (Optional)
	 * @param	integer	$page	Scan into the results by specifying a page number. Defaults to first page. (Optional)
	 * @return	PaginatedResult	A PaginatedResult object.
	 * @see		PaginatedResult
	 *
	 * @static
	 * @access	public
	 * @throws	Error
	 */
	public static function search($artist, $limit = null, $page = null){
		$xml = CallerFactory::getDefaultCaller()->call('artist.search', array(
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

	/** Share an artist with last.fm users or other friends.
	 *
	 * @param	string	$artist		The artist to share. (Required)
	 * @param	array	$recipients	An array email addresses or last.fm usernames. Maximum is 10. (Required)
	 * @param	string	$message	An optional message to send with the recommendation. If not supplied a default message will be used. (Optional)
	 * @param	Session	$session	A session obtained by {@link de.felixbruns.lastfm.Auth#getSession Auth::getSession} or {@link de.felixbruns.lastfm.Auth#getMobileSession Auth::getMobileSession}. (Required)
	 *
	 * @static
	 * @access	public
	 * @throws	Error
	 */
	public static function share($artist, array $recipients, $message = null, Session $session){
		CallerFactory::getDefaultCaller()->signedCall('artist.share', array(
			'artist'    => $artist,
			'recipient' => implode(',', $recipients),
			'message'   => $message
		), $session, 'POST');
	}

	/** Get an artist playlist for streaming. INOFFICIAL.
	 *
	 * @param	string	$artist	Artist name.
	 * @return	Playlist		A Playlist object.
	 * @see		Playlist
	 *
	 * @static
	 * @access	public
	 * @throws	Error
	 */
	public static function getPlaylist($artist){
		$xml = CallerFactory::getDefaultCaller()->call('artist.getPlayerMenu', array(
			'artist' => $artist
		));

		return Playlist::fetch(Util::toString($xml->playlist->url), true, true);
	}

	/** Create an Artist object from a SimpleXMLElement object.
	 *
	 * @param	SimpleXMLElement	$xml	A SimpleXMLElement object.
	 * @return	Artist						An Artist object.
	 *
	 * @static
	 * @access	public
	 * @internal
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
			Util::toInteger($xml->listeners),
			Util::toInteger($xml->playcount),
			$tags,
			$similar,
			($xml->bio)?Util::toString($xml->bio->summary):"", // TODO: Biography object
			Util::toFloat($xml->match)
		);
	}
}


