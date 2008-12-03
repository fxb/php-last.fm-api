<?

/** Represents an event and provides different methods to query event information.
 *
 * @package	de.felixbruns.lastfm.api
 * @author  Felix Bruns <felixbruns@web.de>
 * @version	1.0
 */
class Event {
	/** The events id.
	 *
	 * @var integer
	 */
	private $id;
	
	/** The events title.
	 *
	 * @var string
	 */
	private $title;
	
	/** The events artists.
	 *
	 * @var array
	 */
	private $artists;
	
	/** The events venue.
	 *
	 * @var Venue
	 */
	private $venue;
	
	/** The events start date (unix timestamp).
	 *
	 * @var integer
	 */
	private $startDate;
	
	/** The events description.
	 *
	 * @var string
	 */
	private $description;
	
	/** An array of images of this event.
	 *
	 * @var array
	 */
	private $images;
	
	/** The events last.fm URL.
	 *
	 * @var string
	 */
	private $url;
	
	/** Number of users attending this event.
	 *
	 * @var integer
	 */
	private $attendance;
	
	/** Number of reviews of this event.
	 *
	 * @var integer
	 */
	private $reviews;
	
	/** Tag of this event (e.g. lastfm:event=12345).
	 *
	 * @var string
	 */
	private $tag;
	
	/** Possible attendance statuses.
	 *
	 * @var integer
	 */
	const ATTENDING       = 0;
	const MAYBE_ATTENDING = 1;
	const NOT_ATTENDING   = 2;
	
	/** Create an event object.
	 *
	 * @param integer	id			The events ID.
	 * @param string	title		The events title.
	 * @param array		artists		An array of Artist objects.
	 * @param Venue		venue		A Venue object.
	 * @param integer	startDate	A start date (unix timestamp).
	 * @param string	description	An event description.
	 * @param array		images		An array of cover art images of different sizes.
	 * @param string	url			A last.fm event URL.
	 * @param integer	attendance	Number of users attending this event.
	 * @param integer	reviews		Number of reviews of this event.
	 * @param string	tag			Tag of this event (e.g. lastfm:event=12345).
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
	 * @return	integer	The ID of this event.
	 */
	public function getId(){
		return $this->id;
	}
	
	/** Returns the title of this event.
	 * 
	 * @return	string	The title of this event.
	 */
	public function getTitle(){
		return $this->title;
	}
	
	/** Returns the artists of this event.
	 * 
	 * @return	array	An array of Artist objects.
	 */
	public function getArtists(){
		return $this->artists;
	}
	
	/** Returns the venue of this event.
	 * 
	 * @return	Venue	A Venue object.
	 */
	public function getVenue(){
		return $this->venue;
	}
	
	/** Returns the start date of this event.
	 * 
	 * @return	integer	A unix timestamp.
	 */
	public function getStartDate(){
		return $this->startDate;
	}
	
	/** Returns the start date of this event.
	 * 
	 * @return	integer	A unix timestamp.
	 */
	public function getDescription(){
		return $this->description;
	}
	
	/** Returns an image URL of the specified size.
	 * 
	 * @param	integer	size	Image size constant.
	 * @return	string			An image URL.
	 */
	public function getImage($size){
		return $this->images[$size];
	}
	
	/** Returns the last.fm URL of this event.
	 * 
	 * @return	string	A last.fm URL.
	 */
	public function getUrl(){
		return $this->url;
	}
	
	/** Returns number of users attending this event.
	 * 
	 * @return	integer	Number of users attending.
	 */
	public function getAttendance(){
		return $this->attendance;
	}
	
	/** Returns number of reviews of this event.
	 * 
	 * @return	integer	Number of reviews.
	 */
	public function getReviews(){
		return $this->reviews;
	}
	
	/** Returns the tag of this event.
	 * 
	 * @return	string	A tag.
	 */
	public function getTag(){
		return $this->tag;
	}
	
	/** Change attendance status for an event.
	 * 
	 * @param	integer	event		An event ID.
	 * @param	integer	status		An attendance status.
	 * @param	Session	session		A session obtained by {@link de.felixbruns.lastfm.Auth#getSession Auth::getSession} or {@link de.felixbruns.lastfm.Auth#getMobileSession Auth::getMobileSession}.
	 * @return	boolean				true on success or false on failure.
	 */
	public static function attend($event, $status, $session){
		$response = Caller::getInstance()->signedCall('event.attend', array(
			'event'  => $event,
			'status' => $status
		), $session, 'POST');
		
		return $response;
	}
	
	/** Get event info.
	 * 
	 * @param	integer	event	An event ID.
	 * @return	mixed			An Event object on success or false on failure.
	 */
	public static function getInfo($event){
		$xml = Caller::getInstance()->call('event.getInfo', array(
			'event' => $event
		));
		
		if($xml !== false){
			return Event::fromSimpleXMLElement($xml);
		}
		else{
			return false;
		}
	}
	
	/** Share an event.
	 * 
	 * @param	integer	event		An event ID.
	 * @param	array	recipients	An array of last.fm usernames or e-mail adresses (maximum: 10).
	 * @param	string	message		An optional message to send.
	 * @param	Session	session		A session obtained by {@link de.felixbruns.lastfm.Auth#getSession Auth::getSession} or {@link de.felixbruns.lastfm.Auth#getMobileSession Auth::getMobileSession}.
	 * @return	boolean				true on success or false on failure.
	 */
	public static function share($event, array $recipients, $message, $session){
		$response = Caller::getInstance()->signedCall('event.share', array(
			'event'     => $event,
			'recipient' => implode(',', $recipients),
			'message'   => $message
		), $session, 'POST');
		
		return $response;
	}
	
	/** Get event playlist. INOFFICIAL.
	 * 
	 * @param	integer	event	An event ID.
	 * @return	mixed			A Playlist object on success or false on failure.
	 */
	public static function getPlaylist($event){
		$xml = Caller::getInstance()->call('event.getPlayerMenu', array(
			'event' => $event
		));
		
		return Playlist::fetch(
			Util::toString($xml->playlist->url),
			true,
			true,
			$apiKey
		);
	}
	
	/** Create a Event object from a SimpleXMLElement.
	 * 
	 * @param	SimpleXMLElement	xml	A SimpleXMLElement.
	 * @return	Event					A Event object.
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

?>
