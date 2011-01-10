<?php
/**
 *
 */

/**
 *
 */
class StaticPageController extends Controller
{
    /**
     *
     */
    public function dashboard()
    {
        $this->setView(new SmartyView("admin.home.tpl"));
        $this->getView()->setLayout("layout.admin.tpl");
        $this->getView()->assign("page_title", "Catalogue");
        
        return $this->output();
    }
    
    /**
     *
     */
    public function notFound()
    {
        header($_SERVER["SERVER_PROTOCOL"] . " 404 Not Found");
        
        $this->setView(new SmartyView("static_page.not_found.tpl"));
        $this->getView()->setLayout("layout.default.tpl");
        $this->getView()->assign("page_title", "Page Not Found");
        
        return $this->output();
    }
}
