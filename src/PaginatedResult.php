<?php

/** Stores information of a result that spans across multiple pages.
 *
 * @package	php-lastfm-api
 * @author  Felix Bruns <felixbruns@web.de>
 * @version	1.0
 */
class PaginatedResult implements Iterator, Countable {
	/** The number of total results.
	 *
	 * @var integer
	 * @access	private
	 */
	private $totalResults;

	/** The index of the first element in this result.
	 *
	 * @var integer
	 * @access	private
	 */
	private $startIndex;

	/** The number of items per page.
	 *
	 * @var integer
	 * @access	private
	 */
	private $itemsPerPage;

	/** An array of results.
	 *
	 * @var array
	 * @access	private
	 */
	private $results;

	/** Create a PaginatedResult object.
	 *
	 * @param integer	$totalResults	Number of total results.
	 * @param integer	$startIndex		Index of the first result element.
	 * @param integer	$itemsPerPage	Number of items per page.
	 * @param array		$results		An array of results.
	 *
	 * @access	public
	 */
	public function __construct($totalResults, $startIndex, $itemsPerPage, $results){
		$this->totalResults = $totalResults;
		$this->startIndex   = $startIndex;
		$this->itemsPerPage = $itemsPerPage;
		$this->results      = $results;
	}

	/** Returns the number of total results.
	 *
	 * @return	integer	Number of total results.
	 * @access	public
	 */
	public function getTotalResults(){
		return $this->totalResults;
	}

	/** Returns the index of the first result element.
	 *
	 * @return	integer	Index of the first element.
	 * @access	public
	 */
	public function getStartIndex(){
		return $this->startIndex;
	}

	/** Returns the current page.
	 *
	 * @return	integer	Current page number.
	 * @access	public
	 */
	public function getCurrentPage(){
		return ($this->startIndex / $this->itemsPerPage) + 1;
	}

	/** Returns the number of items per page.
	 *
	 * @return	integer	Number of items per page.
	 * @access	public
	 */
	public function getItemsPerPage(){
		return $this->itemsPerPage;
	}

	/** Returns the number total pages.
	 *
	 * @return	integer Total pages.
	 * @access	public
	 */
	public function getPages(){
		return intval(round($this->totalResults / $this->itemsPerPage));
	}

	/** Returns the array of results.
	 *
	 * @return	array	An array of result elements.
	 * @access	public
	 */
	public function getResults(){
		$this->rewind();

		return $this->results;
	}

	public function rewind(){
		reset($this->results);
	}

	public function current(){
		return current($this->results);
	}

	public function key(){
		return key($this->results);
	}

	public function next(){
		return next($this->results);
	}

	public function valid(){
		return ($this->current() !== false);
	}

	public function count(){
		return count($this->results);
	}
}


