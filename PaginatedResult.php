<?

/** Stores information of a result that spans across multiple pages.
 *
 * @package	de.felixbruns.lastfm.api
 * @author  Felix Bruns <felixbruns@web.de>
 * @version	1.0
 */
class PaginatedResult {
	/** The number of total results.
	 *
	 * @var integer
	 */
	private $totalResults;
	
	/** The index of the first element in this result.
	 *
	 * @var integer
	 */
	private $startIndex;
	
	/** The number of items per page.
	 *
	 * @var integer
	 */
	private $itemsPerPage;
	
	/** An array of results.
	 *
	 * @var array
	 */
	private $results;
	
	/** Create a PaginatedResult object.
	 *
	 * @param integer	totalResults	Number of total results.
	 * @param integer	startIndex		Index of the first result element.
	 * @param integer	itemsPerPage	Number of items per page.
	 * @param array		results			An array of results.
	 */
	public function __construct($totalResults, $startIndex, $itemsPerPage, $results){
		$this->totalResults = $totalResults;
		$this->startIndex   = $startIndex;
		$this->itemsPerPage = $itemsPerPage;
		$this->results      = $results;
	}
	
	/** Returns the number of total results.
	 * 
	 * @return	integer	An integer.
	 */
	public function getTotalResults(){
		return $this->totalResults;
	}
	
	/** Returns the index of the first result element.
	 * 
	 * @return	integer	An integer.
	 */
	public function getStartIndex(){
		return $this->startIndex;
	}
	
	/** Returns the current page.
	 * 
	 * @return	integer	An integer.
	 */
	public function getCurrentPage(){
		return ($this->startIndex / $this->itemsPerPage) + 1;
	}
	
	/** Returns the number of items per page.
	 * 
	 * @return	integer	An integer.
	 */
	public function getItemsPerPage(){
		return $this->itemsPerPage;
	}
	
	/** Returns the number total pages.
	 * 
	 * @return	integer	An integer.
	 */
	public function getPages(){
		return intval(round($this->totalResults / $this->itemsPerPage));
	}
	
	/** Returns the array of results.
	 * 
	 * @return	array	An array of result elements.
	 */
	public function getResults(){
		return $this->results;
	}
}

?>
