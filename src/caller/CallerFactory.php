<?php

/** Caller factory.
 *
 * @package	php-lastfm-api
 * @author  Felix Bruns <felixbruns@web.de>
 * @version	1.0
 */
class CallerFactory {
	/** A default {@link Caller} class.
	 *
	 * @var string
	 * @access	private
	 */
	private static $default = 'CurlCaller';

	/** Get a {@link CurlCaller} instance.
	 *
	 * @return	CurlCaller	A {@link CurlCaller} instance.
	 * @static
	 * @access	public
	 */
	public static function getCurlCaller(){
		return CurlCaller::getInstance();
	}

	/** Get a {@link PeclCaller} instance.
	 *
	 * @return	PeclCaller	A {@link PeclCaller} instance.
	 * @static
	 * @access	public
	 */
	public static function getPeclCaller(){
		return PeclCaller::getInstance();
	}

	/** Get a {@link SocketCaller} instance.
	 *
	 * @return	SocketCaller	A {@link SocketCaller} instance.
	 * @static
	 * @access	public
	 */
	public static function getSocketCaller(){
		return SocketCaller::getInstance();
	}

	/** Get a {@link Caller} instance.
	 *
	 * @return	Caller	A {@link Caller} instance.
	 * @static
	 * @access	public
	 */
	public static function getDefaultCaller(){
		/* > PHP 5.3.0
		return self::$default::getInstance();
		*/
		$function = 'get'.self::$default;

		return self::$function();
	}

	/** Sets the default {@link Caller}.
	 *
	 * @param	string	$caller	A Caller class name. (Required)
	 * @access	public
	 */
	public function setDefaultCaller($class){
		if(get_parent_class($class) == 'Caller'){
			self::$default = $class;
		}
		else{
			throw new Exception("Class '".$class."' does not extend 'Caller'!");
		}
	}
}


