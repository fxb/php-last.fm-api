<?

class Library {
	public static function getAlbums($user, $limit, $page, $apiKey){
		$xml = Caller::getInstance()->call('library.getAlbums', array(
			'user'    => $user,
			'limit'   => $limit,
			'page'    => $page,
			'api_key' => $apiKey
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
	
	public static function getArtists($user, $limit, $page, $apiKey){
		$xml = Caller::getInstance()->call('library.getArtists', array(
			'user'    => $user,
			'limit'   => $limit,
			'page'    => $page,
			'api_key' => $apiKey
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
	
	public static function getTracks($user, $limit, $page, $apiKey){
		$xml = Caller::getInstance()->call('library.getTracks', array(
			'user'    => $user,
			'limit'   => $limit,
			'page'    => $page,
			'api_key' => $apiKey
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
