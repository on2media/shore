<?php
/**
 * Collection
 *
 * @package core
 */

/**
 * This class implements the iterator interface so that we can easily foreach() through the
 * data set.
 */
abstract class Collection implements Iterator
{
    /**
     * Stores the current data set.
     * 
     * @var  array
     */
    protected $_dataSet = array();
    
    /**
     * Stores the record set offset.
     * 
     * @var  int|null
     */
    protected $_start = NULL;
    
    /**
     * Stores the maximum number of records to return.
     * 
     * @var  int|null
     */
    protected $_range = NULL;
    
    /**
     * Stores the default sort order.
     *
     * @var  string
     */
    protected $_order = "";
    
    /**
     * Stores the total number of records in the record set.
     * 
     * @var  int
     */
    protected $_total = 0;
    
    /**
     * Constructor.
     *
     * @param  Object  $obj  Specify what model the collection is for.
     */
    public function __construct(Object $obj)
    {
        $this->_obj = $obj;
    }
    
    /**
     *
     */
    public function getObject()
    {
        return $this->_obj;
    }
    
    /**
     *
     */
    abstract public function setLimit($field, $condition, $value);
    
    /**
     * Defined the offset and maximum number of records to return.
     *
     * @param  int  $start  The offset.
     * @param  int  $range  The maximum number of records to return.
     * @return void
     */
    public function setPagination($start, $range)
    {
        $this->_start = (int)$start;
        $this->_range = (int)$range;
    }
    
    /**
     *
     */
    public function setPaginationPage($page=1, $itemsPerPage=20)
    {
        if ((int)$page > 0) {
            $this->_start = ((int)$page-1) * $itemsPerPage;
            $this->_range = $itemsPerPage;
            return TRUE;
        }
        
        return FALSE;
    }
    
    /**
     *
     */
    public function paginate($showSummary=TRUE, $url="?p=%d")
    {
        $rtn = "";
        
        if ($this->_start !== NULL && $this->_range !== NULL && $this->_total !== NULL) {
            
            $rtn .= $this->getPageLinks($this->_start, $this->_range, $this->_total, $url);
            
            if ($showSummary == TRUE) {
                $rtn .= sprintf("<p class=\"np\">\n    Showing %d - %d of %d records.\n</p>\n",
                    $this->_start + 1,
                    (($this->_start + $this->_range) > $this->_total ? $this->_total : ($this->_start + $this->_range)),
                    $this->_total
                );
            }
            
        }
        
        return $rtn;
    }
    
    /**
     *
     */
    public function getPageLinks($startAt=0, $itemsPerPage=0, $totalRecords=0, $url="?p=%d")
    {
        $a=6; $page = ($startAt / $itemsPerPage) + 1;
        $lastpage = ceil($totalRecords / $itemsPerPage);
        if ($page == 0 || $lastpage <= 1) return "";
       
        $rtn = "<ul class=\"pagination\">";
       
        if ($page > 1) $rtn .= "<li><a href=\"" . sprintf($url, ($page-1)) . "\">&lt;</a></li>";
        if ($lastpage < (8+$a)) {
            $rtn .= $this->paginateSection(1, $lastpage, $url, $page);
        } else {
            if ($page < (3+$a)) {
                $rtn .= $this->paginateSection(1, $i=(3+$a), $url, $page);
            } else {
                $rtn .= $this->paginateSection(1, 2, $url, $page);
                if ($page != (3+$a)) $rtn .= "<li><span>&hellip;</span></li>";
                if ($lastpage > $page+(1+$a)) {
                    $rtn .= $this->paginateSection($page-$a, $i=$page+$a, $url, $page);
                } else {
                    $rtn .= $this->paginateSection($lastpage-(3+$a), $i=$lastpage, $url, $page);
                }
            }
            if ($i<$lastpage) {
                if ($lastpage-2 != $i) $rtn .= "<li><span>&hellip;</span></li>";
                $rtn .= $this->paginateSection($lastpage-1, $lastpage, $url, $page);
            }
        }
        if ($page < $lastpage) $rtn .= "<li><a href=\"" . sprintf($url, ($page+1)) . "\">&gt;</a></li>";
       
        return $rtn . "</ul>";
    }
    
    /**
     *
     */
    protected function paginateSection($start, $end, $url, $currentPage)
    {
        $rtn = "";
       
        for ($i=$start; $i<=$end;$i++) {
            $rtn .= sprintf("<li%s><a href=\"%s\">%d</a></li>\n",
                ($currentPage == $i ? " class=\"active\"":""),
                sprintf($url, $i),
                $i
            );
        }
       
        return $rtn;
    }
    
    /**
     * Sets the sort order.
     *
     * @param  string  $order
     */
    public function setOrder($order)
    {
        $this->_order = $order;
    }
    
    /**
     * Fetches the record set and returns the first record.
     *
     * @return  object
     */
    public function fetchFirst()
    {
        $this->fetchAll();
        return reset($this->_dataSet);
    }
    
    /**
     * Should fetch and return the data set.
     */
    abstract public function fetchAll();
    
    /**
     *
     */
    public function add(Object $obj)
    {
        $this->_dataSet[] = $obj;
    }    
    
    /**
     * Returns the current record.
     *
     * @return  object
     */
    public function current()
    {
        return current($this->_dataSet);
    }
    
    /**
     * Returns the key of the current record.
     *
     * @return  scalar|null
     */
    public function key()
    {
        return key($this->_dataSet);
    }
    
    /**
     * Moves to the next record.
     *
     * @return  void
     */
    public function next()
    {
        next($this->_dataSet);
    }
    
    /**
     * Rewinds the iterator to the first record.
     *
     * @return  void
     */
    public function rewind()
    {
        reset($this->_dataSet);
    }
    
    /**
     * Checks if the current position is valid.
     *
     * @return  boolean
     */
    public function valid()
    {
        return ($this->current() !== FALSE);
    }
    
    /**
     * Returns the number of records in the data set. Smarty doesn't support using count($obj) on
     * iterators so this is a workaround for that.
     *
     * @return  int
     */
    public function count()
    {
        return count($this->_dataSet);
    }
}
