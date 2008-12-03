<?

/** Calls API methods using REST requests.
 *
 * @package	de.felixbruns.lastfm.api
 * @author  Felix Bruns <felixbruns@web.de>
 * @version	1.0
 */
class Caller {
	/** A Caller instance.
	 *
	 * @var Caller
	 */
	private static $instance;
	
	/** A cURL handle
	 *
	 * @var resource
	 */
	private $curl;
	
	/** Last error code
	 *
	 * @var integer
	 */
	private $errorCode;
	
	/** Last error message
	 *
	 * @var string
	 */
	private $errorMessage;
	
	/** Last.fm API key
	 *
	 * @var string
	 */
	private $apiKey;	
	
	/** Last.fm API secret
	 *
	 * @var string
	 */
	private $apiSecret;
	
	/** Last.fm API base URL
	 *
	 * @var string
	 */
	const API_URL = 'http://ws.audioscrobbler.com/2.0/';
	
	/** Private constructor that initializes cURL.
	 */
	private function __construct(){
		$this->curl = curl_init();
		
		curl_setopt($this->curl, CURLOPT_RETURNTRANSFER, 1);
		curl_setopt($this->curl, CURLOPT_USERAGENT, phpversion());
	}
	
	/** Destructor that deinitializes cURL.
	 */
	public function __destruct(){
		curl_close($this->curl);
	}
	
	/** Get a Caller instance.
	 * 
	 * @return	Caller	A Caller instance.
	 */
	public static function getInstance(){
		if(!is_object(self::$instance)){
			self::$instance = new Caller();
		}
		
		return self::$instance;
	}
	
	/** Call an API method.
	 * 
	 * @param	string	method			API method to call.
	 * @param	array	params			Request parameters to send.
	 * @param	string	requestMethod	Request-method for calling (defaults to 'GET').
	 * @return	mixed					true, false or a SimpleXMLElement.
	 */
	public function call($method, array $params = null, $requestMethod = 'GET'){
		/* Set call parameters */
		$callParams = array(
			'method'  => $method,
			'api_key' => $this->apiKey
		);
		
		/* Add call parameters to other request parameters */
		$params = ($params != null)?array_merge($callParams, $params):$callParams;
		$params = Util::toUTF8($params);
		
		/* Build request query */
		$query = http_build_query($params, '', '&');
		
		/* Call API */
		return $this->internalCall($query, $requestMethod);
	}
	
	/** Call an API method which needs to be signed.
	 * 
	 * @param	string	method			API method to call.
	 * @param	array	params			Request parameters to send.
	 * @param	Session	session			A session obtained by {@link de.felixbruns.lastfm.Auth#getSession Auth::getSession} or {@link de.felixbruns.lastfm.Auth#getMobileSession Auth::getMobileSession}.
	 * @param	string	requestMethod	Request-method for calling (defaults to 'GET').
	 * @return	mixed					true, false or a SimpleXMLElement.
	 */
	public function signedCall($method, array $params = null, $session = null,
							   $requestMethod = 'GET'){
		/* Set call parameters */
		$callParams = array(
			'method'  => $method,
			'api_key' => $this->apiKey
		);
		
		/* If session is set, add session key */
		if($session != null){
			$callParams['sk'] = $session->getKey();
		}
		
		/* Add call parameters to other request parameters */
		$params = ($params != null)?array_merge($callParams, $params):$callParams;
		$params = Util::toUTF8($params);
		
		/* Add API signature */
		$params['api_sig'] = Auth::getApiSignature($params, $this->apiSecret);
		
		/* Build request query */
		$query = http_build_query($params, '', '&');
		
		/* Call API */
		return $this->internalCall($query, $requestMethod);
	}
	
	/** Send a query using a specified request-method.
	 * 
	 * @param	string	query			Query to send.
	 * @param	string	requestMethod	Request-method for calling (defaults to 'GET').
	 * @return	mixed					true, false or a SimpleXMLElement.
	 */
	private function internalCall($query, $requestMethod = 'GET'){
		/* Set request options */
		if($requestMethod === 'POST'){
			curl_setopt($this->curl, CURLOPT_URL, self::API_URL);
			curl_setopt($this->curl, CURLOPT_POST, 1);
			curl_setopt($this->curl, CURLOPT_POSTFIELDS, $query);
		}
		else{
			curl_setopt($this->curl, CURLOPT_URL, self::API_URL . '?' . $query);
			curl_setopt($this->curl, CURLOPT_POST, 0);
		}
		
		/* Get response */
		/* TODO: FIXED. namespaces, Fix colons... preg_replace('/<([^:\s>]+):([^>]+)>/', '<$1$2>', curl_exec($this->curl)); */
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
	
	/** Returns the last error code.
	 * 
	 * @return	integer	A last.fm API error code.
	 */
	public function getErrorCode(){
		return $this->errorCode;
	}
	
	/** Returns the last error message.
	 * 
	 * @return	string	A last.fm API error message.
	 */
	public function getErrorMessage(){
		return $this->errorMessage;
	}
	
	/** Set the last.fm API key to be used.
	 * 
	 * @param	string	apiKey	A last.fm API key.
	 */
	public function setApiKey($apiKey){
		$this->apiKey = $apiKey;
	}
	
	/** Get the last.fm API key which is used.
	 * 
	 * @return	string	A last.fm API key.
	 */
	public function getApiKey(){
		return $this->apiKey;
	}
	
	/** Set the last.fm API secret to be used.
	 * 
	 * @param	string	apiSecret	A last.fm API secret.
	 */
	public function setApiSecret($apiSecret){
		$this->apiSecret = $apiSecret;
	}
	
	/** Get the last.fm API secret which is used.
	 * 
	 * @return	string	A last.fm API secret.
	 */
	public function getApiSecret(){
		return $this->apiSecret;
	}
}

?>
