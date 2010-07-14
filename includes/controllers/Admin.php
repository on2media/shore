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
    public function dashboard(array $vars=array())
    {
        $this->useComponent("Auth", new AuthComponent($this));
        
        $this->setView(new SmartyView("admin.home.tpl"));
        $this->getView()->setLayout("layout.admin.tpl");
        
        if ($this->getComponent("Auth")->canAccess(__FUNCTION__)) {
            
            $this->getView()->assign("page_title", "Blog Administration");
            
        }
        
        return $this->output();
    }
}
