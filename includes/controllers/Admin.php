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
        
        if ($this->Auth->checkLogin()) {
            
            $this->getView()->assign("page_title", "Blog Administration");
            
        }
        
        return $this->output();
    }
    
    /**
     *
     */
    public function logout(array $vars=array())
    {
        $this->Auth->logout();
        SmartyView::assign_session("status_confirm", "You have logged out.");
        $this->redirect(_BASE . DIR_ADMIN . "/");
    }
}
