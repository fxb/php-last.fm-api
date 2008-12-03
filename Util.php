<?

class Util {
	public static function toString($var){
		return ($var && trim($var))?strval($var):'';
	}
	
	public static function toInteger($var){
		return ($var && trim($var))?intval($var):0;
	}
	
	public static function toFloat($var){
		return ($var && trim($var))?floatval($var):0.0;
	}
	
	public static function toTimestamp($var){
		return ($var && trim($var))?strtotime(strval($var)):0;
	}
	
	public static function toImageType($var){
		switch(strval($var)){
			case 'small':
					return Media::IMAGE_SMALL;
				break;
			case 'medium':
					return Media::IMAGE_MEDIUM;
				break;
			case 'large':
					return Media::IMAGE_LARGE;
				break;
			default:
				return Media::IMAGE_UNKNOWN;
				break;
		}
	}
}

?>
