<?php
/**
 *
 */

/**
 *
 */
class SearchSelectControl extends SelectControl
{
    /**
     *
     */
    public function output()
    {
        if ($this->getOptions()) {
            
            $func = "get" . var2func($this->_var);
            $selected = $this->_obj->$func();
            
            $field = sprintf(
                
                "\n<span class=\"ssl_wrapper\">\n" .
                "    <input type=\"checkbox\" name=\"%s\" value=\"%s\" checked=\"checked\" />\n" .
                "    <span class=\"ssl_cite\">%s</span>\n" .
                "    (<a href=\"#\" class=\"ssl_change\">Change</a>)\n" .
                "    <span class=\"ssl_search\">\n" .
                "        <input type=\"text\" name=\"search__%s\" size=\"60\" />\n" .
                "        <a href=\"#\" class=\"ssl_searchbutton\">Search</a>\n" .
                "        <span class=\"ssl_loading\"></span>\n" .
                "        <span class=\"cb_list ssl_searchresult\"></span>" .
                "    </span>\n" .
                "</span>\n" .
                "<span class=\"clear\"></span>",
                
                $this->_prefix . $this->_var,
                ($selected ? $selected->uid() : "0"),
                ($selected ? $selected->cite() : "[Not Set]"),
                $this->_prefix . $this->_var
                
            );
            
        }
        
        return $this->getWrapper($field);
    }
}
