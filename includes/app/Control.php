<?php
/**
 *
 */

/**
 *
 */
abstract class Control
{
    /**
     *
     */
    protected $_obj = NULL;
    
    /**
     *
     */
    protected $_var = "";
    
    /**
     *
     */
    protected $_heading = "";
    
    /**
     *
     */
    protected $_tip = "";
    
    /**
     *
     */
    public function __construct(Object $obj, $var, $heading=NULL, $tip=NULL)
    {
        $this->_obj = $obj;
        $this->_var = $var;
        $this->_heading = ($heading != NULL ? $heading : var2label($var));
        $this->_tip = ($tip != NULL ? $tip : "");
    }
    
    /**
     *
     */
    public function getWrapper($field="&nbsp;")
    {
        $rtn = sprintf("<p>\n    <label>%s%s</label>\n    %s\n</p>\n",
            htmlentities($this->_heading),
            ($this->_tip != NULL ? sprintf("<span class=\"tip\">%s</span>", $this->_tip) : ""),
            $field
        );
        
        return $rtn;
    }
}
