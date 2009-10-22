<?php

/** Stores information of a last.fm session.
 *
 * @package	php-lastfm-api
 * @author  Felix Bruns <felixbruns@web.de>
 * @version	1.0
 */
class Session {
	/** The session username.
	 *
	 * @var string
	 * @access	private
	 */
	private $name;

	/** The session key.
	 *
	 * @var string
	 * @access	private
	 */
	private $key;

	/** Indicates if this user is a subscriber.
	 *
	 * @var boolean
	 * @access	private
	 */
	private $subscriber;

	/** Create a Session object.
	 *
	 * @param string	$name		Session username.
	 * @param string	$key		Session key.
	 * @param boolean	$subscriber	User is subscriber.
	 *
	 * @access	public
	 */
	public function __construct($name, $key, $subscriber){
		$this->name       = $name;
		$this->key        = $key;
		$this->subscriber = $subscriber;
	}

	/** Returns the session username.
	 *
	 * @return string	A last.fm username.
	 * @access	public
	 */
	public function getName(){
		return $this->name;
	}

	/** Returns the session key.
	 *
	 * @return string	A session key.
	 * @access	public
	 */
	public function getKey(){
		return $this->key;
	}

	/** Returns if the user is a subscriber.
	 *
	 * @return boolean	true if the user is a subscriber, otherwise false.
	 * @access	public
	 */
	public function isSubscriber(){
		return $this->subscriber;
	}

	/** Create a Session object from a SimpleXMLElement.
	 *
	 * @param	SimpleXMLElement	$xml	A SimpleXMLElement.
	 * @return	Session						A Session object.
	 *
	 * @static
	 * @access	public
	 * @internal
	 */
	public static function fromSimpleXMLElement(SimpleXMLElement $xml){
		return new Session(
			Util::toString($xml->name),
			Util::toString($xml->key),
			Util::toBoolean($xml->subscriber)
		);
	}
}


