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
            
            if ($data->getType()->getKey() != $vars[1]) {
                $this->redirect(_BASE . $data->getType()->getKey() . "/" . $vars[2] . "/");
            }
            
            $this->setView(new SmartyView("post.view.tpl"));
            $this->getView()->setLayout("layout.default.tpl");
            
            $this->getView()->assign("data", $data);
            $this->getView()->assign("page_title", $data->getTitle());
            
            if ($data->getCanComment()) {
                $commentForm = new CommentFormController();
                $this->getView()->assign("comment_form", $commentForm->view(array($data)));
            }
            
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
        
        if ($this->Auth->canAccess(4) || $this->Auth->canAccess(1) || $this->Auth->canAccess(2) || $this->Auth->canAccess(10)) {
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
        
        if ($this->Auth->canAccess(1) || $this->Auth->canAccess(2) || $this->Auth->canAccess(10)) {
            if (count($vars) != 2) exit();
            $this->Edit->draw($obj, $vars[1], "Post");
        }
        
        return $this->output();
    }
}
