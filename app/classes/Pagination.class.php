<?php

class Pagination {
	private $_pagination_data;

	private $_count;
	private $_current;
	private $_per_page;
	private $_jump;
	private $_previous_pages_count;
	private $_next_pages_count;
	private $_first_pages_count;
	private $_last_pages_count;
	private $_link_template;

	public function __construct(
							$count, // Number of items
							$current, // Current page
							$per_page = 10, // Items per page
							$link_template = '{number}', // Template for links
							$jump = 10, // How far to jump forward/back. 0 to leave null
							$previous_pages_count = 5, // Number of pages to show previous to current
							$next_pages_count = 5, // Number of pages to show after current
							$first_pages_count = 5, // Number of pages to show first
							$last_pages_count = 5, // Number of pages to show last
							$span = TRUE /* when current page is within first+previous or last+next of the first/last page
											the first/previous or last/next will be added together and used
											as next or previous.
											Example:
											Current page: 18
											Total pages: 20
											Span: TRUE
											1 2 3 4 5 ... 10 11 12 13 14 15 16 17 _18_ 19 20

											Span: FALSE
											1 2 3 4 5 ... 13 14 15 16 17 _18_ 19 20
											*/
							) {
		$this->_count = $count;
		$this->_current = $current;
		$this->_per_page = $per_page;
		$this->_jump = $jump;
		$this->_previous_pages_count = $previous_pages_count;
		$this->_next_pages_count = $next_pages_count;
		$this->_first_pages_count = $first_pages_count;
		$this->_last_pages_count = $last_pages_count;
		$this->_span = $span;
		$this->_link_template = $link_template;
	} // __construct()

	public function Paginate() {
		$data = new PaginationData();

		// variables to store page arrays
		$previous_pages = NULL;
		$first_pages = NULL;
		$last_pages = NULL;
		$next_pages = NULL;

		$pages = ceil($this->_count / $this->_per_page);

		// temporary variables used for first/next and previous/last pages merging logic below
		$next_pages_count = $this->_next_pages_count;
		$previous_pages_count = $this->_previous_pages_count;

		// Span previous pages with next pages if actual previous pages are less than specified display previous pages
		if ( $this->_span && $previous_pages_count && $this->_current <= $this->_previous_pages_count + 1 ) {
			if ( $previous_pages_count >= ($this->_current - $this->_first_pages_count) ) {
				$next_pages_count += $previous_pages_count - ($this->_current - 1);
			}
		}

		// Same as above with next pages
		if ( $this->_span && $next_pages_count && $pages - $this->_current <= $this->_next_pages_count + 1 ) {
			if ( $next_pages_count > $pages - $this->_current - $this->_last_pages_count ) {
				$previous_pages_count += $next_pages_count - ($pages - $this->_current);
			}
		}

		$data->Current = new PaginationLink($this->_current);

		if ( $this->_jump ) $data->Jump = $this->_jump;

		if ( $pages > 1 && $this->_current > 1 ) {
			$data->First = new PaginationLink(1);
			$data->Previous = new PaginationLink($this->_current - 1);
		}
	
		if ( $pages > $this->_current ) {
			$data->Next = new PaginationLink($this->_current + 1);
			$data->Last = new PaginationLink($pages);
		}

		if ( $this->_jump && $pages > $this->_jump && $this->_current > $this->_jump ) {
			$data->JumpPrevious = new PaginationLink($this->_current - $this->_jump);
		}
	
		if ( $this->_jump && $this->_current <= $pages - $this->_jump ) {
			$data->JumpNext = new PaginationLink($this->_current + $this->_jump);
		}

		if ( $this->_first_pages_count && $this->_current > 1 ) {
			if ( $this->_first_pages_count >= $this->_current ) {
				$first_pages = range(1, $this->_current - 1);
			} else {
				$first_pages = range(1, $this->_first_pages_count);
			}
		}

		if ( $previous_pages_count && $this->_current > $this->_first_pages_count + 1 ) {
			if ( $previous_pages_count >= ($this->_current - $this->_first_pages_count) ) {
				$previous_pages = range($this->_current - ($this->_current - $this->_first_pages_count) + 1, $this->_current - 1);
			} else {
				$previous_pages = range($this->_current - $previous_pages_count, $this->_current - 1);
			}
		}

		/* If the first pages and previous pages are within one of eachother
			(I.E. 1, 2, 3 and 4, 5, 6) then merge them all into first (1, 2, 3, 4, 5, 6)
			*/

		if ( $first_pages && $previous_pages && ($first_pages[count($first_pages) - 1] == $previous_pages[0] - 1 || $first_pages[count($first_pages) - 1] + 1 == $previous_pages[0] - 1) ) {
			$first_pages = range($first_pages[0], $previous_pages[count($previous_pages) - 1]);
			$previous_pages = NULL;
		}

		if ( $next_pages_count && $this->_current < $pages - $this->_last_pages_count ) {
			if ( $next_pages_count > $pages - $this->_current - $this->_last_pages_count ) {
				$next_pages = range($this->_current + 1, $this->_current + ($pages - $this->_current - $this->_last_pages_count));
			} else {
				$next_pages = range($this->_current + 1, $this->_current + $next_pages_count);
			}
		}

		if ( $this->_last_pages_count && $this->_current < $pages ) {
			if ( $this->_last_pages_count > $pages - $this->_current ) {
				$last_pages = range($pages - ($pages - $this->_current) + 1, $pages);
			} else {
				$last_pages = range($pages - $this->_last_pages_count + 1, $pages);
			}
		}

		/* If the last pages and next pages are within one of eachother
			(I.E. 4, 5, 6 and 7, 8, 9) then merge them all into first (4, 5, 6, 7, 8, 9)
			*/
		if ( $next_pages && $last_pages && ($next_pages[count($next_pages) - 1] == $last_pages[0] - 1 || $next_pages[count($next_pages) - 1] + 1 == $last_pages[0] - 1) ) {
			$last_pages = range($next_pages[0], $last_pages[count($last_pages) - 1]);
			$next_pages = null;
		}

		// Setup link objects
		if ( $previous_pages ) {
			foreach ( $previous_pages as $previous_page ) {
				$data->PreviousPages[] = new PaginationLink($previous_page);
			}
		}

		if ( $first_pages ) {
			foreach ( $first_pages as $first_page ) {
				$data->FirstPages[] = new PaginationLink($first_page);
			}
		}

		if ( $next_pages ) {
			foreach ( $next_pages as $next_page ) {
				$data->NextPages[] = new PaginationLink($next_page);
			}
		}

		if ( $last_pages ) {
			foreach ( $last_pages as $last_page ) {
				$data->LastPages[] = new PaginationLink($last_page);
			}
		}

		if ( $this->_link_template ) $this->ApplyPageLink($data);

		$this->_pagination_data = $data;

		return $this->_pagination_data;
	} // Paginate()

	/** dig through objects to find PaginationLink objects and set their Link
		property based on their Number property. Recursion.
		ex. $link_template http://example.com/?page={number}
		*/
	public function ApplyPageLink(&$link_object, $link_template = '') {
		if ( !$link_template ) $link_template = $this->_link_template;

		if ( get_class($link_object) == 'PaginationLink' ) {
			$link_object->Link = str_replace("{number}", $link_object->Number, $link_template);
		} elseif ( get_class($link_object) == 'PaginationData' ) {
			$this->ApplyPageLink($link_object->First, $link_template);
			$this->ApplyPageLink($link_object->JumpPrevious, $link_template);
			$this->ApplyPageLink($link_object->Previous, $link_template);
			$this->ApplyPageLink($link_object->Current, $link_template);
			$this->ApplyPageLink($link_object->Next, $link_template);
			$this->ApplyPageLink($link_object->JumpNext, $link_template);
			$this->ApplyPageLink($link_object->Last, $link_template);
	
			$this->ApplyPageLink($link_object->FirstPages, $link_template);
			$this->ApplyPageLink($link_object->PreviousPages, $link_template);
			$this->ApplyPageLink($link_object->NextPages, $link_template);
			$this->ApplyPageLink($link_object->LastPages, $link_template);
		} elseif ( is_array($link_object) ) {
			foreach ( $link_object as $link_sub_object ) {
				$this->ApplyPageLink($link_sub_object);
			}
		}
	}

	// Getter/Setter functions
	public function GetPaginationData() { return $this->_pagination_data; }

	public function GetCount() { return $this->_count; }
	public function SetCount($val) { $this->_count = $val; }

	public function GetCurrent() { return $this->_current; }
	public function SetCurrent($val) { $this->_current = $val; }

	public function GetPerPage() { return $this->_per_page; }
	public function SetPerPage($val) { $this->_per_page = $val; }

	public function GetJump() { return $this->_jump; }
	public function SetJump($val) { $this->_jump = $val; }

	public function GetPreviousPages() { return $this->_previous_pages; }
	public function SetPreviousPages($val) { $this->_previous_pages = $val; }

	public function GetNextPages() { return $this->_next_pages; }
	public function SetNextPages($val) { $this->_next_pages = $val; }

	public function GetFirstPages() { return $this->_first_pages; }
	public function SetFirstPages($val) { $this->_first_pages = $val; }

	public function GetLastPages() { return $this->_last_pages; }
	public function SetLastPages($val) { $this->_last_pages = $val; }

} // Pagination{}

class PaginationData {
	public $Jump; // how many pages skipped when using the "jump" links
	public $First; // number of the first page (usually 1)
	public $JumpPrevious; // page number to jump to when jumping back
	public $Previous; // previous page number
	public $Current; // current page number
	public $Next; // next page number
	public $JumpNext; // page number to jump to when jumping forward
	public $Last; // last page number
	public $FirstPages = array(); // page numbers put at the beginning
	public $PreviousPages = array(); // page numbers to display before current page
	public $NextPages = array(); // page numbers to display after current page
	public $LastPages = array(); // page numbers to put at the end
} // PaginationData{}

class PaginationLink {
	public $Number;
	public $Link;

	public function __construct($number = NULL, $link = NULL) {
		$this->Number = $number;
		$this->Link = $link;
	}
}

?>