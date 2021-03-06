<?php
/**
 *
 */

/**
 */
class GridComponent extends Component {

    protected $_viewTemplateName = 'admin.grid.tpl';

    protected $_layoutTemplateName = 'layout.admin.tpl';

    public function draw(ShoreObject $obj, $title, $addSimilar = FALSE, $noDelete = FALSE, $uidsForSqlInClause = array()) {
        $filterStr = "";
        $filters = array ();

        foreach ($_GET as $key => $value) {
            if (substr($key, 0, strlen("f")) == "f")
                $filters[$key] = $value;
        }

        $gridNamespace = $this->_controller->getGridNamespace();

        if (count($filters) == 0) {
            // check for any saved filters
            $sess = Session::getInstance();
            $allSessionFilters = $sess->getAllSessionFilters();
            if(!$allSessionFilters || !is_array($allSessionFilters)) {
                $allSessionFilters = array();
            }

            $savedFilters = array();
            if(isset($allSessionFilters[$gridNamespace])) {
                $savedFilters = $allSessionFilters[$gridNamespace];
            }
            if (is_array($savedFilters) && count($savedFilters ) > 0) {
                foreach($savedFilters as $key => $value ) {
                    if (substr($key, 0, strlen("f")) == "f")
                        $filters[$key] = $value;
                }
            }
        }

        if (count($filters) > 0) {

            $filterStr = http_build_query($filters);

            $sess = Session::getInstance();
            $sess->setFilters($filters);

            foreach ($obj->getObjFields() as $fieldName => $fieldSpec) {

                if (isset($fieldSpec["on_grid"], $fieldSpec["on_grid"]["position"], $fieldSpec["on_grid"]["filter"] )) {

                    $pos = $fieldSpec["on_grid"]["position"];
                    $filter = $fieldSpec["on_grid"]["filter"];
                    $isMapObject = FALSE;
                    if(isset($fieldSpec["on_grid"]["map_obj"])) {
                        $isMapObject = TRUE;
                    }

                    if (array_key_exists("f" . $pos, $filters)) {

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

                                $like = $filters["f".$pos];
                                $likeField = $fieldName;

                                // if "strip" is set all whitespace is removed from the filter text
                                if (array_key_exists("strip", $fieldSpec["on_grid"]) && $fieldSpec["on_grid"]["strip"] == TRUE) {
                                    $like = preg_replace("/\s+/", "", $like);
                                    $likeField = sprintf("*REPLACE(%s, ' ', '')", $fieldName);
                                }

                                $obj->getCollection()->setLimit($likeField, "LIKE", "%" . $like . "%");
                                break;

                            case "dropdown" :

                                if($filters["f" . $pos] != "0") {
                                    $obj->getCollection()->setLimit($fieldName, "=", $filters["f" . $pos]);
                                }
                                break;

                            case "boolean" :

                                if (in_array ($filters["f" . $pos], array ("yes", "no"))) {
                                    $obj->getCollection()->setLimit($fieldName, "=", ($filters["f" . $pos] == "yes" ? 1 : 0));
                                }
                                break;

                        }

                    }

                }

            }

        }

        if (isset($_GET["s"]) && preg_match ("/([0-9]+)(a|d)/", $_GET["s"], $matches)) {

            foreach($obj->getObjFields() as $fieldName => $fieldSpec) {

                if (isset($fieldSpec["on_grid"], $fieldSpec["on_grid"]["sortable"], $fieldSpec["on_grid"]["position"])) {

                    $pos = $fieldSpec["on_grid"]["position"];

                    if ($fieldSpec["on_grid"]["sortable"] == TRUE && $pos == $matches [1]) {

                        if ($obj->typeOf($fieldName) == "object") {

                            $pieces = explode( ":", $fieldSpec["type"], 2);
                            if(isset($fieldSpec["on_grid"]["map_obj"]) && $fieldSpec["on_grid"]["map_obj"]) {
                                // this handles cases where we want to use a MapObject as grid filters
                                $obj->getCollection()->setOrder($fieldName . ($matches[2] == "a" ? " ASC" : " DESC"));
                            }elseif (isset($pieces[1])) {
                                $modelObject = $pieces[1] . "Object";
                                $sortObj = new $modelObject();
                                $fieldName = $sortObj->selectCite($fieldName);
                                $obj->getCollection()->setOrder($fieldName . ($matches [2] == "a" ? " ASC" : " DESC"));
                            }else {
                                $obj->getCollection()->setOrder($fieldName . ($matches [2] == "a" ? " ASC" : " DESC"));
                            }

                        }elseif ($obj->typeOf($fieldName) == "string") {
                            $obj->getCollection()->setOrder($fieldName . ($matches[2] == "a" ? " ASC" : " DESC"));
                        }


                        break;
                    }

                }

            }

        }

        // If we have custom limits execute them now.
        if (method_exists($this->_controller, "gridLimits" )) {
            $this->_controller->gridLimits($obj->getCollection());
        }

        $session = Session::getInstance();

        if ($_POST && isset ($_POST["do"] ) && $_POST["do"] == "Update" && isset ($_POST["pp"])) {

            $perPage = (int) $_POST["pp"];
            if ($perPage == 0)
                $perPage = 20;
            $session->setRecordsPerPage($perPage);

            $this->_controller->redirect();

        }

        if (!$perPage = (int) $session->getRecordsPerPage())
            $session->setRecordsPerPage($perPage = 20);


        $allSessionFilters = $session->getAllSessionFilters();
        if(!$allSessionFilters || !is_array($allSessionFilters)) {
            $allSessionFilters = array();
        }

        $savedFilters = array();
        if(!isset($allSessionFilters[$gridNamespace])) {
            $allSessionFilters[$gridNamespace] = array();
        }
        $page = 1;
        if (isset($_GET["p"])) {
            $page = (int) $_GET["p"];
            $allSessionFilters[$gridNamespace]['page'] = $page;
        } else {
            if(isset($allSessionFilters[$gridNamespace]['page'])) {
                $page = (int)$allSessionFilters[$gridNamespace]['page'];
            }
        }
        $session->setAllSessionFilters($allSessionFilters);


        $obj->getCollection()->setPaginationPage($page, $perPage);
        if(!empty($uidsForSqlInClause)) {
            $obj->getCollection()->setLimit('id', 'IN', $uidsForSqlInClause);
        }
        $obj->getCollection()->fetchAll();

        if ($this->_controller->getView() === null) {
            $this->_controller->setView(new SmartyView($this->getView()));
        }
        if ($this->_controller->getView()->getLayout() === null) {
            $this->_controller->getView()->setLayout($this->getLayoutTemplateName());
        }

        $this->_controller->getView()->assign("filter_str", $filterStr);
        $this->_controller->getView()->assign("per_page", $perPage);
        $this->_controller->getView()->assign("namespace", $gridNamespace);

        if ($addSimilar)
            $this->_controller->getView()->assign( "add_similar", TRUE );
        if ($noDelete)
            $this->_controller->getView()->assign( "no_delete", TRUE );

        if ($_POST) {

            $status = "";
            $numDeleted = 0;

            if ($noDelete == FALSE && isset ($_POST["do"] ) && $_POST["do"] == "Delete Selected") {

                if (! isset ($_POST["items"]) || ! is_array ($_POST["items"]) || count ($_POST["items"] ) == 0) {

                    $this->_controller->getView()->assign("status_alert", "Nothing to delete.");

                } else {

                    // This only allows items on the current page to be deleted,
                    // which is good
                    // when some items aren't visible due to user restrictions
                    // etc.

                    foreach ($obj->getCollection () as $item) {

                        if (in_array ($item->uid(), $_POST["items"])) {

                            $deleted = FALSE;

                            if ($item instanceof MySqlViewObject) {

                                $objType = $item->getGridRel() . "Object";
                                $relItemObj = new $objType();
                                $relItem = $relItemObj->fetchById($item->uid());

                                if ($relItem)
                                    $deleted = $relItem->delete();

                            } else {
                                $deleted = $item->delete();
                            }

                            $numDeleted += ($deleted ? 1 : 0);

                            $status .= "\"" . $item->cite() . "\"" . ($deleted ? " was" : " wasn't") . " deleted.<br />\n";

                        }

                    }

                    $statusType = ($numDeleted == count($_POST ["items"]) ? "confirm" : ($numDeleted == 0 ? "alert" : "info"));

                    $this->_controller->getView()->assign_session("status_" . $statusType, $status);
                    $this->_controller->redirect();

                }

            }

            if (isset($_POST["do"] ) && $_POST["do"] == "Filter" && isset ($_POST["filter"] ) && is_array( $_POST["filter"])) {

                $filters = array();

                // grid filters
                foreach ($_POST["filter"] as $pos => $value) {
                    if ($value != "" && $value !== "0")
                        $filters["f" . $pos] = trim($value);
                }

                if(isset($_GET["s"]))
                    $filters["s"] = $_GET["s"];

                $sess = Session::getInstance();
                $allSessionFilters = $sess->getAllSessionFilters();

                if(!$allSessionFilters || !is_array($allSessionFilters)) {
                    $allSessionFilters = array();
                }

                $allSessionFilters[$gridNamespace] = $filters;
                $sess->setAllSessionFilters($allSessionFilters);

                $filterStr = http_build_query($filters);
                $this->_controller->redirect( _BASE . _PAGE . ($filterStr != "" ? "?" . $filterStr : ""));

            }

            if (isset ($_POST["do"] ) && $_POST["do"] == "Clear Filter") {

                $sess = Session::getInstance();
                $sess->unsetFilters();
                $sess->removeNamespaceFilters($gridNamespace);
                $this->_controller->redirect( _BASE . _PAGE );
            }

            $this->actions($obj, $noDelete, $page, $perPage);

        }

        $this->_controller->getView()->assign("session", Session::getInstance());
        $this->_controller->getView()->assign("data", $obj);
        $this->_controller->getView()->assign("page_title", $title);

    }

    public function actions(ShoreObject $obj, $noDelete = FALSE, $page = 1, $perPage = 20) {

        $obj->getCollection()->setPaginationPage($page, $perPage);
        $obj->getCollection()->fetchAll();

        $status = "";
        $numDeleted = 0;
        $doneAction = FALSE;

        if (($noDelete == FALSE && isset ($_POST["do"]) && $_POST["do"] == "Delete Selected") || (isset($_POST['gridBulkAction']) && $_POST['gridBulkAction'] == "delete")) {

            if (!isset($_POST["items"]) || !is_array($_POST["items"]) || count($_POST["items"]) == 0) {

                $this->_controller->getView()->assign("status_alert", "Nothing to delete.");

            } else {

                // This only allows items on the current page to be deleted,
                // which is good
                // when some items aren't visible due to user restrictions etc.

                foreach ($obj->getCollection () as $item) {

                    if (in_array ($item->uid(), $_POST ["items"])) {

                        $deleted = FALSE;

                        if ($item instanceof MySqlViewObject) {

                            $objType = $item->getGridRel() . "Object";
                            $relItemObj = new $objType();
                            $relItem = $relItemObj->fetchById($item->uid());

                            if ($relItem)
                                $deleted = $relItem->delete();

                        } else {
                            $deleted = $item->delete();

                        }

                        $numDeleted += ($deleted ? 1 : 0);

                        $status .= "\"" . $item->cite() . "\"" . ($deleted ? " was" : " wasn't") . " deleted.<br />\n";

                    }

                    $doneAction = TRUE;

                }

                $statusType = ($numDeleted == count($_POST["items"]) ? "confirm" : ($numDeleted == 0 ? "alert" : "info"));

                $this->_controller->getView()->assign("status_" . $statusType, $status);


            }

        }

        return $doneAction;

    }
}
