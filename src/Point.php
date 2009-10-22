<?php

/** Represents a geographical coordinate (latitude and longitude).
 *
 * @package	php-lastfm-api
 * @author  Felix Bruns <felixbruns@web.de>
 * @version	1.0
 */
class Point {
	/** Latitude.
	 *
	 * @var float
	 * @access	private
	 */
	private $lat;

	/** Longitude.
	 *
	 * @var float
	 * @access	private
	 */
	private $long;

	/** Create a Point object.
	 *
	 * @param string	$lat	Latitude.
	 * @param string	$long	Longitude.
	 *
	 * @access	public
	 */
	public function __construct($lat, $long){
		$this->lat  = $lat;
		$this->long = $long;
	}

	/** Returns the points latitude.
	 *
	 * @return	float	Latitude value.
	 * @access	public
	 */
	public function getLatitude(){
		return $this->lat;
	}

	/** Returns the points longitude.
	 *
	 * @return	float	Longitude value.
	 * @access	public
	 */
	public function getLongitude(){
		return $this->long;
	}
}


