<?

class PaginatedResult {
	private $totalResults;
	private $startIndex;
	private $itemsPerPage;
	private $results;
	
	public function __construct($totalResults, $startIndex, $itemsPerPage, $results){
		$this->totalResults = $totalResults;
		$this->startIndex   = $startIndex;
		$this->itemsPerPage = $itemsPerPage;
		$this->results      = $results;
	}
	
	public function getTotalResults(){
		return $this->totalResults;
	}
	
	public function getStartIndex(){
		return $this->startIndex;
	}
	
	public function getStartPage(){
		return ($this->startIndex / $this->itemsPerPage) + 1;
	}
	
	public function getItemsPerPage(){
		return $this->itemsPerPage;
	}
	
	public function getPages(){
		return intval(round($this->totalResults / $this->itemsPerPage));
	}
	
	public function getResults(){
		return $this->results;
	}
}

?>
