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
        $filterStr = "";
        $filters = array();
        
        foreach ($_GET as $key => $value) {
            if (substr($key, 0, strlen("f")) == "f") $filters[$key] = $value;
        }
        
        if (count($filters) > 0) {
            
            $filterStr = http_build_query($filters);
            
            foreach ($obj->getObjFields() as $fieldName => $fieldSpec) {
                
                if (isset($fieldSpec["on_grid"], $fieldSpec["on_grid"]["position"], $fieldSpec["on_grid"]["filter"])) {
                    
                    $pos = $fieldSpec["on_grid"]["position"];
                    $filter = $fieldSpec["on_grid"]["filter"];
                    
                    if (array_key_exists("f".$pos, $filters)) {
                        
                        if ($filter == "freetext" && $obj->typeOf($fieldName) == "object") {
                            
                            $pieces = explode(":", $fieldSpec["type"], 2);
                            
                            if (isset($pieces[1])) {
                                
                                $modelObject = $pieces[1] . "Object";
                                $filterObj = new $modelObject();
                                $fieldName = "*" . $filterObj->selectCite($fieldName);
                            }
                            
                        }
                        
                        switch ($filter) {
                            
                            case "freetext":
                                
                                $obj->getCollection()->setLimit($fieldName, "LIKE", "%" . $filters["f".$pos] . "%");
                                break;
                            
                            case "dropdown":
                                
                                $obj->getCollection()->setLimit($fieldName, "=", $filters["f".$pos]);
                                break;
                            
                        }
                        
                    }
                    
                }
                
            }
            
        }
        
        if (isset($_GET["s"]) && preg_match("/([0-9]+)(a|d)/", $_GET["s"], $matches)) {
            
            foreach ($obj->getObjFields() as $fieldName => $fieldSpec) {
                
                if (isset($fieldSpec["on_grid"], $fieldSpec["on_grid"]["sortable"], $fieldSpec["on_grid"]["position"])) {
                    
                    $pos = $fieldSpec["on_grid"]["position"];
                    
                    if ($fieldSpec["on_grid"]["sortable"] == TRUE && $pos == $matches[1]) {
                        
                        if ($obj->typeOf($fieldName) == "object") {
                            
                            $pieces = explode(":", $fieldSpec["type"], 2);
                            
                            if (isset($pieces[1])) {
                                
                                $modelObject = $pieces[1] . "Object";
                                $sortObj = new $modelObject();
                                $fieldName = $sortObj->selectCite($fieldName);
                            }
                            
                        }
                        
                        $obj->getCollection()->setOrder($fieldName . ($matches[2] == "a" ? " ASC" : " DESC"));
                        break;
                    }
                    
                }
                
            }
            
        }
        
        $session = Session::getInstance();
        
        if ($_POST && isset($_POST["do"]) && $_POST["do"] == "Update" && isset($_POST["pp"])) {
            
            $perPage = (int)$_POST["pp"];
            if ($perPage == 0) $perPage = 20;
            $session->setRecordsPerPage($perPage);
            
            $this->_controller->redirect();
            
        }
        
        if (!$perPage = (int)$session->getRecordsPerPage()) $session->setRecordsPerPage($perPage = 20);
        
        $obj->getCollection()->setPaginationPage((isset($_GET["p"]) ? (int)$_GET["p"] : 1), $perPage);
        $obj->getCollection()->fetchAll();
        
        $this->_controller->setView(new SmartyView("admin.grid.tpl"));
        $this->_controller->getView()->setLayout("layout.admin.tpl");
        
        $this->_controller->getView()->assign("filter_str", $filterStr);
        $this->_controller->getView()->assign("per_page", $perPage);
        
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
            
            if (isset($_POST["do"]) && $_POST["do"] == "Filter" && isset($_POST["filter"]) && is_array($_POST["filter"])) {
                
                $filters = array();
                
                foreach ($_POST["filter"] as $pos => $value) {
                    if ($value != "" && $value !== "0") $filters["f" . $pos] = trim($value);
                }
                
                if (isset($_GET["s"])) $filters["s"] = $_GET["s"];
                
                $filterStr = http_build_query($filters);
                $this->_controller->redirect(_BASE . _PAGE . ($filterStr != "" ? "?" . $filterStr : ""));
                
            }
            
        }
        
        $this->_controller->getView()->assign("data", $obj);
        $this->_controller->getView()->assign("page_title", $title);
    }
}
