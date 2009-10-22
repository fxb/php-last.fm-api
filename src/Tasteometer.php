<?php

/** Provides a method for comparing music tastes.
 *
 * @package	php-lastfm-api
 * @author  Felix Bruns <felixbruns@web.de>
 * @version	1.0
 */
class Tasteometer {
	/** Possible comparison types.
	 *
	 * @var integer
	 * @access	public
	 */
	const COMPARE_USER    = 'user';
	const COMPARE_ARTISTS = 'artists';
	const COMPARE_MYSPACE = 'myspace';

	/** Get a Tasteometer score from two inputs, along with a list of shared artists. If the input is a User or a Myspace URL, some additional information is returned.
	 *
	 * @param	integer	$type1	A Tasteometer comparison type. (Required)
	 * @param	integer	$type2	A Tasteometer comparison type. (Required)
	 * @param	mixed	$value1	A last.fm username, an array of artist names or a myspace profile URL. (Required)
	 * @param	mixed	$value2	A last.fm username, an array of artist names or a myspace profile URL. (Required)
	 * @param	integer	$limit	How many shared artists to display (default = 5). (Optional)
	 * @return	array			An array containing comparison results, input information and shared artists.
	 *
	 * @static
	 * @access	public
	 * @throws	Error
	 */
	public static function compare($type1, $type2, $value1, $value2, $limit = null){
		/* Handle arrays of artist names. */
		if(is_array($value1)){
			$value1 = implode(',', $value1);
		}

		if(is_array($value2)){
			$value2 = implode(',', $value2);
		}

		/* API call. */
		$xml = CallerFactory::getDefaultCaller()->call('tasteometer.compare', array(
			'type1'  => $type1,
			'type2'  => $type2,
			'value1' => $value1,
			'value2' => $value2,
			'limit'  => $limit
		));

		/* Get shared artists. */
		$artists = array();

		foreach($xml->result->artists->children() as $artist){
			$artists[] = Artist::fromSimpleXMLElement($artist);
		}

		/* Get input information. */
		$inputs = array();

		foreach($xml->input->children() as $input){
			$inputs[] = User::fromSimpleXMLElement($input);
		}

		return array(
			'score'   => Util::toFloat($xml->result->score),
			'input'   => $inputs,
			'artists' => $artists
		);
	}
}


