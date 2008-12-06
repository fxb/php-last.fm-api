<?

/** Represents a venue.
 *
 * @package	de.felixbruns.lastfm.api
 * @author  Felix Bruns <felixbruns@web.de>
 * @version	1.0
 */
class Venue {
	/** The venues name.
	 *
	 * @var string
	 * @access private
	 */
	private $name;

	/** The venues location.
	 *
	 * @var Location
	 * @access private
	 */
	private $location;

	/** The venues URL.
	 *
	 * @var string
	 * @access private
	 */
	private $url;

	/** Create a Venue object.
	 *
	 * @param string	$name		A venue name.
	 * @param Location	$location	A venue location.
	 * @param string	$url		A venue URL.
	 *
	 * @access public
	 */
	public function __construct($name, Location $location, $url){
		$this->name     = $name;
		$this->location = $location;
		$thus->url      = $url;
	}

	/** Returns the venues name.
	 *
	 * @return	string	A venue name.
	 * @access public
	 */
	public function getName(){
		return $this->name;
	}

	/** Returns the venues location.
	 *
	 * @return	Location	A venue location.
	 * @access public
	 */
	public function getLocation(){
		return $this->location;
	}

	/** Returns the venues URL.
	 *
	 * @return	string	A venue URL.
	 * @access public
	 */
	public function getUrl(){
		return $this->url;
	}

	/** Create a Venue object from a SimpleXMLElement.
	 *
	 * @param	SimpleXMLElement	$xml	A SimpleXMLElement.
	 * @return	Venue						A Venue object.
	 *
	 * @static
	 * @access	public
	 * @internal
	 */
	public static function fromSimpleXMLElement(SimpleXMLElement $xml){
		return new Venue(
			Util::toString($xml->name),
			Location::fromSimpleXMLElement($xml->location),
			Util::toString($xml->url)
		);
	}
}

?>
