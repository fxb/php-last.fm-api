<?php

/** Calls API methods using REST requests using PHP sockets.
 *
 * @package	php-lastfm-api
 * @author  Felix Bruns <felixbruns@web.de>
 * @version	1.0
 */
final class SocketCaller extends Caller {
	/** A SocketCaller instance.
	 *
	 * @var SocketCaller
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
			self::$instance = new SocketCaller();
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
			/* Build request query. */
			$query = http_build_query($params, '', '&');

			/* Extract URL information. */
			$info = parse_url(self::API_URL);

			/* TODO: Accept-Encoding: deflate, gzip */
			/* Set request data. */
			if($requestMethod === 'POST'){
				$data  = "POST " . $info['path'] . " HTTP/1.1\r\n";
				$data .= "Host: ". $info['host'] ."\r\n";
				$data .= "User-Agent: PHP last.fm API (PHP/" . phpversion() . ")\r\n";
				$data .= "Content-Type: application/x-www-form-urlencoded\r\n";
				$data .= "Content-Length: " . strlen($query) ."\r\n";
				$data .= "Connection: Close\r\n\r\n";
				$data .= $query;
			}
			else{
				$data  = "GET " . $info['path'] . "?" . $query ." HTTP/1.1\r\n";
				$data .= "Host: ". $info['host'] ."\r\n";
				$data .= "User-Agent: PHP last.fm API (PHP/" . phpversion() . ")\r\n";
				$data .= "Connection: Close\r\n\r\n";
			}

			/* Open socket. */
			$socket = fsockopen($info['host'], 80);

			/* Write request. */
			fwrite($socket, $data);

			/* Clear response headers. */
			$this->headers = array();

			/* Read headers. */
			while(($line = fgets($socket)) !== false && $line != "\r\n"){
				$this->header($line);
			}

			/* Read response. */
			$response = "";
			while(($line = fgets($socket)) !== false && $line != "\r\n"){
				$response .= $line;
			}

			/* Close socket. */
			fclose($socket);

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

	/** Header callback.
	 *
	 * @param	string	$header	A HTTP response header.
	 *
	 * @access	private
	 * @internal
	 */
	private function header($header){
		$parts = explode(': ', $header, 2);

		if(count($parts) == 2){
			list($key, $value) = $parts;

			$this->headers[$key] = trim($value);
		}

		return strlen($header);
	}
}


