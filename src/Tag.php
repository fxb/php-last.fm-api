<?php

/** Represents a tag and provides different methods to query tag information.
 *
 * @package	php-lastfm-api
 * @author  Felix Bruns <felixbruns@web.de>
 * @version	1.0
 */
class Tag {
	/** The tags name.
	 *
	 * @var string
	 * @access	private
	 */
	private $name;

	/** The tags count.
	 *
	 * @var integer
	 * @access	private
	 */
	private $count;

	/** The tags last.fm URL.
	 *
	 * @var string
	 * @access	private
	 */
	private $url;

	/** Create a Tag object.
	 *
	 * @param string	$name	Tag name.
 	 * @param integer	$count	Tag count.
 	 * @param string	$url	Tag URL.
 	 *
	 * @access	public
	 */
	public function __construct($name, $count, $url){
		$this->name  = $name;
		$this->count = $count;
		$this->url   = $url;
	}

	/** Returns the tags name.
	 *
	 * @return	string	Tag name.
	 * @access	public
	 */
	public function getName(){
		return $this->name;
	}

	/** Returns the tags count.
	 *
	 * @return	integer	Tag count.
	 * @access	public
	 */
	public function getCount(){
		return $this->count;
	}

	/** Returns the tags last.fm URL.
	 *
	 * @return	string	Tag URL.
	 * @access	public
	 */
	public function getUrl(){
		return $this->url;
	}

	/** Search for tags similar to this one. Returns tags ranked by similarity, based on listening data.
	 *
	 * @param	string	$tag	The tag name in question. (Required)
	 * @return	array			An array of Tag objects.
	 *
	 * @static
	 * @access	public
	 * @throws	Error
	 */
	public static function getSimilar($tag){
		$xml = CallerFactory::getDefaultCaller()->call('tag.getSimilar', array(
			'tag' => $tag
		));

		$tags = array();

		foreach($xml->children() as $tag){
			$tags[] = Tag::fromSimpleXMLElement($tag);
		}

		return $tags;
	}

	/** Get the top albums tagged by this tag, ordered by tag count.
	 *
	 * @param	string	$tag	The tag name in question. (Required)
	 * @return	array			An array of Album objects.
	 *
	 * @static
	 * @access	public
	 * @throws	Error
	 */
	public static function getTopAlbums($tag){
		$xml = CallerFactory::getDefaultCaller()->call('tag.getTopAlbums', array(
			'tag' => $tag
		));

		$albums = array();

		foreach($xml->children() as $album){
			$albums[] = Album::fromSimpleXMLElement($album);
		}

		return $albums;
	}

	/** Get the top artists tagged by this tag, ordered by tag count.
	 *
	 * @param	string	$tag	The tag name in question. (Required)
	 * @return	array			An array of Artist objects.
	 *
	 * @static
	 * @access	public
	 * @throws	Error
	 */
	public static function getTopArtists($tag){
		$xml = CallerFactory::getDefaultCaller()->call('tag.getTopArtists', array(
			'tag' => $tag
		));

		$artists = array();

		foreach($xml->children() as $artist){
			$artists[] = Artist::fromSimpleXMLElement($artist);
		}

		return $artists;
	}

	/** Fetches the top global tags on last.fm, sorted by popularity (number of times used).
	 *
	 * @return	array	An array of Tag objects.
	 *
	 * @static
	 * @access	public
	 * @throws	Error
	 */
	public static function getTopTags(){
		$xml = CallerFactory::getDefaultCaller()->call('tag.getTopTags');

		$tags = array();

		foreach($xml->children() as $tag){
			$tags[] = Tag::fromSimpleXMLElement($tag);
		}

		return $tags;
	}

	/** Get the top tracks tagged by this tag, ordered by tag count.
	 *
	 * @param	string	$tag	The tag name in question. (Required)
	 * @return	array			An array of Track objects.
	 *
	 * @static
	 * @access	public
	 * @throws	Error
	 */
	public static function getTopTracks($tag){
		$xml = CallerFactory::getDefaultCaller()->call('tag.getTopTracks', array(
			'tag' => $tag
		));

		$tracks = array();

		foreach($xml->children() as $track){
			$tracks[] = Track::fromSimpleXMLElement($track);
		}

		return $tracks;
	}

	/** Get an artist chart for a tag, for a given date range. If no date range is supplied, it will return the most recent artist chart for this tag.
	 *
	 * @param	string	$tag	The tag name in question. (Required)
	 * @param	integer	$from	The date at which the chart should start from. See Tag.getWeeklyChartList for more. (Optional)
	 * @param	integer	$to		The date at which the chart should end on. See Tag.getWeeklyChartList for more. (Optional)
	 * @param	integer	$limit	The number of chart items to return (default = 50). (Optional)
	 * @return	array			An array of Artist objects.
	 *
	 * @static
	 * @access	public
	 * @throws	Error
	 */
	public static function getWeeklyArtistChart($tag, $from = null, $to = null,
												$limit = null){
		$xml = CallerFactory::getDefaultCaller()->call('tag.getWeeklyArtistChart', array(
			'tag'   => $tag,
			'from'  => $from,
			'to'    => $to,
			'limit' => $limit
		));

		$artists = array();

		foreach($xml->children() as $artist){
			$artists[] = Artist::fromSimpleXMLElement($artist);
		}

		return $artists;
	}

	/** Get a list of available charts for this group, expressed as date ranges which can be sent to the chart services.
	 *
	 * @param	string	$tag	The tag name in question. (Required)
	 * @return	array			An array of from/to unix timestamp pairs.
	 *
	 * @static
	 * @access	public
	 * @throws	Error
	 */
	public static function getWeeklyChartList($tag){
		$xml = CallerFactory::getDefaultCaller()->call('tag.getWeeklyChartList', array(
			'tag' => $tag
		));

		$chartList = array();

		foreach($xml->children() as $chart){
			$chartList[] = array(
				'from' => Util::toInteger($chart['from']),
				'to'   => Util::toInteger($chart['to']),
			);
		}

		return $chartList;
	}

	/** Search for a tag by name. Returns matches sorted by relevance.
	 *
	 * @param	string	$tag	The tag name in question. (Required)
	 * @param	integer	$limit	Limit the number of tags returned at one time. Default (maximum) is 30. (Optional)
	 * @param	integer	$page	Scan into the results by specifying a page number. Defaults to first page. (Optional)
	 * @return	array			An array of Tag objects.
	 *
	 * @static
	 * @access	public
	 * @throws	Error
	 */
	public static function search($tag, $limit = null, $page = null){
		$xml = CallerFactory::getDefaultCaller()->call('tag.search', array(
			'tag'   => $tag,
			'limit' => $limit,
			'page'  => $page
		));

		$tags = array();

		foreach($xml->tagmatches->children() as $tag){
			$tags[] = Tag::fromSimpleXMLElement($tag);
		}

		return $tags;
	}

	/** Create a Tag object from a SimpleXMLElement.
	 *
	 * @param	SimpleXMLElement	$xml	A SimpleXMLElement.
	 * @return	Tag							A Tag object.
	 *
	 * @static
	 * @access	public
	 * @internal
	 */
	public static function fromSimpleXMLElement(SimpleXMLElement $xml){
		return new Tag(
			Util::toString($xml->name),
			Util::toInteger($xml->count),
			Util::toString($xml->url)
		);
	}
}


