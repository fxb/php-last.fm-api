<?

class Geo {
	/** Get all events in a specific location by country or city name.
	 * 
	 * @param	string	location	Specifies a location to retrieve events for (service returns nearby events by default).
	 * @param	float	lat			Specifies a latitude value to retrieve events for (service returns nearby events by default).
	 * @param	float	long		Specifies a longitude value to retrieve events for (service returns nearby events by default).
	 * @param	integer	distance	Find events within a specified distance.
	 * @param	integer	page		Display more results by pagination.
	 * @return	array				An array of Artist objects.
	 */
	public static function getEvents($location = '', $lat = null, $long = null,
									 $distance = null, $page = null){
		$xml = Caller::getInstance()->call('geo.getEvents', array(
			'location' => $location,
			'lat'      => $lat,
			'long'     => $long,
			'distance' => $distance,
			'page'     => $page
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
	
	/** Get top artists by country.
	 * 
	 * @param	string	country		A country name, as defined by the ISO 3166-1 country names standard.
	 * @return	array				An array of Artist objects.
	 */
	public static function getTopArtists($country){
		$xml = Caller::getInstance()->call('geo.getTopArtists', array(
			'country' => $country
		));
		
		$artists = array();
		
		foreach($xml->children() as $artist){
			$artists[] = Artist::fromSimpleXMLElement($artist);
		}
		
		return $artists;
	}
	
	/** Get top tracks by country.
	 * 
	 * @param	string	country		A country name, as defined by the ISO 3166-1 country names standard.
	 * @param	string	location	A metro name, to fetch the charts for (must be within the country specified).
	 * @return	array				An array of Track objects.
	 */
	public static function getTopTracks($country, $location){
		$xml = Caller::getInstance()->call('geo.getTopTracks', array(
			'country'  => $country,
			'location' => $location
		));
		
		$tracks = array();
		
		foreach($xml->children() as $track){
			$tracks[] = Track::fromSimpleXMLElement($track);
		}
		
		return $tracks;
	}
}

?>
