<?php
/**
 *
 */

/**
 *
 */
class BlogController extends Controller
{
    /**
     *
     */
    public function browse(array $vars=array())
    {
        $obj = new PostObject();
        
        // set order
        $obj->getCollection()->setLimit("type", "=", "post");
        $obj->getCollection()->setPagination(0, 10);
        
        if ($data = $obj->getCollection()->fetchAll()) {
            
            $this->setView(new SmartyView("posts.browse.tpl"));
            $this->getView()->setLayout("layout.default.tpl");
            
            $this->getView()->assign("data", $data);
            $this->getView()->assign("page_title", "Latest Posts");
            
            $sideBar = new SideBarController();
            $this->getView()->assign("sidebar", $sideBar->view());
            
            return $this->output();
            
        }
        
        $controller = new StaticPageController();
        return $controller->notFound();
    }
}
