<?php
/**
 *
 */

/**
 *
 */
class AuthorController extends Controller
{
    /**
     *
     */
    public function browse(array $vars=array())
    {
        $obj = new PostObject();
        
        // set order
        $obj->getCollection()->setLimit("type", "=", "post");
        $obj->getCollection()->setLimit("author", "=", $vars[1]); // ADDED
        $obj->getCollection()->setPagination(0, 10);
        
        if (!$data = $obj->getCollection()->fetchAll()) {
            $controller = new StaticPageController();
            return $controller->notFound();
        }
        
        $author = $obj->getCollection()->fetchFirst()->getAuthor(); // ADDED
        
        $this->setView(new SmartyView("posts.browse.tpl"));
        $this->getView()->setLayout("layout.default.tpl");
        
        $this->getView()->assign("data", $data);
        $this->getView()->assign("page_title", "Posts by " . htmlentities($author->getName())); // EDITED
        
        $sideBar = new SideBarController();
        $this->getView()->assign("sidebar", $sideBar->view());
        
        return $this->output();
    }
}
