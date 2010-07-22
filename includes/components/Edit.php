<?php
/**
 *
 */

/**
 *
 */
class EditComponent extends Component
{
    /**
     *
     */
    public function draw(Object $obj, $uid, $title)
    {
        if ($data = $obj->fetchById($uid)) {
            
            $this->_controller->setView(new SmartyView("admin.edit.tpl"));
            $tpl = $this->_controller->getView();
            
            $tpl->setLayout("layout.admin.tpl");
            
            if ($_POST) {
                
                if (!$data->validateEditForm($_POST)) {
                    
                    $tpl->assign("status_alert", "Please correct the error(s) below.");
                    
                } else if ($data->save()) {
                    
                    $tpl->assign("status_confirm", "Changes have been successfully saved.");
                    
                } else {
                    
                    $tpl->assign("status_alert", "An error occured whilst saving the changes.");
                    
                }
                
            }
            
            $tpl->assign("data", $data);
            $tpl->assign("page_title", $title);
            
        }
    }
}
