<?

/** Authentication methods.
 *
 * @package	de.felixbruns.lastfm.api
 * @author  Felix Bruns <felixbruns@web.de>
 * @version	1.0
 */
class Auth {
	/** Returns a last.fm API signature for the given request parameters.
	 * 
	 * @param	array	params		Request parameters.
	 * @param	string	apiSecret	Last.fm API secret.
	 * @return	string				Last.fm API signature.
	 */
	public static function getApiSignature(array $params, $apiSecret){
		ksort($params);
		
		$string = '';
		
		foreach($params as $name => $value){
			$string .= $name . $value;
		}
		
		$string .= $apiSecret;
		
		return md5($string);
	}
	
	/** Returns a mobile session using username and password.
	 * 
	 * @param	string	username	Last.fm username.
	 * @param	string	password	Last.fm password.
	 * @return	mixed				A Session object or false on failure.
	 */
	public static function getMobileSession($username, $password){
		$xml = Caller::getInstance()->signedCall('auth.getMobileSession', array(
			'username'  => $username,
			'authToken' => md5($username . md5($password))
		));
		
		return Session::fromSimpleXMLElement($xml);
	}
	
	/** Returns a session using an authorized token.
	 * 
	 * @param	string	token	Token obtained by {@link de.felixbruns.lastfm.Auth#getToken Auth::getToken}.
	 * @return	mixed			A Session object or false on failure.
	 */
	public static function getSession($token){
		$xml = Caller::getInstance()->signedCall('auth.getSession', array(
			'token' => $token
		));
		
		if($xml !== false){
			return Session::fromSimpleXMLElement($xml);
		}
		else{
			return false;
		}
	}
	
	/** Returns an unauthorized token.
	 * 
	 * @return	string	Token string.
	 */
	public static function getToken(){
		$xml = Caller::getInstance()->signedCall('auth.getToken');
		
		return Util::toString($xml);
	}
	
	/** Returns an anonymous web session.
	 * 
	 * @return	mixed	A Session object or false on failure.
	 */
	public static function getWebSession(){
		$xml = Caller::getInstance()->signedCall('auth.getWebSession');
		
		if($xml !== false){
			return Session::fromSimpleXMLElement($xml);
		}
		else{
			return false;
		}
	}
}

?>
