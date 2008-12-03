<?

class Group {
	public static function getWeeklyAlbumChart($group, $from, $to, $apiKey){
		$xml = Caller::getInstance()->call('group.getWeeklyAlbumChart', array(
			'group'   => $group,
			'from'    => $from,
			'to'      => $to,
			'api_key' => $apiKey
		));
		
		$albums = array();
		
		foreach($xml->children() as $album){
			$albums[] = Album::fromSimpleXMLElement($album);
		}
		
		return $albums;
	}
	
	public static function getWeeklyArtistChart($group, $from, $to, $apiKey){
		$xml = Caller::getInstance()->call('group.getWeeklyArtistChart', array(
			'group'   => $group,
			'from'    => $from,
			'to'      => $to,
			'api_key' => $apiKey
		));
		
		$artists = array();
		
		foreach($xml->children() as $artist){
			$artists[] = Artist::fromSimpleXMLElement($artist);
		}
		
		return $artists;
	}
	
	public static function getWeeklyChartList($group, $from, $to, $apiKey){
		$xml = Caller::getInstance()->call('group.getWeeklyChartList', array(
			'group'   => $group,
			'api_key' => $apiKey
		));
		
		$chartList = array();
		
		foreach($xml->children() as $chart){
			$chartList[] = array(
				'from' => Util::toInteger($chart['from']),
				'to'   => Util::toInteger($chart['to']),
			);
		}
		
		return $chartList;
	}
	
	public static function getWeeklyTrackChart($group, $from, $to, $apiKey){
		$xml = Caller::getInstance()->call('group.getWeeklyTrackChart', array(
			'group'   => $group,
			'from'    => $from,
			'to'      => $to,
			'api_key' => $apiKey
		));
		
		$tracks = array();
		
		foreach($xml->children() as $track){
			$tracks[] = Track::fromSimpleXMLElement($track);
		}
		
		return $tracks;
	}
}

?>
