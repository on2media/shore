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
    public function canAccess($method)
    {
        //echo "Access requested for: " . get_class($this->_controller) . "::" . $method . "()<br />";
        return TRUE;
    }
}
