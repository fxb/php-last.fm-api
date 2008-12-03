<?

class Session {
	private $name;
	private $key;
	private $subscriber;
	private $apiKey;
	private $apiSecret;
	
	public function __construct($name, $key, $subscriber, $apiKey, $apiSecret){
		$this->name       = $name;
		$this->key        = $key;
		$this->subscriber = $subscriber;
		$this->apiKey     = $apiKey;
		$this->apiSecret  = $apiSecret;
	}
	
	public function getName(){
		return $this->name;
	}
	
	public function getKey(){
		return $this->key;
	}
	
	public function isSubscriber(){
		return $this->subscriber;
	}
	
	public function getApiKey(){
		return $this->apiKey;
	}
	
	public function getApiSecret(){
		return $this->apiSecret;
	}
	
	public static function fromSimpleXMLElement(SimpleXMLElement $xml, $apiKey,
												$apiSecret){
		return new Session(
			Util::toString($xml->name),
			Util::toString($xml->key),
			Util::toInteger($xml->subscriber),
			$apiKey,
			$apiSecret
		);
	}
}

?>
