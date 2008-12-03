<?

class Tasteometer {
	const COMPARE_USER    = 'user';
	const COMPARE_ARTIST  = 'artists';
	const COMPARE_MYSPACE = 'myspace';
	
	public static function compare($type1, $type2, $value1, $value2, $limit,
								   $apiKey){
		if(is_array($value1)){
			$value1 = implode(',', $value1);
		}
		
		if(is_array($value2)){
			$value2 = implode(',', $value2);
		}
		
		$xml = Caller::getInstance()->call('tasteometer.compare', array(
			'type1'   => $type1,
			'type2'   => $type2,
			'value1'  => $value1,
			'value2'  => $value2,
			'limit'   => $limit,
			'api_key' => $apiKey
		));
		
		$artists = array();
		
		foreach($xml->result->artists->children() as $artist){
			$artists[] = Artist::fromSimpleXMLElement($artist);
		}
		
		/* TODO: Return a Comparison object, including input element. */
		
		return array(
			'score'   => Util::toFloat($xml->result->score),
			'artists' => $artists
		);
	}
}

?>
