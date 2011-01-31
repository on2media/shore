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
    public function notFound(array $vars=array())
    {
        @header($_SERVER["SERVER_PROTOCOL"] . " 404 Not Found");
        
        $this->setView($tpl = new SmartyView("static_page.not_found.tpl"));
        $tpl->setLayout("layout.default.tpl");
        $tpl->assign("page_title", "Page Not Found");
        
        return $this->output();
    }
}
