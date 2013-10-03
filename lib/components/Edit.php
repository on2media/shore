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
            
            if ($addSimilar) $data->{$data->uidField()} = NULL;
            
            $dataValid = $data->validate();
            $customValid = (!$hasCustomFields || $this->_controller->validateCustomFields($data));
            
            if (!$dataValid || !$customValid) {
                
                $tpl->assign("status_alert", "Please correct the error(s) below.");
                
            } else {
                
                $dbh = MySqlDatabase::getInstance();
                $dbh->beginTransaction();
                
                if (
                    $data->save(TRUE) &&
                    (!$hasCustomFields || $this->_controller->saveCustomFields($data))
                ) {
                    
                    $dbh->commit();
                    $tpl->assign_session("status_confirm", "Changes have been successfully saved.");
                    
                    $parts = explode("/", _PAGE);
                    for($i=0;$i<3;$i++) array_pop($parts);
                    $newUrl = _BASE . implode("/", $parts) . "/";
                    
                    $this->_controller->redirect($newUrl);
                    
                } else {
                    
                    $dbh->rollBack();
                    $tpl->assign("status_alert", "An error occured whilst saving the changes.");
                    
                }
                
            }
            
        }
        
        $tpl->assign("data", $data);
        $tpl->assign("page_title", ($uid == "new" || $addSimilar == TRUE ? "Add" : "Edit") . " $title");
        
        return TRUE;
    }

    public function validateObj(Object $obj, $uid, array $postData = array()) {

//      if (!$data = ($uid == "new" ? $obj : $obj->fetchById($uid))) {

//          $tpl = new SmartyView($this->_defaultLayout);
//          $tpl->assign("page_title", "Invalid Identifier");
//          $tpl->assign("status_alert", "Unable to match the identifier supplied to a dataset.");
//          $this->_controller->setView($tpl);
//          return FALSE;

//      }

        $data = $obj;

        if ($_POST && !empty($postData)) {

            foreach ($data->getControls() as $control) {
                $control->process($postData);
            }

            $messages = array();
            $dataValid = $data->validate();
            foreach ($data->getControls() as $control) {
                $error = $control->getError();
                if(!empty($error)) {
                    $messages[$control->getVar()] = $control->getError();
                }
            }
            $obj->setMessages($messages);
            //$customValid = ($this->_controller->validateCustomFields($data));

            if (!$dataValid) {// || !$customValid) {
                //var_dump($data->getMessages());exit;
                return FALSE;

            } else {

                $dbh = MySqlDatabase::getInstance();
                $dbh->beginTransaction();

                if ($data->save(TRUE)) {// && ($this->_controller->saveCustomFields($data))) {

                    $dbh->commit();
                    return TRUE;

                } else {

                    $dbh->rollBack();
                    return FALSE;

                }

            }

        }

        return FALSE;
    }
}
