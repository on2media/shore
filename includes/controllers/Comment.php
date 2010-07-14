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
    public function grid(array $vars=array())
    {
        $obj = new CommentObject();
        $obj->getCollection()->fetchAll();
        
        $this->useComponent("Auth", new AuthComponent($this));
        
        $this->setView(new SmartyView("admin.grid.tpl"));
        $this->getView()->setLayout("layout.admin.tpl");
        
        if ($this->getComponent("Auth")->canAccess(__FUNCTION__)) {
            
            $this->getView()->assign("data", $obj);
            $this->getView()->assign("page_title", "Comments");
            
        }
        
        return $this->output();
    }
}
