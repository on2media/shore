<?php
/**
 *
 */

/**
 *
 */
class CommentController extends Controller
{
    /**
     *
     */
    protected $_components = array("Auth", "Edit", "Grid");
    
    /**
     *
     */
    public function grid(array $vars=array())
    {
        $obj = new CommentObject();
        
        if ($this->Auth->canAccess(__FUNCTION__)) {
            $this->Grid->draw($obj, "Comments");
        }
        
        return $this->output();
    }
    
    /**
     *
     */
    public function edit(array $vars=array())
    {
        $obj = new CommentObject();
        
        if ($this->Auth->canAccess(__FUNCTION__)) {
            if (count($vars) != 2) exit();
            $this->Edit->draw($obj, $vars[1], "Edit Comment");
        }
        
        return $this->output();
    }
}
