<?php
/**
 *
 */

/**
 * 
 */
class FileView extends View
{
    /**
     *
     */
    protected $_mime = FALSE;
    
    /**
     *
     */
    protected $_filename = FALSE;
    
    /**
     *
     */
    protected $_data = "\0x";
    
    /**
     *
     */
    public function __construct()
    {
        ini_set("session.cache_limiter", "private_no_expire");
        @header("Content-Description: File Transfer");
    }
    
    /**
     *
     */
    public function setMime($mime)
    {
        $this->_mime = $mime;
    }
    
    /**
     *
     */
    public function setFilename($filename)
    {
        $this->_filename = $filename;
    }
    
    /**
     *
     */
    public function setData($data)
    {
        $this->_data = $data;
    }
    
    /**
     *
     */
    public function output()
    {
        @header("Content-Type: " . $this->_mime);
        @header(sprintf(
            "Content-Disposition: attachment; filename=\"%s\"",
            $this->_filename
        ));
        @header("Content-Length: " . strlen($this->_data));
        
        return $this->_data;
    }
}
