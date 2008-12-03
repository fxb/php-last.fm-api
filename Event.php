<?

class Event {
	private $id;
	private $title;
	private $artists;
	private $venue;
	private $startDate;
	private $description;
	private $images;
	private $url;
	private $attendance;
	private $reviews;
	private $tag;
	
	const ATTENDING       = 0;
	const MAYBE_ATTENDING = 1;
	const NOT_ATTENDING   = 2;
	
	public function __construct($id, $title, array $artists, Venue $venue,
								$startDate, $description, $images, $url,
								$attendance, $reviews, $tag){
		$this->id          = $id;
		$this->title       = $title;
		$this->artists     = $artists;
		$this->venue       = $venue;
		$this->startDate   = $startDate;
		$this->description = $description;
		$this->images      = $images;
		$this->url         = $url;
		$this->attendance  = $attendance;
		$this->reviews     = $reviews;
		$this->tag         = $tag;
	}
	
	public function getId(){
		return $this->id;
	}
	
	public function getTitle(){
		return $this->title;
	}
	
	public function getArtists(){
		return $this->artists;
	}
	
	public function getVenue(){
		return $this->venue;
	}
	
	public function getStartDate(){
		return $this->startDate;
	}
	
	public function getDescription(){
		return $this->description;
	}
	
	public function getImages(){
		return $this->images;
	}
	
	public function getUrl(){
		return $this->url;
	}
	
	public function getAttendance(){
		return $this->attendance;
	}
	
	public function getReviews(){
		return $this->reviews;
	}
	
	public function getTag(){
		return $this->tag;
	}
	
	public static function attend($event, $status, $session){
		$xml = Caller::getInstance()->signedCall('event.attend', array(
			'event'   => $event,
			'status'  => $status,
			'api_key' => $session->getApiKey(),
			'sk'      => $session->getKey()
		), $session->getApiSecret, 'POST');
		
		return $xml;
	}
	
	public static function getInfo($event, $apiKey){
		$xml = Caller::getInstance()->call('event.getInfo', array(
			'event'   => $event,
			'api_key' => $apiKey
		));
		
		if($xml !== false){
			return self::fromSimpleXMLElement($xml);
		}
		else{
			return false;
		}
	}
	
	public static function share($event, array $recipients, $message, $session){
		$xml = Caller::getInstance()->signedCall('event.share', array(
			'event'     => $event,
			'recipient' => implode(',', $recipients),
			'message'   => $message,
			'api_key'   => $session->getApiKey(),
			'sk'        => $session->getKey()
		), $session->getApiSecret(), 'POST');
		
		return $xml;
	}
	
	public static function getPlaylist($event, $apiKey){
		$xml = Caller::getInstance()->call('event.getPlayerMenu', array(
			'event'   => $event,
			'api_key' => $apiKey
		));
		
		return Playlist::fetch(
			Util::toString($xml->playlist->url),
			true,
			true,
			$apiKey
		);
	}
	
	public static function fromSimpleXMLElement(SimpleXMLElement $xml){
		$artists = array();
		$images  = array();
		
		if($xml->artists){
			foreach($xml->artists->artist as $artist){
				$artists[] = Util::toString($artist);
			}
			
			$artists['headliner'] = Util::toString($xml->artists->headliner);
		}
		
		if($xml->image){
			foreach($xml->image as $image){
				$images[Util::toImageType($image['size'])] =
					Util::toString($image);
			}
		}
		
		return new Event(
			Util::toInteger($xml->id),
			Util::toString($xml->title),
			$artists,
			($xml->venue)?Venue::fromSimpleXMLElement($xml->venue):null,
			Util::toTimestamp($xml->startDate),
			Util::toString($xml->description),
			$images,
			Util::toString($xml->url),
			Util::toInteger($xml->attendance),
			Util::toInteger($xml->reviews),
			Util::toString($xml->tag)
		);
	}
}

?>
