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
            $this->_controller->getView()->setLayout("layout.admin.tpl");
            
            $this->_controller->getView()->assign("data", $data);
            $this->_controller->getView()->assign("page_title", $title);
            
        }
    }
}
