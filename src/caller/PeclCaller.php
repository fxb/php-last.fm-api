<?php

/** Calls API methods using REST requests using PECL HTTP.
 *
 * @package	php-lastfm-api
 * @author  Felix Bruns <felixbruns@web.de>
 * @version	1.0
 */
final class PeclCaller extends Caller {
	/** A PeclCaller instance.
	 *
	 * @var PeclCaller
	 * @access	protected
	 */
	private static $instance;

	/** An array of response headers.
	 *
	 * @var array
	 * @access	private
	 */
	private $headers;

	/** Private constructor.
	 *
	 * @access	private
	 */
	private function __construct(){
		$this->cache = new DiskCache();
	}

	/** Get a Caller instance.
	 *
	 * @return	Caller	A Caller instance.
	 * @static
	 * @access	public
	 */
	public static function getInstance(){
		if(!is_object(self::$instance)){
			self::$instance = new PeclCaller();
		}

		return self::$instance;
	}

	/** Send a query using a specified request-method.
	 *
	 * @param	string	$query			Query to send. (Required)
	 * @param	string	$requestMethod	Request-method for calling (defaults to 'GET'). (Optional)
	 * @return	SimpleXMLElement		A SimpleXMLElement object.
	 *
	 * @access	protected
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
			/* Build request query. */
			$query = http_build_str($params, '', '&');

			/* Set request options. */
			$options = array(
				'useragent' => 'PHP last.fm API (PHP/' . phpversion() . ')'
			);

			/* Clear response headers. */
			$this->headers = array();

			/* Get response */
			if($requestMethod === 'POST'){
				$response = http_post_data(self::API_URL, $query, $options, $info);
			}
			else{
				$response = http_get(self::API_URL . '?' . $query, $options, $info);
			}

			$response = http_parse_message($response);

			foreach($response->headers as $header => $value){
				$this->headers[$header] = $value;
			}
			
			$response = $response->body;

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
}


