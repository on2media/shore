<?php
/**
 *
 */

/**
 *
 */
class CommentFormController extends Controller
{
    /**
     *
     */
    public function view(array $vars=array())
    {
        if ($vars[0] instanceof PostObject) {
            
            $this->setView(new SmartyView("comment_form.view.tpl"));
            
            $obj = new CommentObject();
            $this->getView()->assign("data", $obj);
            
            if ($_POST && isset($_POST["do"]) && $_POST["do"] == "Leave Comment") {
                
                $obj->setPost($vars[0]);
                $obj->setReceived(time());
                
                $capture = array("name", "email", "website", "content");
                
                foreach ($capture as $field) {
                    if (isset($_POST[$field])) $obj->$field = trim($_POST[$field]);
                }
                
                if (!$obj->validate()) {
                    
                    $this->getView()->assign("status", "invalid");
                    
                } else if (!$obj->save()) {
                    
                    $this->getView()->assign("status", "error");
                    
                } else {
                    
                    $this->getView()->assign("status", "ok");
                    
                    //TODO: redirect with hash, email notification
                    
                }
                
            }
            
            return $this->output();
            
        }
        
    }
}
