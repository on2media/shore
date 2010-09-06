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
    public function draw(Object $obj, $uid, $title, $hasCustomFields=FALSE, $addSimilar=FALSE)
    {
        if (!$data = ($uid == "new" ? $obj : $obj->fetchById($uid))) {
            
            $tpl = new SmartyView("layout.admin.tpl");
            $tpl->assign("page_title", "Invalid Identifier");
            $tpl->assign("status_alert", "Unable to match the identifier supplied to a dataset.");
            $this->_controller->setView($tpl);
            return FALSE;
            
        }
        
        $this->_controller->setView(new SmartyView("admin.edit.tpl"));
        $tpl = $this->_controller->getView();
        
        $tpl->setLayout("layout.admin.tpl");
        
        if ($_POST) {
            
            foreach ($data->getControls() as $control) $control->process($_POST);
            
            $dataValid = $data->validate();
            $customValid = (!$hasCustomFields || $this->_controller->validateCustomFields($data));
            
            if (!$dataValid || !$customValid) {
                
                $tpl->assign("status_alert", "Please correct the error(s) below.");
                
            } else {
                
                $dbh = MySqlDatabase::getInstance();
                $dbh->beginTransaction();
                
                if ($addSimilar) $data->{$data->uidField()} = NULL;
                
                if (
                    $data->save(TRUE) &&
                    (!$hasCustomFields || $this->_controller->saveCustomFields($data))
                ) {
                    
                    $dbh->commit();
                    $tpl->assign_session("status_confirm", "Changes have been successfully saved.");
                    
                    $newUrl = _PAGE;
                    
                    if (substr(_PAGE, -4, 4) == "new/") {
                        $newUrl = substr(_PAGE, 0, -4) . $data->uid() . "/";
                    }
                    
                    $newUrl = substr($newUrl, 0, -strlen(($addSimilar ? "add" : "edit") . "/{$data->uid()}/"));
                    $this->_controller->redirect(_BASE . $newUrl);
                    
                } else {
                    
                    $dbh->rollBack();
                    $tpl->assign("status_alert", "An error occured whilst saving the changes.");
                    
                }
                
            }
            
        }
        
        $tpl->assign("data", $data);
        $tpl->assign("page_title", ($uid == "new" ? "Add" : "Edit") . " $title");
        
        return TRUE;
    }
}
