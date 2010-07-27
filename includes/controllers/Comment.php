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
            $this->getView()->assign("edit_only", TRUE);
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
            
            if ($vars[1] == "new") {
                
                $this->setView(new SmartyView("layout.admin.tpl"));
                $this->getView()->assign("page_title", "Add Comment");
                $this->getView()->assign("status_alert", "You are not able to add comments.");
                
            } else {
                
                $this->Edit->draw($obj, $vars[1], "Comment");
                
            }
        }
        
        return $this->output();
    }
}
