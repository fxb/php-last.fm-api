<?

/** A cache policy.
 *
 * @package	php-lastfm-api
 * @author  Felix Bruns <felixbruns@web.de>
 * @version	1.0
 */
interface CachePolicy {
	public function getExpirationTime($params);
}

?>
