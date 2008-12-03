<?

class Auth {
	public static function getApiSignature(array $params, $apiSecret){
		ksort($params);
		
		$string = '';
		
		foreach($params as $name => $value){
			$string .= $name . $value;
		}
		
		$string .= $apiSecret;
		
		return md5($string);
	}
	
	public static function getMobileSession($username, $password, $apiKey,
											$apiSecret){
		$xml = Caller::getInstance()->signedCall('auth.getMobileSession', array(
			'username'  => $username,
			'authToken' => md5($username . md5($password)),
			'api_key'   => $apiKey
		), $apiSecret);
		
		return Session::fromSimpleXMLElement($xml, $apiKey, $apiSecret);
	}
	
	public static function getSession($token, $apiKey, $apiSecret){
		$xml = Caller::getInstance()->signedCall('auth.getSession', array(
			'token'   => $token,
			'api_key' => $apiKey
		), $apiSecret);
		
		return Session::fromSimpleXMLElement($xml, $apiKey, $apiSecret);
	}
	
	public static function getToken($apiKey, $apiSecret){
		$xml = Caller::getInstance()->signedCall('auth.getToken', array(
			'api_key' => $apiKey
		), $apiSecret);
		
		return Util::toString($xml);
	}
}

?>
