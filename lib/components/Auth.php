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
        if (!$this->checkLogin()) return FALSE;
        if ($_SESSION[AUTH_SESSION]->canAccess($access)) return TRUE;
        
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
        @session_start();
        
        $userObject = AUTH_MODEL;
        
        if (!isset($_SESSION[AUTH_SESSION]) || !$_SESSION[AUTH_SESSION] instanceof $userObject) {
            
            $tpl = new SmartyView("admin.login.tpl");
            $tpl->setLayout("layout.admin.tpl");
            
            $tpl->assign("page_title", "Login Required");
            
            $users = new $userObject();
            $tpl->assign("label_u", $users->getFieldHeading(AUTH_USERNAME));
            $tpl->assign("label_p", $users->getFieldHeading(AUTH_PASSWORD));
            
            if ($_POST && isset($_POST["do"]) && $_POST["do"] == "Login") {
                
                if (!isset($_POST["u"]) || !isset($_POST["p"]) || trim($_POST["u"]) == "" || trim($_POST["p"]) == "") {
                    
                    $tpl->assign("status_alert", "Please complete both fields.");
                    
                } else {
                    
                    $users->getCollection()->setLimit(AUTH_USERNAME, "=", trim($_POST["u"]));
                    $users->getCollection()->setLimit(AUTH_PASSWORD, "=", md5(trim($_POST["p"])));
                    
                    if(!$currentUser = $users->getCollection()->fetchFirst()) {
                        
                        $tpl->assign("status_alert", "Your credentials were incorrect.");
                        
                    } else {
                        
                        $_SESSION[AUTH_SESSION] = $currentUser;
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
        unset($_SESSION[AUTH_SESSION]);
    }
}
