<?php
/**
 *
 */

/**
 *
 */
class TopicController extends Controller
{
    /**
     *
     */
    protected $_components = array("Auth", "Edit", "Grid");
    
    /**
     *
     */
    public function browse(array $vars=array())
    {
        $topics = new TopicObject();
        if (!$topic = $topics->fetchById($vars[1])) {
            
            $controller = new StaticPageController();
            return $controller->notFound();
            
        }
        
        $data = $topic->getPosts();
        $data->setLimit("type", "=", "post");
        
        if (!$data->fetchAll()) {
            $controller = new StaticPageController();
            return $controller->notFound();
        }
        
        $this->setView(new SmartyView("posts.browse.tpl")); // Common
        $this->getView()->setLayout("layout.default.tpl"); // Common
        
        $this->getView()->assign("data", $data); // Common
        $this->getView()->assign("page_title", "Posts about " . htmlentities($topic->getTopic()));
        
        $sideBar = new SideBarController(); // Common
        $this->getView()->assign("sidebar", $sideBar->view()); // Common
        
        return $this->output(); // Common
    }
    
    /**
     *
     */
    public function grid(array $vars=array())
    {
        $obj = new TopicObject();
        
        if ($this->Auth->canAccess(__FUNCTION__)) {
            $this->Grid->draw($obj, "Topics");
        }
        
        return $this->output();
    }
    
    /**
     *
     */
    public function edit(array $vars=array())
    {
        $obj = new TopicObject();
        
        if ($this->Auth->canAccess(__FUNCTION__)) {
            if (count($vars) != 2) exit();
            $this->Edit->draw($obj, $vars[1], "Topic");
        }
        
        return $this->output();
    }
}
