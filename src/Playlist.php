<?php

/** Represents a playlist and provides different methods to query playlist information.
 *
 * @package php-lastfm-api
 * @author  Felix Bruns <felixbruns@web.de>
 * @version 1.0
 */
class Playlist {
  /** Playlist ID.
   *
   * @var integer
   * @access  private
   */
  private $id;

  /** Playlist title.
   *
   * @var string
   * @access  private
   */
  private $title;

  /** Playlist annotation.
   *
   * @var string
   * @access  private
   */
  private $description;
  
  /** Playlist date (unix timestamp).
   *
   * @var integer
   * @access  private
   */
  private $date;
  
  /** Playlist size.
   *
   * @var integer
   * @access  private
   */
  private $size;
  
  /** Playlist duration.
   *
   * @var integer
   * @access  private
   */
  private $duration;
  
  /** Playlist is streamable.
   *
   * @var boolean
   * @access  private
   */
  private $streamable;

  /** Playlist creator.
   *
   * @var string
   * @access  private
   */
  private $creator;

  /** Playlist size.
   *
   * @var integer
   * @access  private
   */
  private $url;
  
  /** Playlist size.
   *
   * @var array of Track
   * @access  private
   */
  private $tracks = array();

  /** Create a playlist object.
   *
   * @param integer $id     Playlist id.
   * @param string  $title    Playlist tile.
   * @param string  $description  Playlist description.
   * @param integer $date   Playlist date (unix timestamp).
   * @param integer $size   Playlist size.
   * @param integer $duration   Playlist duration.
   * @param boolean $streamable Playlist is streamable.
   * @param string  $creator  Playlist creator.
   * @param string  $url  Playlist url.
   * @param array   $track    An array of Track objects.
   * @access  public
   */
  public function __construct($id, $title, $description, $date, 
    $size, $duration, $streamable, $creator, $url, array $tracks){
      
    $this->id         = $id;
    $this->title      = $title;
    $this->description = $description;
    $this->date       = $date;
    $this->size       = $size;
    $this->duration   = $duration;
    $this->streamable = $streamable;
    $this->creator    = $creator;
    $this->url        = $url;
    $this->tracks     = $tracks;
  }

  /** Returns the ID of this playlist.
   *
   * @return  integer A playlist id.
   * @access  public
   */
  public function getId(){
    return $this->id;
  }

  /** Returns the title of this playlist.
   *
   * @return  string  A playlist title.
   * @access  public
   */
  public function getTitle(){
    return $this->title;
  }

  /** Returns the annotation of this playlist.
   *
   * @return  string  A playlist annotation.
   * @access  public
   */
  public function getDescription(){
    return $this->description;
  }
  
 /** Returns the date of this playlist.
   *
   * @return  integer A playlist date (unix timestamp).
   * @access  public
   */
  public function getDate(){
    return $this->date;
  }
  
  /** Returns the size of this playlist.
   *
   * @return  integer A playlist size.
   * @access  public
   */
  public function getSize(){
    return $this->size;
  }
  
  /** Returns the duration of this playlist.
   *
   * @return  integer A playlist duration.
   * @access  public
   */
  public function getDuration(){
    return $this->duration;
  }
  
  /** Returns if the playlist is streamable.
   *
   * @return  boolean Playlist is streamable.
   * @access  public
   */
  public function isStreamable(){
    return $this->streamable;
  }

  /** Returns the creator of this playlist.
   *
   * @return  string  A playlist creator.
   * @access  public
   */
  public function getCreator(){
    return $this->creator;
  }
  
  /** Returns the url of this playlist.
   *
   * @return  string  A playlist url.
   * @access  public
   */
  public function getUrl(){
    return $this->url;
  }
  
  /** Returns an array of tracks of this playlist.
   *
   * @return  array An array of Track objects.
   * @access  public
   */
  public function getTracks(){
    return $this->tracks;
  }

  /** Returns a track of this playlist.
   *
   * @param integer $index  Track index.
   * @return  Track     A Track object.
   * @access  public
   */
  public function getTrack($index){
    return $this->tracks[$index];
  }

  /** Add a track to a last.fm user's playlist.
   *
   * @param string  $id     The ID of the playlist - this is available in user.getPlaylists. (Required)
   * @param string  $artist   The artist name that corresponds to the track to be added. (Required)
   * @param string  $track    The track name to add to the playlist. (Required)
   * @param Session $session  A session obtained by {@link de.felixbruns.lastfm.Auth#getSession Auth::getSession} or {@link de.felixbruns.lastfm.Auth#getMobileSession Auth::getMobileSession}. (Required)
   *
   * @static
   * @access  public
   * @throws  Error
   */
  public static function addTrack($id, $artist, $track, $session){
    CallerFactory::getDefaultCaller()->signedCall('playlist.addTrack', array(
      'playlistID' => $id,
      'artist'     => $artist,
      'track'      => $track
    ), $session, 'POST');
  }

  /** Create a last.fm playlist on behalf of a user.
   *
   * @param string  $title      Title for the playlist. (Optional)
   * @param string  $description  Description for the playlist. (Optional)
   * @param Session $session    A session obtained by {@link de.felixbruns.lastfm.Auth#getSession Auth::getSession} or {@link de.felixbruns.lastfm.Auth#getMobileSession Auth::getMobileSession}. (Required)
   * @return  Playlist      A Playlist object.
   *
   * @static
   * @access  public
   * @throws  Error
   */
  public static function create($title = null, $description = null, $session){
    $xml = CallerFactory::getDefaultCaller()->signedCall('playlist.create', array(
      'title'       => $title,
      'description' => $description
    ), $session , 'POST');

    return Playlist::fromSimpleXMLElement($xml);
  }

  /** Fetch XSPF playlists using a last.fm playlist url.
   *
   * @param string  $playlist A lastfm protocol playlist url ('lastfm://playlist/...'). (Required)
   * @param string  $streaming  Weather to fetch a playlist for streaming. (Optional)
   * @param string  $fod    Weather to fetch a playlist with free on demand tracks. (Optional)
   * @param Session $session  A session obtained by {@link de.felixbruns.lastfm.Auth#getSession Auth::getSession} or {@link de.felixbruns.lastfm.Auth#getMobileSession Auth::getMobileSession}. (Optional)
   * @return  Playlist      A Playlist object.
   *
   * @static
   * @access  public
   * @throws  Error
   */
  public static function fetch($playlist, $streaming = null, $fod = null, $session = null){
    if($session == null){
      $xml = CallerFactory::getDefaultCaller()->call('playlist.fetch', array_filter(array(
        'playlistURL' => $playlist,
        'streaming'   => $streaming,
        'fod'         => $fod
      )));
    }
    else{
      $xml = CallerFactory::getDefaultCaller()->call('playlist.fetch', array_filter(array(
        'playlistURL' => $playlist,
        'streaming'   => $streaming,
        'fod'         => $fod,
        'sk'          => $session->getKey()
      )));
    }

    return Playlist::fromSimpleXMLElement($xml);
  }

  /** Create a Playlist object from a SimpleXMLElement.
   *
   * @param SimpleXMLElement  $xml  A SimpleXMLElement object.
   * @return  Playlist          A Playlist object.
   *
   * @static
   * @access  public
   * @internal
   */
  public static function fromSimpleXMLElement(SimpleXMLElement $xml){
    $tracks = array();
    if (isset($xml->trackList)) {
      foreach($xml->trackList->children() as $track){
        $tracks[] = Track::fromSimpleXMLElement($track);
      }
    }
    
    return new Playlist(
      Util::toInteger($xml->id),
      Util::toString($xml->title),
      Util::toString($xml->description),
      Util::toTimestamp($xml->date),
      Util::toInteger($xml->size),
      Util::toInteger($xml->duration),
      Util::toInteger($xml->streamable),
      Util::toString($xml->creator),
      Util::toString($xml->url),
      $tracks
    );
  }
}