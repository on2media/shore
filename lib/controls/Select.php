<?php
/**
 *
 */

/**
 *
 */
class SelectControl extends Control
{
    /**
     *
     */
    public function output()
    {
        $func = "get" . var2func($this->_var);
        $options = $this->getOptions();

        $field = "";

        $selected = FALSE;
        if ($this->_obj->$func() instanceof $this->_objType) $selected = $this->_obj->$func()->uid();
        if (!$selected) $selected = $this->getDefaultSelected();

        if ($options->fetchAll()->count() > 0) {

            $field = sprintf("<select name=\"%s\"%s>\n    <option value=\"0\">&nbsp;</option>",
                $this->_prefix . $this->_var,
                ($this->usingBootstrap() ? " class=\"input-xxlarge\"" : "")
            );

            foreach ($options as $option) {
                $field .= sprintf("    <option value=\"%s\"%s>%s</option>\n",
                    $option->uid(),
                    ($selected == $option->uid() ? " selected=\"selected\"":""),
                    $option->cite()
                );
            }

            $field .= "</select>";

        }

        return $this->getWrapper($field);
    }

    /**
     *
     */
    public function process(array $formData)
    {
        $func = "get" . var2func($this->_var);
        $options = $this->getOptions();

        if (!isset($formData[$this->_prefix . $this->_var])) {

            $formData[$this->_prefix . $this->_var] = NULL;

        } else {

            if ($formData[$this->_prefix . $this->_var] == "0") $formData[$this->_prefix . $this->_var] = NULL;
            else {

                $options->setLimit($options->getObject()->uidField(), "=", $formData[$this->_prefix . $this->_var]);

                if ($selected = $options->fetchFirst()) {

                    // a valid value has been selected from the list
                    $formData[$this->_prefix . $this->_var] = $selected;

                } else {

                    // if we try and assign a value not on the list to the field it'll be returned as
                    // null when we get...() it and we won't get an error, so we'll set a manual error
                    // which will stop any further validation

                    if (array_key_exists("object", $this->_validation)) {

                        if (array_key_exists("message", $this->_validation["object"])) {

                            $this->setError($this->_validation["object"]["message"]);

                        } else {

                            $this->setError("Please select from the list.");

                        }

                    }

                }

            }

        }

        return parent::process($formData);
    }

    /**
     *
     */
    public function getOptions()
    {
        $func = "get" . var2func($this->_var);
        $optionClass = $this->_objType;
        $options = new $optionClass();
        return $options->getCollection();
    }

    /**
     *
     */
    public function getDefaultSelected()
    {
        return FALSE;
    }
}
