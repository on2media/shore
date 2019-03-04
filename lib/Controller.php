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
     * Component classes used by this controller
     *
     * @var array
     */
    protected $_components = array();

    /**
     * Error messages
     *
     * @var array
     */
    protected $_messages = array();

    /**
     * Class Constructor
     */
    public function __construct()
    {
        foreach ($this->_components as $component) {

            $componentClass = $component . "Component";
            $this->$component = new $componentClass($this);

        }
    }

    /**
     * Sets the view to use.
     *
     * @param  View  $view
     * @return  View
     */
    public function setView(View $view)
    {
        return $this->_view = $view;
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
        if (!$this->_view instanceof View) return FALSE;
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
        if ($url == NULL) $url = _BASE . _PAGE . _QS;

        switch ($status) {
            case 301 : $status = "301 Moved Permanently"; break;
            case 303 : $status = "303 See Other"; break;
            case 307 : $status = "307 Temporary Redirect"; break;
            default  : $status = "302 Found"; break;
        }

        header($_SERVER["SERVER_PROTOCOL"] . " $status");
        header("Location: " . $url);

        exit();
    }

    /**
     * Returns the elapsed time as a sentence
     *
     * @param int $timestamp Unix Timestamp
     * @param int|null $now
     * @return string
     */
    public static function relativeTime($timestamp, $now=NULL)
    {
        $seconds = ($now == NULL ? time() : $now) - $timestamp;

        if ($seconds < 60)               return "less than a minute ago";
        else if ($seconds < 120)         return "about a minute ago";
        else if ($seconds < (60*60))     return (int)($seconds/60) . " minutes ago";
        else if ($seconds < (120*60))    return "about an hour ago";
        else if ($seconds < (24*60*60))  return "about " . (int)($seconds/(60*60)) . " hours ago";
        else if ($seconds < (48*60*60))  return "1 day ago";
        else                             return (int)($seconds/(60*60*24)) . " days ago";
    }

    /**
     * Helper method for form upload error messages
     *
     * @param int $error The code for the error
     * @return string The error message relating to code
     */
    public static function uploadErrorMessage($error)
    {
        switch ($error) {

            case UPLOAD_ERR_OK:
            case UPLOAD_ERR_NO_FILE:
                return FALSE;

            case UPLOAD_ERR_INI_SIZE:
            case UPLOAD_ERR_FORM_SIZE:
                return  "Uploaded file is too large.";

            case UPLOAD_ERR_PARTIAL:
                return "Uploaded file was only partially uploaded.";

            case UPLOAD_ERR_NO_TMP_DIR:
            case UPLOAD_ERR_CANT_WRITE:
                return "Unable to save uploaded file.";

            case UPLOAD_ERR_EXTENSION:
                return "An internal upload error occured.";

        }

        return "An unknown upload error occured.";
    }

    /**
     * Search helper method
     *
     * @param array $mapping
     */
    public function handleSearchSelects(array $mapping=array())
    {
        if (array_key_exists("do", $_GET) && $_GET["do"] == "sslsearch") {

            if (!IS_LIVE) sleep(1);

            $rtn = new stdClass();
            $rtn->status = "ERROR";

            $results = array();

            $field = (array_key_exists("field", $_GET) ? $_GET["field"] : "");
            $search = (array_key_exists("search", $_GET) ? $_GET["search"] : "");

            if ($field != "" && $search != "" && array_key_exists($field, $mapping)) {

                $className = $mapping[$field];
                $obj = new $className();

                $options = $obj->getCollection();

                $options->setLimit($obj->citeField(), "LIKE", "%" . $search . "%");
                //$options->setPagination(0, 20);

                foreach ($options->fetchAll() as $item) {

                    $results[$item->uid()] = $item->cite();

                }

                $rtn->status = "OK";
                $rtn->results = $results;

            }

            $this->setView(new JsonView($rtn));
            return $this->output();

        }

        return FALSE;
    }

    /**
     * Helper method to print log execution information
     *
     * @param string $message The message to display with information.
     * @param bool $nl Where we are processing a new line.
     */
    public function progressLog($message, $nl=TRUE)
    {
        $lineLen = 150;

        echo $log = sprintf("[%s] %s", date("H:i:s"), $message);

        if ($nl == TRUE) {

            $size = memory_get_usage();
            $unit = array("B", "KiB", "MiB", "GiB", "TiB", "PiB", "EiB", "ZiB", "YiB");
            $mem = " " . number_format(@round($size / pow(1024, ($i=floor(log($size,1024))) ),2),2) . " " . $unit[$i];

            $space = mb_strlen($log, "UTF-8") + mb_strlen($mem, "UTF-8");

            if ($space > $lineLen) {
                echo "\n" . str_repeat(" ", $lineLen - strlen($mem));
            } else {
                echo str_repeat(" ", $lineLen - $space);
            }

            echo "$mem\n";

        }

        flush();
    }

    /**
     * Get any error messages
     *
     * @return array
     */
    public function getMessages() {
        return $this->_messages;
    }

    public function __toString() {
        return get_class($this);
    }

    public function getGridNamespace()
    {
        return get_class($this);
    }
}
