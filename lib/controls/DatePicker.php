<?php
/**
 *
 */

/**
 *
 */
class DatePickerControl extends DateTimePickerControl
{
    /**
     *
     */
    public function output()
    {
        $func = "get" . var2func($this->_var);

        $field = sprintf("<input type=\"text\" name=\"%s\" value=\"%s\" size=\"30\" class=\"date\" />",
            $this->_prefix . $this->_var,
            date("d F Y", ($this->_obj->$func() == 0 ? time() : $this->_obj->$func()))
        );

        return $this->getWrapper($field);
    }
}
