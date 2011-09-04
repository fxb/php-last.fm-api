<?php

/** Authentication methods.
 *
 * @package	php-lastfm-api
 * @author  Felix Bruns <felixbruns@web.de>
 * @version	1.0
 */
class Auth {
	/** Returns a last.fm API signature for the given request parameters.
	 *
	 * @param	array	$params		Request parameters.
	 * @param	string	$apiSecret	Last.fm API secret.
	 * @return	string				Last.fm API signature.
	 *
	 * @static
	 * @access	public
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

	/** Create a web service session for a user. Used for authenticating a user when the password can be inputted by the user. Only suitable for standalone mobile devices. See the authentication how-to for more.
	 *
	 * @param	string	$username	The last.fm username. (Required)
	 * @param	string	$password	The last.fm password. (Required)
     * @param   bool    $apply_md5  Set to false if the supplied password has already been hashed. (Optional)
	 * @return	Session				A Session object.
	 *
	 * @static
	 * @access	public
	 * @throws	Error
	 */
	public static function getMobileSession($username, $password, $apply_md5 = true){
		$xml = CallerFactory::getDefaultCaller()->signedCall('auth.getMobileSession', array(
			'username'  => $username,
			'authToken' => md5($username . (($apply_md5) ? md5($password) : $password))
		));

		return Session::fromSimpleXMLElement($xml);
	}

	/** Returns a session using an authorized token.
	 *
	 * @param	string	$token	A 32-character ASCII hexadecimal MD5 hash returned by step 1 of the authentication process (following the granting of permissions to the application by the user). (Required)
	 * @return	Session			A Session object.
	 *
	 * @static
	 * @access	public
	 * @throws	Error
	 */
	public static function getSession($token){
		$xml = CallerFactory::getDefaultCaller()->signedCall('auth.getSession', array(
			'token' => $token
		));

		return Session::fromSimpleXMLElement($xml);
	}

	/** Fetch an unauthorized request token for an API account. This is step 2 of the authentication process for desktop applications. Web applications do not need to use this service.
	 *
	 * @return	string	A 32-character ASCII hexadecimal MD5 hash.
	 *
	 * @static
	 * @access	public
	 * @throws	Error
	 */
	public static function getToken(){
		$xml = CallerFactory::getDefaultCaller()->signedCall('auth.getToken');

		return Util::toString($xml);
	}

	/** Used by our flash embeds (on trusted domains) to use a site session cookie to seed a ws session without requiring a password. Uses the site cookie so must be accessed over a *.last.fm domain.
	 *
	 * @return	Session	A Session object.
	 *
	 * @static
	 * @access	public
	 * @throws	Error
	 */
	public static function getWebSession(){
		$xml = CallerFactory::getDefaultCaller()->signedCall('auth.getWebSession');

		return Session::fromSimpleXMLElement($xml);
	}
}


