<?php
/**
 *
 */

/**
 * 
 */
class LessView extends View
{
    /**
     *
     */
    protected $_less = array();
    
    /**
     *
     */
    public function __construct($styles)
    {
        @header("Content-Type: text/css;charset=utf-8");
        $this->_less = $styles;
    }
    
    /**
     *
     */
    public function output()
    {
        $lc = new lessc();
        $rtn = $lc->parse($this->_less);
        
        return (IS_LIVE ? $this->minify($rtn) : $rtn);
    }
    
    /**
     * Minify the CSS (based on Joe Scylla's ccsmin class)
     * 
     * @see http://code.google.com/p/cssmin/
     */
    protected function minify($styles)
    {
        $output = preg_replace("/\/\*(.*?)\*\//s", "", $styles);
        $output = preg_replace("/\s\s+/", " ", $output);
        $output = preg_replace("/\s*({|}|\[|\]|=|~|\+|>|\||;|:|,)\s*/", "$1", $output);
        $output = str_replace(";}", "}", $output);
        return trim($output);
    }
}
