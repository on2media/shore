<?php
/**
 *
 */

/**
 *
 */
class TextareaControl extends Control
{
    /**
     *
     */
    public function output()
    {
        $func = "get" . var2func($this->_var);

        $field = sprintf("<textarea name=\"%s\" %s rows=\"10\">%s</textarea>",
            $this->_prefix . $this->_var,
            ($this->usingBootstrap() ? "class=\"input-xxlarge\"" : "cols=\"80\""),
            htmlspecialchars($this->_obj->$func() ?? "")
        );

        return $this->getWrapper($field);
    }
}
