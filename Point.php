<?

class Point {
	private $lat;
	private $long;
	
	public function __construct($lat, $long){
		$this->lat  = $lat;
		$this->long = $long;
	}
	
	public function getLatitude(){
		return $this->lat;
	}
	
	public function getLongitude(){
		return $this->long;
	}
}

?>
