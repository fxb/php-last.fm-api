<?php

/** Represents some kind of media and provides common information.
 *
 * @package	php-lastfm-api
 * @author  Felix Bruns <felixbruns@web.de>
 * @version	1.0
 */
class Media {
	/** Name of this medium.
	 *
	 * @var string
	 * @access	private
	 */
	private $name;

	/** MusicBrainz ID of this medium.
	 *
	 * @var string
	 * @access	private
	 */
	private $mbid;

	/** Last.fm URL of this medium.
	 *
	 * @var string
	 * @access	private
	 */
	private $url;

	/** An array of images of this medium.
	 *
	 * @var array
	 * @access	private
	 */
	private $images;

	/** Number of listeners of this medium.
	 *
	 * @var integer
	 * @access	private
	 */
	private $listeners;

	/** Play count of this medium.
	 *
	 * @var integer
	 * @access	private
	 */
	private $playCount;

	/** Possible image sizes.
	 *
	 * @var integer
	 * @access	public
	 */
	const IMAGE_UNKNOWN    = -1;
	const IMAGE_SMALL      =  0;
	const IMAGE_MEDIUM     =  1;
	const IMAGE_LARGE      =  2;
	const IMAGE_HUGE       =  3;
	const IMAGE_EXTRALARGE =  4;
	const IMAGE_ORIGINAL   =  5;

	/** Create a media object.
	 *
	 * @param string	$name		Name for this medium.
	 * @param string	$mbid		MusicBrainz ID for this medium.
	 * @param string	$url		Last.fm URL for this medium.
	 * @param array		$images		An array of images of different sizes.
	 * @param integer	$listeners	Number of listeners for this medium.
	 * @param integer	$playCount	Play count of this medium.
	 *
	 * @access	public
	 */
	public function __construct($name, $mbid, $url, array $images, $listeners,
								$playCount){
		$this->name      = $name;
		$this->mbid      = $mbid;
		$this->url       = $url;
		$this->images    = $images;
		$this->listeners = $listeners;
		$this->playCount = $playCount;
	}

	/** Returns the name of this medium.
	 *
	 * @return	string	The mediums name.
	 * @access	public
	 */
	public function getName(){
		return $this->name;
	}

	/** Returns the MusicBrainz ID of this medium.
	 *
	 * @return	string	MusicBrainz ID.
	 * @access	public
	 */
	public function getMbid(){
		return $this->mbid;
	}

	/** Returns the last.fm URL of this medium.
	 *
	 * @return	string	Last.fm URL.
	 * @access	public
	 */
	public function getUrl(){
		return $this->url;
	}

	/** Returns an image URL of the specified size of this medium.
	 *
	 * @param	integer	$size	Image size constant. (Optional)
	 * @return	string			An image URL.
	 * @access	public
	 */
	public function getImage($size = null){
		if($size !== null and array_key_exists($size, $this->images)){
			return $this->images[$size];
		}
		else if ($size === null){
			for($size = Media::IMAGE_ORIGINAL; $size > Media::IMAGE_UNKNOWN; $size--){
				if(array_key_exists($size, $this->images)){
					return $this->images[$size];
				}
			}
		}

		return null;
	}

	/** Returns the number of listeners of this medium.
	 *
	 * @return	integer	Number of listeners.
	 * @access	public
	 */
	public function getListeners(){
		return $this->listeners;
	}

	/** Returns the play count of this medium.
	 *
	 * @return	integer	Play count.
	 * @access	public
	 */
	public function getPlayCount(){
		return $this->playCount;
	}
}


