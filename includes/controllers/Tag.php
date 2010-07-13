<?php
/**
 *
 */

/**
 *
 */
class TagController extends Controller
{
    /**
     *
     */
    public function browse(array $vars=array())
    {
        $obj = new PostObject();
        
        $tags = new TagObject();
        if (!$tag = $tags->fetchById($vars[1])) {
            
            $controller = new StaticPageController();
            return $controller->notFound();
            
        }
        
        $data = $tag->getPosts();
        $data->setLimit("type", "=", "post");
        
        if (!$data->fetchAll()) {
            $controller = new StaticPageController();
            return $controller->notFound();
        }
        
        $this->setView(new SmartyView("posts.browse.tpl")); // Common
        $this->getView()->setLayout("layout.default.tpl"); // Common
        
        $this->getView()->assign("data", $data); // Common
        $this->getView()->assign("page_title", "Posts tagged " . htmlentities($tag->getTag()));
        
        $sideBar = new SideBarController(); // Common
        $this->getView()->assign("sidebar", $sideBar->view()); // Common
        
        return $this->output(); // Common
    }
}
