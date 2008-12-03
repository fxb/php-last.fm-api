<?

class Geo {
	public static function getEvents($location, $distance, $page, $apiKey){
		$xml = Caller::getInstance()->call('geo.getEvents', array(
			'location' => $location,
			'distance' => $distance,
			'page'     => $page,
			'api_key'  => $apiKey
		));
		
		$events = array();
		
		foreach($xml->children() as $event){
			$events[] = Event::fromSimpleXMLElement($event);
		}
		
		$perPage = intval(ceil(
			Util::toInteger($xml['total']) / Util::toInteger($xml['totalpages'])
		));
		
		return new PaginatedResult(
			Util::toInteger($xml['total']),
			(Util::toInteger($xml['page']) - 1) * $perPage,
			$perPage,
			$events
		);
	}
	
	public static function getTopArtists($country, $apiKey){
		$xml = Caller::getInstance()->call('geo.getTopArtists', array(
			'country' => $country,
			'api_key' => $apiKey
		));
		
		$artists = array();
		
		foreach($xml->children() as $artist){
			$artists[] = Artist::fromSimpleXMLElement($artist);
		}
		
		return $artists;
	}
	
	public static function getTopTracks($country, $location, $apiKey){
		// NOTE: doesn't work
		
		$xml = Caller::getInstance()->call('geo.getTopTracks', array(
			'country'  => $country,
			'location' => $location,
			'api_key'  => $apiKey
		));
		
		$tracks = array();
		
		foreach($xml->children() as $track){
			$tracks[] = Track::fromSimpleXMLElement($track);
		}
		
		return $tracks;
	}
}

?>
