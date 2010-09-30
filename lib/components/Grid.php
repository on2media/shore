<?php
/**
 *
 */

/**
 *
 */
class GridComponent extends Component
{
    /**
     *
     */
    public function draw(Object $obj, $title, $addSimilar=FALSE, $noDelete=FALSE)
    {
        $obj->getCollection()->setPaginationPage((isset($_GET["p"]) ? (int)$_GET["p"] : 1));
        $obj->getCollection()->fetchAll();
        
        $this->_controller->setView(new SmartyView("admin.grid.tpl"));
        $this->_controller->getView()->setLayout("layout.admin.tpl");
        
        if ($addSimilar) $this->_controller->getView()->assign("add_similar", TRUE);
        if ($noDelete) $this->_controller->getView()->assign("no_delete", TRUE);
        
        if ($_POST) {
            
            $status = "";
            $numDeleted = 0;
            
            if ($noDelete == FALSE && isset($_POST["do"]) && $_POST["do"] == "Delete Selected") {
                
                if (!isset($_POST["items"]) || !is_array($_POST["items"]) || count($_POST["items"]) == 0) {
                    
                    $this->_controller->getView()->assign("status_alert", "Nothing to delete.");
                    
                } else {
                    
                    foreach ($obj->getCollection() as $item) {
                        
                        if (in_array($item->uid(), $_POST["items"])) {
                            
                            $deleted = $item->delete();
                            $numDeleted += ($deleted ? 1 : 0);
                            
                            $status .= "\"" . $item->cite() . "\"" . ($deleted ? " was" : " wasn't") . " deleted.<br />\n";
                            
                        }
                        
                    }
                    
                    $statusType = ($numDeleted == count($_POST["items"]) ? "confirm" : ($numDeleted == 0 ? "alert" : "info"));
                    
                    $this->_controller->getView()->assign_session("status_" . $statusType, $status);
                    $this->_controller->redirect();
                    
                }
                
            }
            
        }
        
        $this->_controller->getView()->assign("data", $obj);
        $this->_controller->getView()->assign("page_title", $title);
    }
}
