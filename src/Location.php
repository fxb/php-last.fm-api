<?php

/** Represents a geographical location (address, coordinate, timezone).
 *
 * @package	php-lastfm-api
 * @author  Felix Bruns <felixbruns@web.de>
 * @version	1.0
 */
class Location {
	/** The locations city.
	 *
	 * @var string
	 * @access	private
	 */
	private $city;

	/** The locations country.
	 *
	 * @var string
	 * @access	private
	 */
	private $country;

	/** The locations street.
	 *
	 * @var string
	 * @access	private
	 */
	private $street;

	/** The locations postal code.
	 *
	 * @var integer
	 * @access	private
	 */
	private $postalCode;

	/** The locations geographical coordinate.
	 *
	 * @var Point
	 * @access	private
	 */
	private $point;

	/** The locations timezone.
	 *
	 * @var string
	 * @access	private
	 */
	private $timezone;

	/** Create a Location object.
	 *
	 * @param string	$city		A city name.
 	 * @param string	$country	An ISO 3166-1 country code.
 	 * @param string	$street		A street name.
 	 * @param integer	$postalCode	A postal code.
 	 * @param Point		$point		A Point object.
 	 * @param string	$timezone	A timezone string.
	 *
	 * @access	public
	 */
	public function __construct($city, $country, $street, $postalCode,
								Point $point, $timezone){
		$this->city       = $city;
		$this->country    = $country;
		$this->street     = $street;
		$this->postalCode = $postalCode;
		$this->point      = $point;
		$this->timezone   = $timezone;
	}

	/** Returns the locations city.
	 *
	 * @return	string	A city name.
	 * @access	public
	 */
	public function getCity(){
		return $this->city;
	}

	/** Returns the locations country.
	 *
	 * @return	string	An ISO 3166-1 country code.
	 * @access	public
	 */
	public function getCountry(){
		return $this->country;
	}

	/** Returns the locations street.
	 *
	 * @return	string	A street name.
	 * @access	public
	 */
	public function getStreet(){
		return $this->street;
	}

	/** Returns the locations postal code.
	 *
	 * @return	integer	A postal code.
	 * @access	public
	 */
	public function getPostalCode(){
		return $this->postalCode;
	}

	/** Returns the locations geographical point.
	 *
	 * @return	string	A Point object.
	 * @access	public
	 */
	public function getPoint(){
		return $this->point;
	}

	/** Returns the locations timezone.
	 *
	 * @return	string	A timezone string.
	 * @access	public
	 */
	public function getTimezone(){
		return $this->timezone;
	}

	/** Create a Location object from a SimpleXMLElement.
	 *
	 * @param	SimpleXMLElement	$xml	A SimpleXMLElement.
	 * @return	Location					A Location object.
	 *
	 * @static
	 * @access	public
	 * @internal
	 */
	public static function fromSimpleXMLElement(SimpleXMLElement $xml){
		$geo = $xml->children('http://www.w3.org/2003/01/geo/wgs84_pos#');

		return new Location(
			Util::toString($xml->city),
			Util::toString($xml->country),
			Util::toString($xml->street),
			Util::toString($xml->postalcode),
			($geo->point)?new Point(
				Util::toFloat($geo->point->lat),
				Util::toFloat($geo->point->long)
			):null,
			Util::toString($xml->timezome)
		);
	}
}


