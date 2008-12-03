<?

class Location {
	private $city;
	private $country;
	private $street;
	private $postalCode;
	private $point;
	private $timeZone;
	
	public function __construct($city, $country, $street, $postalCode,
								Point $point, $timeZone){
		$this->city       = $city;
		$this->country    = $country;
		$this->street     = $street;
		$this->postalCode = $postalCode;
		$this->point      = $point;
		$this->timeZone   = $timeZone;
	}
	
	public function getCity(){
		return $this->city;
	}
	
	public function getCountry(){
		return $this->country;
	}
	
	public function getStreet(){
		return $this->street;
	}
	
	public function getPostalCode(){
		return $this->postalCode;
	}
	
	public function getPoint(){
		return $this->point;
	}
	
	public function getTimeZone(){
		return $this->timeZone;
	}
	
	public static function fromSimpleXMLElement(SimpleXMLElement $xml){
		$geo = $xml->children('http://www.w3.org/2003/01/geo/wgs84_pos#');
		
		return new Location(
			Util::toString($xml->city),
			Util::toString($xml->country),
			Util::toString($xml->street),
			Util::toString($xml->postalcode),
			($geo->point)?new Point(
				Util::toFloat($geo->point->lat),
				Util::toFloat($geo->point->long)
			):null,
			Util::toString($xml->timezome)
		);
	}
}

?>
