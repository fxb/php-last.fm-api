<?php

/** Represents an event and provides different methods to query event information.
 *
 * @package	php-lastfm-api
 * @author  Felix Bruns <felixbruns@web.de>
 * @version	1.0
 */
class Event {
	/** Event id.
	 *
	 * @var integer
	 * @access	private
	 */
	private $id;

	/** Event title.
	 *
	 * @var string
	 * @access	private
	 */
	private $title;

	/** Event artists.
	 *
	 * @var array
	 * @access	private
	 */
	private $artists;

	/** Event venue.
	 *
	 * @var Venue
	 * @access	private
	 */
	private $venue;

	/** Event start date (unix timestamp).
	 *
	 * @var integer
	 * @access	private
	 */
	private $startDate;

	/** Event description.
	 *
	 * @var string
	 * @access	private
	 */
	private $description;

	/** An array of images of this event.
	 *
	 * @var array
	 * @access	private
	 */
	private $images;

	/** Event last.fm URL.
	 *
	 * @var string
	 * @access	private
	 */
	private $url;

	/** Number of users attending this event.
	 *
	 * @var integer
	 * @access	private
	 */
	private $attendance;

	/** Number of reviews of this event.
	 *
	 * @var integer
	 * @access	private
	 */
	private $reviews;

	/** Tag of this event (e.g. lastfm:event=<id>).
	 *
	 * @var string
	 * @access	private
	 */
	private $tag;

	/** Possible attendance statuses.
	 *
	 * @var integer
	 * @access	public
	 */
	const ATTENDING       = 0;
	const MAYBE_ATTENDING = 1;
	const NOT_ATTENDING   = 2;

	/** Create an event object.
	 *
	 * @param integer	$id				Event ID.
	 * @param string	$title			Event title.
	 * @param array		$artists		An array of Artist objects.
	 * @param Venue		$venue			A Venue object.
	 * @param integer	$startDate		A start date (unix timestamp).
	 * @param string	$description	An event description.
	 * @param array		$images			An array of cover art images of different sizes.
	 * @param string	$url			A last.fm event URL.
	 * @param integer	$attendance		The Number of users attending this event.
	 * @param integer	$reviews		The Number of reviews of this event.
	 * @param string	$tag			Tag of this event.
	 *
	 * @access	public
	 */
	public function __construct($id, $title, array $artists, Venue $venue,
								$startDate, $description, $images, $url,
								$attendance, $reviews, $tag){
		$this->id          = $id;
		$this->title       = $title;
		$this->artists     = $artists;
		$this->venue       = $venue;
		$this->startDate   = $startDate;
		$this->description = $description;
		$this->images      = $images;
		$this->url         = $url;
		$this->attendance  = $attendance;
		$this->reviews     = $reviews;
		$this->tag         = $tag;
	}

	/** Returns the ID of this event.
	 *
	 * @return	integer	ID of this event.
	 * @access	public
	 */
	public function getId(){
		return $this->id;
	}

	/** Returns the title of this event.
	 *
	 * @return	string	Title of this event.
	 * @access	public
	 */
	public function getTitle(){
		return $this->title;
	}

	/** Returns the artists of this event.
	 *
	 * @return	array	An array of Artist objects.
	 * @access	public
	 */
	public function getArtists(){
		return $this->artists;
	}

	/** Returns the venue of this event.
	 *
	 * @return	Venue	A Venue object.
	 * @access	public
	 */
	public function getVenue(){
		return $this->venue;
	}

	/** Returns the start date of this event.
	 *
	 * @return	integer	A unix timestamp.
	 * @access	public
	 */
	public function getStartDate(){
		return $this->startDate;
	}

	/** Returns the description of this event.
	 *
	 * @return	string	A description string.
	 * @access	public
	 */
	public function getDescription(){
		return $this->description;
	}

	/** Returns an image URL of the specified size.
	 *
	 * @param	integer	$size	Image size constant.
	 * @return	string			An image URL.
	 * @access	public
	 */
	public function getImage($size){
		return $this->images[$size];
	}

	/** Returns the last.fm URL of this event.
	 *
	 * @return	string	A last.fm URL.
	 * @access	public
	 */
	public function getUrl(){
		return $this->url;
	}

	/** Returns number of users attending this event.
	 *
	 * @return	integer	Number of users attending.
	 * @access	public
	 */
	public function getAttendance(){
		return $this->attendance;
	}

	/** Returns number of reviews of this event.
	 *
	 * @return	integer	Number of reviews.
	 * @access	public
	 */
	public function getReviews(){
		return $this->reviews;
	}

	/** Returns the tag of this event.
	 *
	 * @return	Tag	A Tag object.
	 * @access	public
	 */
	public function getTag(){
		return $this->tag;
	}

	/** Set a user's attendance status for an event.
	 *
	 * @param	integer	$event		The numeric last.fm event ID. (Required)
	 * @param	integer	$status		The attendance status (ATTENDING, MAYBE_ATTENDING, NOT_ATTENDING). (Required)
	 * @param	Session	$session	A session obtained by {@link de.felixbruns.lastfm.Auth#getSession Auth::getSession} or {@link de.felixbruns.lastfm.Auth#getMobileSession Auth::getMobileSession}. (Required)
	 *
	 * @static
	 * @access	public
	 * @throws	Error
	 */
	public static function attend($event, $status, $session){
		CallerFactory::getDefaultCaller()->signedCall('event.attend', array(
			'event'  => $event,
			'status' => $status
		), $session, 'POST');
	}

	/** Get the metadata for an event on last.fm. Includes attendance and lineup information.
	 *
	 * @param	integer	$event	The numeric last.fm event ID. (Required)
	 * @return	mixed			An Event object.
	 *
	 * @static
	 * @access	public
	 * @throws	Error
	 */
	public static function getInfo($event){
		$xml = CallerFactory::getDefaultCaller()->call('event.getInfo', array(
			'event' => $event
		));

		return Event::fromSimpleXMLElement($xml);
	}

	/** Get shouts for this event.
	 *
	 * @param	integer	$event	The numeric last.fm event id (Required)
	 * @return	array			An array of Shout objects.
	 *
	 * @static
	 * @access	public
	 * @throws	Error
	 */
	public static function getShouts($event){
		$xml = CallerFactory::getDefaultCaller()->call('event.getShouts', array(
			'event' => $event
		));

		$shouts = array();

		foreach($xml->children() as $shout){
			$shouts[] = Shout::fromSimpleXMLElement($shout);
		}

		return $shouts;
	}

	/** Share an event with one or more last.fm users or other friends.
	 *
	 * @param	integer	$event		An event ID. (Required)
	 * @param	array	$recipients	An array of email addresses or last.fm usernames. Maximum is 10. (Required)
	 * @param	string	$message	An optional message to send with the recommendation. If not supplied a default message will be used. (Optional)
	 * @param	Session	$session	A session obtained by {@link de.felixbruns.lastfm.Auth#getSession Auth::getSession} or {@link de.felixbruns.lastfm.Auth#getMobileSession Auth::getMobileSession}. (Required)
	 *
	 * @static
	 * @access	public
	 * @throws	Error
	 */
	public static function share($event, array $recipients, $message = null, $session){
		CallerFactory::getDefaultCaller()->signedCall('event.share', array(
			'event'     => $event,
			'recipient' => implode(',', $recipients),
			'message'   => $message
		), $session, 'POST');
	}

	/** Get an event playlist for streaming.. INOFFICIAL.
	 *
	 * @param	integer	$event	An event ID. (Required)
	 * @return	mixed			A Playlist object.
	 *
	 * @static
	 * @access	public
	 * @throws	Error
	 */
	public static function getPlaylist($event){
		$xml = CallerFactory::getDefaultCaller()->call('event.getPlayerMenu', array(
			'event' => $event
		));

		return Playlist::fetch(Util::toString($xml->playlist->url), true, true);
	}

	/** Create a Event object from a SimpleXMLElement.
	 *
	 * @param	SimpleXMLElement	$xml	A SimpleXMLElement.
	 * @return	Event						A Event object.
	 *
	 * @static
	 * @access	public
	 * @internal
	 */
	public static function fromSimpleXMLElement(SimpleXMLElement $xml){
		$artists = array();
		$images  = array();

		if($xml->artists){
			foreach($xml->artists->artist as $artist){
				$artists[] = Util::toString($artist);
			}

			$artists['headliner'] = Util::toString($xml->artists->headliner);
		}

		if($xml->image){
			foreach($xml->image as $image){
				$images[Util::toImageType($image['size'])] =
					Util::toString($image);
			}
		}

		return new Event(
			Util::toInteger($xml->id),
			Util::toString($xml->title),
			$artists,
			($xml->venue)?Venue::fromSimpleXMLElement($xml->venue):null,
			Util::toTimestamp($xml->startDate),
			Util::toString($xml->description),
			$images,
			Util::toString($xml->url),
			Util::toInteger($xml->attendance),
			Util::toInteger($xml->reviews),
			Util::toString($xml->tag)
		);
	}
}


