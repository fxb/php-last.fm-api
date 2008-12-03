<?

/** Provides utility methods to convert variables to a specified type.
 *
 * @package	de.felixbruns.lastfm.api
 * @author  Felix Bruns <felixbruns@web.de>
 * @version	1.0
 */
class Util {
	/** Returns the string value of a variable.
	 * 
	 * @return	string	A string.
	 */
	public static function toString($var){
		return ($var && trim($var))?strval($var):'';
	}
	
	/** Returns the integer value of a variable.
	 * 
	 * @return	integer	An integer.
	 */
	public static function toInteger($var){
		return ($var && trim($var))?intval($var):0;
	}
	
	/** Returns the floating-point value of a variable.
	 * 
	 * @return	float	A floating-point number.
	 */
	public static function toFloat($var){
		return ($var && trim($var))?floatval($var):0.0;
	}
	
	/** Returns the boolean value of a variable.
	 * 
	 * @return	boolean	A boolean.
	 */
	public static function toBoolean($var){
		switch(Util::toString($var)){
			case 'true':
					return true;
			case 'false':
					return false;
			default:
				return !!intval($var);
		}
	}
	
	/** Returns the unix timestamp value of a variable.
	 * 
	 * @return	integer	A unix timestamp.
	 */
	public static function toTimestamp($var){
		return ($var && trim($var))?strtotime(strval($var)):0;
	}
	
	/** Returns the image type value of a variable.
	 * 
	 * @return	integer	An image type.
	 */
	public static function toImageType($var){
		switch(Util::toString($var)){
			case 'small':
					return Media::IMAGE_SMALL;
			case 'medium':
					return Media::IMAGE_MEDIUM;
			case 'large':
					return Media::IMAGE_LARGE;
			case 'hude':
					return Media::IMAGE_HUGE;
			case 'extralarge':
					return Media::IMAGE_EXTRALARGE;
			case 'original':
					return Media::IMAGE_ORIGINAL;
			default:
				return Media::IMAGE_UNKNOWN;
		}
	}
	
	/** Converts any string or array of strings to UTF8.
	 * 
	 * @param	mixed	object	String or array.
	 * @return	mixed			UTF8-string or array.
	 */
	public static function toUTF8($object){
		if(is_array($object)){
			return array_map(array('Util', 'toUTF8'), $object);
		}
		
		return mb_convert_encoding($object, "UTF-8", "auto");
	}
}

?>
