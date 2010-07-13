<?php
/**
 *
 */

/**
 *
 */
class SideBarController extends Controller
{
    /**
     *
     */
    public function view(array $vars=array())
    {
        $this->setView(new SmartyView("side_bar.view.tpl"));
        return $this->output();
    }
}
