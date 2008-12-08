<?

/** Calls API methods using REST requests.
 *
 * @package	php-lastfm-api
 * @author  Felix Bruns <felixbruns@web.de>
 * @version	1.0
 */
class Caller {
	/** A Caller instance.
	 *
	 * @var Caller
	 * @access	private
	 */
	private static $instance;

	/** A cURL handle
	 *
	 * @var resource
	 * @access	private
	 */
	private $curl;

	/** A Cache instance
	 *
	 * @var Cache
	 * @access	private
	 */
	private $cache;

	/** An array of response headers.
	 *
	 * @var array
	 * @access	private
	 */
	private $headers;

	/** Last.fm API key
	 *
	 * @var string
	 * @access	private
	 */
	private $apiKey;

	/** Last.fm API secret
	 *
	 * @var string
	 * @access	private
	 */
	private $apiSecret;

	/** Last.fm API base URL
	 *
	 * @var string
	 * @access	public
	 */
	const API_URL = 'http://ws.audioscrobbler.com/2.0/';

	/** Private constructor that initializes cURL.
	 *
	 * @access	private
	 */
	private function __construct(){
		$this->curl  = curl_init();
		$this->cache = new DiskCache();

		curl_setopt($this->curl, CURLOPT_RETURNTRANSFER, 1);
		curl_setopt($this->curl, CURLOPT_USERAGENT, phpversion());
		curl_setopt($this->curl, CURLOPT_HEADERFUNCTION, array(&$this, 'header'));
	}

	/** Destructor that deinitializes cURL.
	 *
	 * @access	public
	 * @internal
	 */
	public function __destruct(){
		curl_close($this->curl);
	}

	/** Get a Caller instance.
	 *
	 * @return	Caller	A Caller instance.
	 * @static
	 * @access	public
	 */
	public static function getInstance(){
		if(!is_object(self::$instance)){
			self::$instance = new Caller();
		}

		return self::$instance;
	}

	/** Call an API method.
	 *
	 * @param	string	$method			API method to call. (Required)
	 * @param	array	$params			Request parameters to send. (Optional)
	 * @param	string	$requestMethod	Request-method for calling (defaults to 'GET'). (Optional)
	 * @return	SimpleXMLElement		A SimpleXMLElement object.
	 *
	 * @access	public
	 */
	public function call($method, array $params = array(), $requestMethod = 'GET'){
		/* Set call parameters */
		$callParams = array(
			'method'  => $method,
			'api_key' => $this->apiKey
		);

		/* Add call parameters to other request parameters */
		$params = array_merge($callParams, $params);
		$params = Util::toUTF8($params);

		/* Call API */
		return $this->internalCall($params, $requestMethod);
	}

	/** Call an API method which needs to be signed.
	 *
	 * @param	string	$method			API method to call. (Required)
	 * @param	array	$params			Request parameters to send. (Optional)
	 * @param	Session	$session		A session obtained by {@link de.felixbruns.lastfm.Auth#getSession Auth::getSession} or {@link de.felixbruns.lastfm.Auth#getMobileSession Auth::getMobileSession}. (Optional)
	 * @param	string	$requestMethod	Request-method for calling (defaults to 'GET'). (Optional)
	 * @return	SimpleXMLElement		A SimpleXMLElement object.
	 *
	 * @access	public
	 */
	public function signedCall($method, array $params = array(), $session = null,
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
		$params = array_merge($callParams, $params);
		$params = Util::toUTF8($params);

		/* Add API signature */
		$params['api_sig'] = Auth::getApiSignature($params, $this->apiSecret);

		/* Call API */
		return $this->internalCall($params, $requestMethod);
	}

	/** Send a query using a specified request-method.
	 *
	 * @param	string	$query			Query to send. (Required)
	 * @param	string	$requestMethod	Request-method for calling (defaults to 'GET'). (Optional)
	 * @return	SimpleXMLElement		A SimpleXMLElement object.
	 *
	 * @access	private
	 * @internal
	 */
	private function internalCall($params, $requestMethod = 'GET'){
		/* Create caching hash. */
		$hash = Cache::createHash($params);

		/* Check if response is cached. */
		if($this->cache != null &&
			$this->cache->contains($hash) &&
			!$this->cache->isExpired($hash)){
			/* Get cached response. */
			$response = $this->cache->load($hash);
		}
		else{
			/* Build request query */
			$query = http_build_query($params, '', '&');

			/* Set request options. */
			if($requestMethod === 'POST'){
				curl_setopt($this->curl, CURLOPT_URL, self::API_URL);
				curl_setopt($this->curl, CURLOPT_POST, 1);
				curl_setopt($this->curl, CURLOPT_POSTFIELDS, $query);
			}
			else{
				curl_setopt($this->curl, CURLOPT_URL, self::API_URL . '?' . $query);
				curl_setopt($this->curl, CURLOPT_POST, 0);
			}

			/* Clear response headers. */
			$this->headers = array();

			/* Get response. */
			$response = curl_exec($this->curl);

			/* Cache it. */
			if($this->cache != null){
				if(array_key_exists('Expires', $this->headers)){
					$this->cache->store(
						$hash, $response,
						strtotime($this->headers['Expires'])
					);
				}
				else{
					$expiration = $this->cache->getPolicy()->getExpirationTime($params);
					
					if($expiration > 0){
						$this->cache->store($hash, $response, time() + $expiration);
					}
				}
			}
		}

		/* Create SimpleXMLElement from response. */
		$response = new SimpleXMLElement($response);

		/* Return response or throw an error. */
		if(Util::toString($response['status']) === 'ok'){
			if($response->children()->{0}){
				return $response->children()->{0};
			}
		}
		else{
			throw new Error(
				Util::toString($response->error),
				Util::toInteger($response->error['code'])
			);
		}
	}

	/** Header callback for cURL.
	 *
	 * @param	resource	$cURL	A cURL handle.
	 * @param	string		$header	A HTTP response header.
	 *
	 * @access	private
	 * @internal
	 */
	private function header($cURL, $header){
		$parts = explode(': ', $header, 2);

		if(count($parts) == 2){
			list($key, $value) = $parts;

			$this->headers[$key] = trim($value);
		}

		return strlen($header);
	}

	/** Set the last.fm API key to be used.
	 *
	 * @param	string	$apiKey	A last.fm API key. (Required)
	 * @access	public
	 */
	public function setApiKey($apiKey){
		$this->apiKey = $apiKey;
	}

	/** Get the last.fm API key which is used.
	 *
	 * @return	string	A last.fm API key.
	 * @access	public
	 */
	public function getApiKey(){
		return $this->apiKey;
	}

	/** Set the last.fm API secret to be used.
	 *
	 * @param	string	$apiSecret	A last.fm API secret. (Required)
	 * @access	public
	 */
	public function setApiSecret($apiSecret){
		$this->apiSecret = $apiSecret;
	}

	/** Get the last.fm API secret which is used.
	 *
	 * @return	string	A last.fm API secret.
	 * @access	public
	 */
	public function getApiSecret(){
		return $this->apiSecret;
	}

	/** Sets the active {@link Cache} (null to disable caching).
	 *
	 * @param	Cache	$cache	A Cache object. (Required)
	 * @access	public
	 */
	public function setCache($cache){
		$this->cache = $cache;
	}

	/** Get the current {@link Cache}.
	 *
	 * @return	Cache	A Cache object.
	 * @access	public
	 */
	public function getCache(){
		return $this->cache;
	}
}

?>
