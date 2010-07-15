<?php
/**
 *
 */

/**
 *
 */
class AdminController extends Controller
{
    /**
     *
     */
    protected $_components = array("Auth");
    
    /**
     *
     */
    public function dashboard(array $vars=array())
    {
        $this->setView(new SmartyView("admin.home.tpl"));
        $this->getView()->setLayout("layout.admin.tpl");
        
        if ($this->Auth->canAccess(__FUNCTION__)) {
            
            $this->getView()->assign("page_title", "Blog Administration");
            
        }
        
        return $this->output();
    }
}
