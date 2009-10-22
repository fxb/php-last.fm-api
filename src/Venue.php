<?php

/** Represents a venue.
 *
 * @package	php-lastfm-api
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

	/** Get a list of upcoming events at this venue.
	 *
	 * @param	string	$venue	The venue id to fetch the events for. (Required)
	 * @return	array			An array of Event objects.
	 *
	 * @static
	 * @access	public
	 * @throws	Error
	 */
	public static function getEvents($event){
		$xml = CallerFactory::getDefaultCaller()->call('venue.getEvents', array(
			'event' => $event
		));

		$events = array();

		foreach($xml->children() as $event){
			$events[] = Event::fromSimpleXMLElement($event);
		}

		return $events;
	}

	/** Get a paginated list of all the events held at this venue in the past.
	 *
	 * @param	string	$venue	The id for the venue you would like to fetch event listings for. (Required)
	 * @param	integer	$limit	The maximum number of results to return. (Optional)
	 * @param	integer	$page	The page of results to return. (Optional)
	 * @return	PaginatedResult	A PaginatedResult object.
	 * @see		PaginatedResult
	 *
	 * @static
	 * @access	public
	 * @throws	Error
	 */
	public static function getPastEvents($venue, $limit = null, $page = null){
		$xml = CallerFactory::getDefaultCaller()->call('venue.getPastEvents', array(
			'venue' => $venue,
			'limit' => $limit,
			'page'  => $page
		));

		$events = array();

		foreach($xml->children() as $event){
			$events[] = Event::fromSimpleXMLElement($event);
		}

		$perPage = Util::toInteger($xml['perPage']);

		return new PaginatedResult(
			Util::toInteger($xml['total']),
			(Util::toInteger($xml['page']) - 1) * $perPage,
			$perPage,
			$events
		);
	}

	/** Search for a venue by venue name .
	 *
	 * @param	string	$venue		The venue name you would like to search for. (Required)
	 * @param	integer	$limit		The number of results to fetch per page. Defaults to 50. (Optional)
	 * @param	integer	$page		The results page you would like to fetch. (Optional)
	 * @param	string	$country	Filter your results by country. Expressed as an ISO 3166-2 code. (Optional)
	 * @return	PaginatedResult		A PaginatedResult object.
	 * @see		PaginatedResult
	 *
	 * @static
	 * @access	public
	 * @throws	Error
	 */
	public static function search($venue, $limit = null, $page = null, $country = null){
		$xml = CallerFactory::getDefaultCaller()->call('venue.search', array(
			'venue'   => $venue,
			'limit'   => $limit,
			'page'    => $page,
			'country' => $country
		));

		$venues = array();

		foreach($xml->venuematches->children() as $venue){
			$venues[] = Venue::fromSimpleXMLElement($venue);
		}

		$opensearch = $xml->children('http://a9.com/-/spec/opensearch/1.1/');

		return new PaginatedResult(
			Util::toInteger($opensearch->totalResults),
			Util::toInteger($opensearch->startIndex),
			Util::toInteger($opensearch->itemsPerPage),
			$venues
		);
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


