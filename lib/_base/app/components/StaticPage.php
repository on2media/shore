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
    public function notFound()
    {
        header($_SERVER["SERVER_PROTOCOL"] . " 404 Not Found");
        
        $this->setView(new SmartyView("static_page.not_found.tpl"));
        $this->getView()->setLayout("layout.default.tpl");
        $this->getView()->assign("page_title", "Page Not Found");
        
        return $this->output();
    }
}
