<?

class Venue {
	private $name;
	private $location;
	private $url;
	
	public function __construct($name, Location $location, $url){
		$this->name     = $name;
		$this->location = $location;
		$thus->url      = $url;
	}
	
	public function getName(){
		return $this->name;
	}
	
	public function getLocation(){
		return $this->location;
	}
	
	public function getUrl(){
		return $this->url;
	}
	
	public static function fromSimpleXMLElement(SimpleXMLElement $xml){
		return new Venue(
			Util::toString($xml->name),
			Location::fromSimpleXMLElement($xml->location),
			Util::toString($xml->url)
		);
	}
}

?>
