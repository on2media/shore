<?php
/**
 * Controller
 *
 * @package core
 */

/**
 * The abstract controller class. 
 */
abstract class Controller
{
    /**
     * Stores the defined View object.
     * 
     * @var  View|null
     */
    protected $_view = NULL;
    
    /**
     *
     */
    protected $_components = array();
    
    /**
     * Sets the view to use.
     *
     * @param  View  $view
     * @return  void
     */
    public function setView(View $view)
    {
        $this->_view = $view;
    }
    
    /**
     * Returns the view.
     *
     * @return  View
     */
    public function getView()
    {
        return $this->_view;
    }
    
    /**
     * Uses the view to return the requested output.
     *
     * @return  string  The returned data can be in any format (e.g. HTML, XML, JSON etc.)
     */
    public function output()
    {
        return $this->_view->output();
    }
    
    /**
     * Sends a redirect header and terminates the script.
     *
     * @param  string  $url  The URL to redirect to. By default this is the current page which can
     *                       be used when processing forms so that refreshing the page doesn't
     *                       resubmit the form.
     * @param  integer  $status  The HTTP status to send. If nothing is set 301 is used for a
     *                           permanent redirect. 302, 303 and 307 are also accepted. If an
     *                           invalid status is passed (i.e. not 301, 302, 303 or 307) a status
     *                           of 302 is used.
     */
    public static function redirect($url=NULL, $status=301)
    {
        if ($url == NULL) $url = _BASE . _PAGE;
        
        switch ($status) {
            case 301 : $status = "301 Moved Permanently"; break;
            case 303 : $status = "303 See Other"; break;
            case 307 : $status = "307 Temporary Redirect"; break;
            default  : $status = "302 Found"; break;
        }
        
        @header($_SERVER["SERVER_PROTOCOL"] . " $status");
        @header("Location: " . $url);
        
        exit();
    }
    
    /**
     *
     */
    public function useComponent($name, Component $component)
    {
        $this->_components[$name] = $component;
    }
    
    /**
     *
     */
    public function getComponent($name)
    {
        return (isset($this->_components[$name]) ? $this->_components[$name] : FALSE);
    }
}
