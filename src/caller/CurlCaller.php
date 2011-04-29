<?php

/** Calls API methods using REST requests using cURL.
 *
 * @package	php-lastfm-api
 * @author  Felix Bruns <felixbruns@web.de>
 * @version	1.0
 */
final class CurlCaller extends Caller {
	/** A CurlCaller instance.
	 *
	 * @var CurlCaller
	 * @access	protected
	 */
	private static $instance;

	/** A cURL handle
	 *
	 * @var resource
	 * @access	private
	 */
	private $curl;

	/** An array of response headers.
	 *
	 * @var array
	 * @access	private
	 */
	private $headers;

	/** Private constructor that initializes cURL.
	 *
	 * @access	private
	 */
	private function __construct(){
		$this->curl  = curl_init();

		curl_setopt($this->curl, CURLOPT_RETURNTRANSFER, 1);
		curl_setopt($this->curl, CURLOPT_USERAGENT, "PHP last.fm API (PHP/" . phpversion() . ")");
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
			self::$instance = new CurlCaller();
		}

		return self::$instance;
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
	protected function internalCall($params, $requestMethod = 'GET'){
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
}


