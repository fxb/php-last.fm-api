<?

/** A cache policy.
 *
 * @package	php-lastfm-api
 * @author  Felix Bruns <felixbruns@web.de>
 * @version	1.0
 */
final class DefaultCachePolicy implements CachePolicy {
	private static $MINUTE;
	private static $HOUR;
	private static $DAY;
	private static $WEEK;
	private static $MONTH;
	private static $YEAR;

	private $weeklyMethods;

	private $weeklyChartsExpiration;

	public function __construct(){
		$this->MINUTE  =                 60;
		$this->HOUR    = $this->MINUTE * 60;
		$this->DAY     = $this->HOUR   * 24;
		$this->WEEK    = $this->DAY    *  7;
		$this->MONTH   = $this->WEEK   *  4.34812141;
		$this->YEAR    = $this->MONTH  * 12;

		$this->weeklyMethods = array(
			'artist.getSimilar',
			'tag.getSimilar',
			'track.getSimilar',
			'artist.getTopAlbums',
			'artist.getTopTracks',
			'geo.getTopArtists',
			'geo.getTopTracks',
			'tag.getTopAlbums',
			'tag.getTopArtists',
			'tag.getTopTags',
			'tag.getTopTracks',
			'user.getTopAlbums',
			'user.getTopArtists',
			'user.getTopTags',
			'user.getTopTracks'
		);

		$this->weeklyChartsExpiration = $this->WEEK;
	}

	public function getExpirationTime($params){
		$method = $params['method'];

		if(stripos($method, 'Weekly') !== false && stripos($method, 'List') === false){
			if(in_array('to', $params) && in_array('from', $params)){
				return $this->YEAR;
			}
			else{
				return $this->weeklyChartsExpiration;
			}
		}

		return in_array($method, $this->weeklyMethods) ? $this->WEEK : -1;
	}

	public function getWeeklyChartsExpiration(){
		return $this->weeklyChartsExpiration;
	}

	public function setWeeklyChartsExpiration($expiration){
		$this->weeklyChartsExpiration = $expiration;
	}
}

?>
