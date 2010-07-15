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
    public function draw(Object $obj, $title)
    {
        $obj->getCollection()->fetchAll();
        
        $this->_controller->setView(new SmartyView("admin.grid.tpl"));
        $this->_controller->getView()->setLayout("layout.admin.tpl");
        
        $this->_controller->getView()->assign("data", $obj);
        $this->_controller->getView()->assign("page_title", $title);
    }
}
