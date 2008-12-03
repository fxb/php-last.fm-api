<?

class Tag {
	private $name;
	private $count;
	private $url;
	
	public function __construct($name, $count, $url){
		$this->name  = $name;
		$this->count = $count;
		$this->url   = $url;
	}
	
	public function getName(){
		return $this->name;
	}
	
	public function getCount(){
		return $this->count;
	}
	
	public function getUrl(){
		return $this->url;
	}
	
	public static function getSimilar($tag, $apiKey){
		$xml = Caller::getInstance()->call('tag.getSimilar', array(
			'tag'     => $tag,
			'api_key' => $apiKey
		));
		
		$tags = array();
		
		foreach($xml->children() as $tag){
			$tags[] = Tag::fromSimpleXMLElement($tag);
		}
		
		return $tags;
	}
	
	public static function getTopAlbums($tag, $apiKey){
		$xml = Caller::getInstance()->call('tag.getTopAlbums', array(
			'tag'     => $tag,
			'api_key' => $apiKey
		));
		
		$albums = array();
		
		foreach($xml->children() as $album){
			$albums[] = Album::fromSimpleXMLElement($album);
		}
		
		return $albums;
	}
	
	public static function getTopArtists($tag, $apiKey){
		$xml = Caller::getInstance()->call('tag.getTopArtists', array(
			'tag'     => $tag,
			'api_key' => $apiKey
		));
		
		$artists = array();
		
		foreach($xml->children() as $artist){
			$artists[] = Artist::fromSimpleXMLElement($artist);
		}
		
		return $artists;
	}
	
	public static function getTopTags($apiKey){
		$xml = Caller::getInstance()->call('tag.getTopTags', array(
			'api_key' => $apiKey
		));
		
		$tags = array();
		
		foreach($xml->children() as $tag){
			$tags[] = Tag::fromSimpleXMLElement($tag);
		}
		
		return $tags;
	}
	
	public static function getTopTracks($tag, $apiKey){
		$xml = Caller::getInstance()->call('tag.getTopTracks', array(
			'tag'     => $tag,
			'api_key' => $apiKey
		));
		
		$tracks = array();
		
		foreach($xml->children() as $track){
			$tracks[] = Track::fromSimpleXMLElement($track);
		}
		
		return $tracks;
	}
	
	public static function search($tag, $limit, $page, $apiKey){
		$xml = Caller::getInstance()->call('tag.search', array(
			'tag'     => $tag,
			'limit'   => $limit,
			'page'    => $page,
			'api_key' => $apiKey
		));
		
		$tags = array();
		
		foreach($xml->tagmatches->children() as $tag){
			$tags[] = Tag::fromSimpleXMLElement($tag);
		}
		
		return $tags;
	}
	
	public static function fromSimpleXMLElement(SimpleXMLElement $xml){
		return new Tag(
			Util::toString($xml->name),
			Util::toInteger($xml->count),
			Util::toString($xml->url)
		);
	}
}

?>
