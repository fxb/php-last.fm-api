<?

/** Stores information of a last.fm session.
 *
 * @package	de.felixbruns.lastfm.api
 * @author  Felix Bruns <felixbruns@web.de>
 * @version	1.0
 */
class Session {
	/** The session username.
	 *
	 * @var string
	 */
	private $name;
	
	/** The session key.
	 *
	 * @var string
	 */
	private $key;
	
	/** Indicates if this user is a subscriber.
	 *
	 * @var boolean
	 */
	private $subscriber;
	
	/** Create a Session object.
	 *
	 * @param string	name		Session username.
	 * @param string	key			Session key.
	 * @param boolean	subscriber	User is subscriber.
	 */
	public function __construct($name, $key, $subscriber){
		$this->name       = $name;
		$this->key        = $key;
		$this->subscriber = $subscriber;
	}
	
	/** Returns the session username.
	 *
	 * @return string	A last.fm username.
	 */
	public function getName(){
		return $this->name;
	}
	
	/** Returns the session key.
	 *
	 * @return string	A session key.
	 */
	public function getKey(){
		return $this->key;
	}
	
	/** Returns if the user is a subscriber.
	 *
	 * @return boolean	true if the user is a subscriber, otherwise false.
	 */
	public function isSubscriber(){
		return $this->subscriber;
	}
	
	/** Create a Session object from a SimpleXMLElement.
	 * 
	 * @param	SimpleXMLElement	xml	A SimpleXMLElement.
	 * @return	Session					A Session object.
	 */
	public static function fromSimpleXMLElement(SimpleXMLElement $xml){
		return new Session(
			Util::toString($xml->name),
			Util::toString($xml->key),
			Util::toBoolean($xml->subscriber)
		);
	}
}

?>
