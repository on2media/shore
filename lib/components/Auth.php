<?php
/**
 *
 */

/**
 *
 */
class AuthComponent extends Component
{
    /**
     *
     */
    public function canAccess($access)
    {
        $session = Session::getInstance();
        if (!$this->checkLogin()) return FALSE;
        if ($session->getUser()->canAccess($access)) return TRUE;
        
        $tpl = new SmartyView("layout.admin.tpl");
        $tpl->assign("page_title", "Access Denied");
        $tpl->assign("status_alert", "You are not authorised to access this function.");
        $this->_controller->setView($tpl);
        
        return FALSE;
    }
    
    /**
     *
     */
    public function checkLogin()
    {
        $session = Session::getInstance();
        
        if (!$session->getUser()) {
            
            $tpl = new SmartyView("admin.login.tpl");
            $tpl->setLayout("layout.admin.tpl");
            
            $tpl->assign("page_title", "Login Required");
            
            $tpl->assign("label_u", $session->getUsernameHeading());
            $tpl->assign("label_p", $session->getPasswordHeading());
            
            if ($_POST && isset($_POST["do"]) && $_POST["do"] == "Login") {
                
                if (!isset($_POST["u"]) || !isset($_POST["p"]) || trim($_POST["u"]) == "" || trim($_POST["p"]) == "") {
                    
                    $tpl->assign("status_alert", "Please complete both fields.");
                    
                } else {
                    
                    if (!$session->login(trim($_POST["u"]), trim($_POST["p"]))) {
                        
                        $tpl->assign("status_alert", "Your credentials were incorrect.");
                        
                    } else {
                        
                        $tpl->assign_session("status_confirm", "You have successfully logged in.");
                        $this->_controller->redirect();
                        
                    }
                    
                }
                
            }
            
            $this->_controller->setView($tpl);
            return FALSE;
            
        }
        
        return TRUE;
    }
    
    /**
     *
     */
    public function logout()
    {
        $session = Session::getInstance();
        $session->logout();
    }
}
