<?

/** Provides different methods to query user music library information.
 *
 * @package	de.felixbruns.lastfm.api
 * @author  Felix Bruns <felixbruns@web.de>
 * @version	1.0
 */
class Library {
	/** Add an album to a user's Last.fm library.
	 * 
	 * @param	string	artist		The artist that composed the album.
	 * @param	string	album		The album name you wish to add.
	 * @param	Session	session		A session obtained by {@link de.felixbruns.lastfm.Auth#getSession Auth::getSession} or {@link de.felixbruns.lastfm.Auth#getMobileSession Auth::getMobileSession}.
	 * @return	boolean				true on success or false on failure.
	 */
	public static function addAlbum($artist, $album, $session){
		$response = Caller::getInstance()->signedCall('library.addAlbum', array(
			'artist' => $artist,
			'album'  => $album
		), $session, 'POST');
		
		return $response;
	}
	
	/** Add an artist to a user's Last.fm library.
	 * 
	 * @param	string	artist		The artist name you wish to add.
	 * @param	Session	session		A session obtained by {@link de.felixbruns.lastfm.Auth#getSession Auth::getSession} or {@link de.felixbruns.lastfm.Auth#getMobileSession Auth::getMobileSession}.
	 * @return	boolean				true on success or false on failure.
	 */
	public static function addArtist($artist, $session){
		$response = Caller::getInstance()->signedCall('library.addArtist', array(
			'artist' => $artist
		), $session, 'POST');
		
		return $response;
	}
	
	/** Add a track to a user's Last.fm library.
	 * 
	 * @param	string	artist		The artist that composed the track.
	 * @param	string	track		The track name you wish to add.
	 * @param	Session	session		A session obtained by {@link de.felixbruns.lastfm.Auth#getSession Auth::getSession} or {@link de.felixbruns.lastfm.Auth#getMobileSession Auth::getMobileSession}.
	 * @return	boolean				true on success or false on failure.
	 */
	public static function addAlbum($artist, $track, $session){
		$response = Caller::getInstance()->signedCall('library.addTrack', array(
			'artist' => $artist,
			'track'  => $track
		), $session, 'POST');
		
		return $response;
	}
	
	/** A paginated list of all the albums in a user's library, with play counts and tag counts.
	 * 
	 * @param	string	user		The user whose library you want to fetch.
	 * @param	integer	limit		Limit the amount of albums returned (maximum/default is 50).
	 * @param	integer	page		The page number you wish to scan to.
	 * @return	PaginatedResult		A PaginatedResult object.
	 */
	public static function getAlbums($user, $limit = null, $page = null){
		$xml = Caller::getInstance()->call('library.getAlbums', array(
			'user'  => $user,
			'limit' => $limit,
			'page'  => $page
		));
		
		$albums = array();
		
		foreach($xml->children() as $album){
			$albums[] = Album::fromSimpleXMLElement($album);
		}
		
		return new PaginatedResult(
			Util::toInteger($xml['totalPages']) *
				Util::toInteger($xml['perPage']),
			(Util::toInteger($xml['page']) - 1) *
				Util::toInteger($xml['perPage']),
			Util::toInteger($xml['perPage']),
			$albums
		);
	}
	
	/** A paginated list of all the artists in a user's library, with play counts and tag counts.
	 * 
	 * @param	string	user		The user whose library you want to fetch.
	 * @param	integer	limit		Limit the amount of artists returned (maximum/default is 50).
	 * @param	integer	page		The page number you wish to scan to.
	 * @return	PaginatedResult		A PaginatedResult object.
	 */
	public static function getArtists($user, $limit = null, $page = null){
		$xml = Caller::getInstance()->call('library.getArtists', array(
			'user'  => $user,
			'limit' => $limit,
			'page'  => $page
		));
		
		$artists = array();
		
		foreach($xml->children() as $artist){
			$artists[] = Artist::fromSimpleXMLElement($artist);
		}
		
		return new PaginatedResult(
			Util::toInteger($xml['totalPages']) *
				Util::toInteger($xml['perPage']),
			(Util::toInteger($xml['page']) - 1) *
				Util::toInteger($xml['perPage']),
			Util::toInteger($xml['perPage']),
			$artists
		);
	}
	
	/** A paginated list of all the tracks in a user's library, with play counts and tag counts.
	 * 
	 * @param	string	user		The user whose library you want to fetch.
	 * @param	integer	limit		Limit the amount of tracks returned (maximum/default is 50).
	 * @param	integer	page		The page number you wish to scan to.
	 * @return	PaginatedResult		A PaginatedResult object.
	 */
	public static function getTracks($user, $limit, $page){
		$xml = Caller::getInstance()->call('library.getTracks', array(
			'user'  => $user,
			'limit' => $limit,
			'page'  => $page
		));
		
		$tracks = array();
		
		foreach($xml->children() as $track){
			$tracks[] = Track::fromSimpleXMLElement($track);
		}
		
		return new PaginatedResult(
			Util::toInteger($xml['totalPages']) *
				Util::toInteger($xml['perPage']),
			(Util::toInteger($xml['page']) - 1) *
				Util::toInteger($xml['perPage']),
			Util::toInteger($xml['perPage']),
			$tracks
		);
	}
}

?>
