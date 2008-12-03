<?

class Caller {
	private static $instance;
	
	private $curl;
	
	private $errorCode;
	private $errorMessage;
	
	const API_URL = 'http://ws.audioscrobbler.com/2.0/';
	
	private function __construct(){
		$this->curl = curl_init();
		
		/* Set cURL options */
		curl_setopt($this->curl, CURLOPT_RETURNTRANSFER, 1);
		curl_setopt($this->curl, CURLOPT_USERAGENT, phpversion());
	}
	
	public function __destruct(){
		curl_close($this->curl);
	}
	
	public static function getInstance(){
		/* If no instance exists, create one */
		if(!is_object(self::$instance)){
			self::$instance = new Caller();
		}
		
		/* Return instance */
		return self::$instance;
	}
	
	public function call($method, array $params, $request_method = 'GET'){
		/* Add method to request parameters */
		$params = array_merge(array('method' => $method), $params);
		$params = array_map('utf8_encode', $params);
		
		/* Build request query */
		$query = http_build_query($params, '', '&');
		
		/* Call API */
		return $this->internalCall($query, $request_method);
	}
	
	public function signedCall($method, array $params, $secret,
							   $request_method = 'GET'){
		/* Add method to request parameters */
		$params = array_merge(array('method' => $method), $params);
		$params = array_map('utf8_encode', $params);
		
		/* Get API signature */
		$params['api_sig'] = Auth::getApiSignature($params, $secret);
		
		/* Build request query */
		$query = http_build_query($params, '', '&');
		
		/* Call API */
		return $this->internalCall($query, $request_method);
	}
	
	private function internalCall($query, $request_method = 'GET'){
		/* Set request options */
		if($request_method === 'POST'){
			curl_setopt($this->curl, CURLOPT_URL, self::API_URL);
			curl_setopt($this->curl, CURLOPT_POST, 1);
			curl_setopt($this->curl, CURLOPT_POSTFIELDS, $query);
		}
		else{
			curl_setopt($this->curl, CURLOPT_URL, self::API_URL . '?' . $query);
			curl_setopt($this->curl, CURLOPT_POST, 0);
		}
		
		/* Get response */
		/* Fix colons... preg_replace('/<([^:\s>]+):([^>]+)>/', '<$1$2>', curl_exec($this->curl)); */
		$response = new SimpleXMLElement(curl_exec($this->curl));
		
		/* Return response */
		if(Util::toString($response['status']) === 'ok'){
			if($response->children()->{0}){
				return $response->children()->{0};
			}
			
			return true;
		}
		else{
			$this->errorCode    = Util::toInteger($response->error['code']);
			$this->errorMessage = Util::toString($response->error);
			
			return false;
		}
	}
	
	public function getLastError(){
		return $this->errorCode;
	}
	
	public function getLastErrorMessage(){
		return $this->errorMessage;
	}
}

?>
