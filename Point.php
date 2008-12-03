<?

/** Represents a geographical coordinate (latitude and longitude).
 *
 * @package	de.felixbruns.lastfm.api
 * @author  Felix Bruns <felixbruns@web.de>
 * @version	1.0
 */
class Point {
	/** Latitude.
	 *
	 * @var float
	 */
	private $lat;
	
	/** Longitude.
	 *
	 * @var float
	 */
	private $long;
	
	/** Create a Point object.
	 *
	 * @param string	lat		Latitude.
	 * @param string	long	Longitude.
	 */
	public function __construct($lat, $long){
		$this->lat  = $lat;
		$this->long = $long;
	}
	
	/** Returns the points latitude.
	 * 
	 * @return	float	Latitude value.
	 */
	public function getLatitude(){
		return $this->lat;
	}
	
	/** Returns the points longitude.
	 * 
	 * @return	float	Longitude value.
	 */
	public function getLongitude(){
		return $this->long;
	}
}

?>
