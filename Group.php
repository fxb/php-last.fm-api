<?

/** Provides different methods to query group information.
 *
 * @package	de.felixbruns.lastfm.api
 * @author  Felix Bruns <felixbruns@web.de>
 * @version	1.0
 */
class Group {	
	/** Get an album chart for a group, for a given date range. If no date range is supplied, it will return the most recent album chart for this group.
	 * 
	 * @param	string	group	The last.fm group name to fetch the charts of.
 	 * @param	string	from	The date at which the chart should start from. See {@link de.felixbruns.lastfm.Group#getWeeklyChartList Group::getWeeklyChartList} for more.
	 * @param	string	to		The date at which the chart should end on. See {@link de.felixbruns.lastfm.Group#getWeeklyChartList Group::getWeeklyChartList} for more.
	 * @return	array			An array of Album objects.
	 */
	public static function getWeeklyAlbumChart($group, $from = null, $to = null){
		$xml = Caller::getInstance()->call('group.getWeeklyAlbumChart', array(
			'group' => $group,
			'from'  => $from,
			'to'    => $to
		));
		
		$albums = array();
		
		foreach($xml->children() as $album){
			$albums[] = Album::fromSimpleXMLElement($album);
		}
		
		return $albums;
	}
	
	/** Get an artist chart for a group, for a given date range. If no date range is supplied, it will return the most recent album chart for this group.
	 * 
	 * @param	string	group	The last.fm group name to fetch the charts of.
 	 * @param	string	from	The date at which the chart should start from. See {@link de.felixbruns.lastfm.Group#getWeeklyChartList Group::getWeeklyChartList} for more.
	 * @param	string	to		The date at which the chart should end on. See {@link de.felixbruns.lastfm.Group#getWeeklyChartList Group::getWeeklyChartList} for more.
	 * @return	array			An array of Artist objects.
	 */
	public static function getWeeklyArtistChart($group, $from = null, $to = null){
		$xml = Caller::getInstance()->call('group.getWeeklyArtistChart', array(
			'group' => $group,
			'from'  => $from,
			'to'    => $to
		));
		
		$artists = array();
		
		foreach($xml->children() as $artist){
			$artists[] = Artist::fromSimpleXMLElement($artist);
		}
		
		return $artists;
	}
	
	/** Get a list of available charts for this group, expressed as date ranges which can be sent to the chart services.
	 * 
	 * @param	string	group	The last.fm group name to fetch the charts list for.
	 * @return	array			An array of from/to unix timestamp pairs.
	 */
	public static function getWeeklyChartList($group){
		$xml = Caller::getInstance()->call('group.getWeeklyChartList', array(
			'group' => $group
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
	
	/** Get a track chart for a group, for a given date range. If no date range is supplied, it will return the most recent album chart for this group.
	 * 
	 * @param	string	group	The last.fm group name to fetch the charts of.
 	 * @param	string	from	The date at which the chart should start from. See {@link de.felixbruns.lastfm.Group#getWeeklyChartList Group::getWeeklyChartList} for more.
	 * @param	string	to		The date at which the chart should end on. See {@link de.felixbruns.lastfm.Group#getWeeklyChartList Group::getWeeklyChartList} for more.
	 * @return	array			An array of Track objects.
	 */
	public static function getWeeklyTrackChart($group, $from = null, $to = null){
		$xml = Caller::getInstance()->call('group.getWeeklyTrackChart', array(
			'group' => $group,
			'from'  => $from,
			'to'    => $to
		));
		
		$tracks = array();
		
		foreach($xml->children() as $track){
			$tracks[] = Track::fromSimpleXMLElement($track);
		}
		
		return $tracks;
	}
}

?>
