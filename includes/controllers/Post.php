<?php
/**
 *
 */

/**
 *
 */
class PostController extends Controller
{
    /**
     *
     */
    protected $_components = array("Auth", "Edit", "Grid");
    
    /**
     *
     */
    public function view(array $vars=array())
    {
        if (count($vars) != 3) exit();
        
        $obj = new PostObject();
        
        if ($data = $obj->fetchById($vars[2])) {
            
            if ($data->getType() != $vars[1]) {
                $this->redirect(_BASE . $data->getType() . "/" . $vars[2] . "/");
            }
            
            $this->setView(new SmartyView("post.view.tpl"));
            $this->getView()->setLayout("layout.default.tpl");
            
            $this->getView()->assign("data", $data);
            $this->getView()->assign("page_title", $data->getTitle());
            
            $sideBar = new SideBarController();
            $this->getView()->assign("sidebar", $sideBar->view());
            
            return $this->output();
            
        }
        
        $controller = new StaticPageController();
        return $controller->notFound();
    }
    
    /**
     *
     */
    public function grid(array $vars=array())
    {
        $obj = new PostObject();
        
        if ($this->Auth->canAccess(__FUNCTION__)) {
            $this->Grid->draw($obj, "Posts");
        }
        
        return $this->output();
    }
    
    /**
     *
     */
    public function edit(array $vars=array())
    {
        $obj = new PostObject();
        
        if ($this->Auth->canAccess(__FUNCTION__)) {
            if (count($vars) != 2) exit();
            $this->Edit->draw($obj, $vars[1], "Edit Post");
        }
        
        return $this->output();
    }
}
