<?php

/** Provides different methods to query user music library information.
 *
 * @package	php-lastfm-api
 * @author  Felix Bruns <felixbruns@web.de>
 * @version	1.0
 */
class Library {
	/** Add an album to a user's last.fm library.
	 *
	 * @param	string	$artist		The artist that composed the album. (Required)
	 * @param	string	$album		The album name you wish to add. (Required)
	 * @param	Session	$session	A session obtained by {@link de.felixbruns.lastfm.Auth#getSession Auth::getSession} or {@link de.felixbruns.lastfm.Auth#getMobileSession Auth::getMobileSession}. (Required)
	 *
	 * @static
	 * @access	public
	 * @throws	Error
	 */
	public static function addAlbum($artist, $album, $session){
		CallerFactory::getDefaultCaller()->signedCall('library.addAlbum', array(
			'artist' => $artist,
			'album'  => $album
		), $session, 'POST');
	}

	/** Add an artist to a user's last.fm library.
	 *
	 * @param	string	$artist		The artist name you wish to add. (Required)
	 * @param	Session	$session	A session obtained by {@link de.felixbruns.lastfm.Auth#getSession Auth::getSession} or {@link de.felixbruns.lastfm.Auth#getMobileSession Auth::getMobileSession}. (Required)
	 *
	 * @static
	 * @access	public
	 * @throws	Error
	 */
	public static function addArtist($artist, $session){
		CallerFactory::getDefaultCaller()->signedCall('library.addArtist', array(
			'artist' => $artist
		), $session, 'POST');
	}

	/** Add a track to a user's last.fm library.
	 *
	 * @param	string	$artist		The artist that composed the track. (Required)
	 * @param	string	$track		The track name you wish to add. (Required)
	 * @param	Session	$session	A session obtained by {@link de.felixbruns.lastfm.Auth#getSession Auth::getSession} or {@link de.felixbruns.lastfm.Auth#getMobileSession Auth::getMobileSession}. (Required)
	 *
	 * @static
	 * @access	public
	 * @throws	Error
	 */
	public static function addTrack($artist, $track, $session){
		CallerFactory::getDefaultCaller()->signedCall('library.addTrack', array(
			'artist' => $artist,
			'track'  => $track
		), $session, 'POST');
	}

	/** A paginated list of all the albums in a user's library, with play counts and tag counts.
	 *
	 * @param	string	$user	The user whose library you want to fetch. (Required)
	 * @param	integer	$limit	Limit the amount of albums returned (maximum/default is 50). (Optional)
	 * @param	integer	$page	The page number you wish to scan to. (Optional)
	 * @return	PaginatedResult	A PaginatedResult object.
	 *
	 * @static
	 * @access	public
	 * @throws	Error
	 */
	public static function getAlbums($user, $limit = null, $page = null){
		$xml = CallerFactory::getDefaultCaller()->call('library.getAlbums', array(
			'user'  => $user,
			'limit' => $limit,
			'page'  => $page
		));

		$albums = array();

		foreach($xml->children() as $album){
			$albums[] = Album::fromSimpleXMLElement($album);
		}

		$perPage = Util::toInteger($xml['perPage']);

		return new PaginatedResult(
			Util::toInteger($xml['totalPages']) * $perPage,
			(Util::toInteger($xml['page']) - 1) * $perPage,
			$perPage,
			$albums
		);
	}

	/** A paginated list of all the artists in a user's library, with play counts and tag counts.
	 *
	 * @param	string	$user	The user whose library you want to fetch. (Required)
	 * @param	integer	$limit	Limit the amount of artists returned (maximum/default is 50). (Optional)
	 * @param	integer	$page	The page number you wish to scan to. (Optional)
	 * @return	PaginatedResult	A PaginatedResult object.
	 *
	 * @static
	 * @access	public
	 * @throws	Error
	 */
	public static function getArtists($user, $limit = null, $page = null){
		$xml = CallerFactory::getDefaultCaller()->call('library.getArtists', array(
			'user'  => $user,
			'limit' => $limit,
			'page'  => $page
		));

		$artists = array();

		foreach($xml->children() as $artist){
			$artists[] = Artist::fromSimpleXMLElement($artist);
		}

		$perPage = Util::toInteger($xml['perPage']);

		return new PaginatedResult(
			Util::toInteger($xml['totalPages']) * $perPage,
			(Util::toInteger($xml['page']) - 1) * $perPage,
			$perPage,
			$artists
		);
	}

	/** A paginated list of all the tracks in a user's library, with play counts and tag counts.
	 *
	 * @param	string	$user	The user whose library you want to fetch. (Required)
	 * @param	integer	$limit	Limit the amount of tracks returned (maximum/default is 50). (Optional)
	 * @param	integer	$page	The page number you wish to scan to. (Optional)
	 * @return	PaginatedResult	A PaginatedResult object.
	 *
	 * @static
	 * @access	public
	 * @throws	Error
	 */
	public static function getTracks($user, $limit, $page){
		$xml = CallerFactory::getDefaultCaller()->call('library.getTracks', array(
			'user'  => $user,
			'limit' => $limit,
			'page'  => $page
		));

		$tracks = array();

		foreach($xml->children() as $track){
			$tracks[] = Track::fromSimpleXMLElement($track);
		}

		$perPage = Util::toInteger($xml['perPage']);

		return new PaginatedResult(
			Util::toInteger($xml['totalPages']) * $perPage,
			(Util::toInteger($xml['page']) - 1) * $perPage,
			$perPage,
			$tracks
		);
	}
}


