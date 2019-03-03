<?php
/**
 *
 */

/**
 *
 */
abstract class Control
{
    /**
     *
     */
    protected $_obj = NULL;

    /**
     *
     */
    protected $_prefix = "";

    /**
     *
     */
    protected $_var = "";

    /**
     *
     */
    protected $_heading = "";

    /**
     *
     */
    protected $_tip = "";

    /**
     *
     */
    protected $_validation = array();

    /**
     *
     */
    protected $_required = FALSE;

    /**
     *
     */
    protected $_showEmpty = TRUE;

    /**
     *
     */
    protected $_objType;

    /**
     *
     */
    protected $_showValidation = FALSE;

    /**
     *
     */
    protected $_error = "";

    /**
     *
     */
    protected $_error_code = "";

    /**
     *
     */
    protected $_fieldPrefix = "";

    /**
     *
     */
    protected $_fieldSuffix = "";

    /**
     *
     * @param ShoreObject $obj
     */
    public function __construct($obj, $prefix, $var, $fieldSpec = array())
    {
        $this->_obj = $obj;

        $this->_prefix = $prefix;
        $this->_var = $var;

        $this->_heading = $fieldSpec["obj"]->heading;
        $this->_tip = $fieldSpec["obj"]->tip;

        if (isset($fieldSpec["validation"])) $this->_validation = $fieldSpec["validation"];

        $this->_required = $fieldSpec["obj"]->required;

        if (isset($fieldSpec["on_edit"]["show_empty"]) && $fieldSpec["on_edit"]["show_empty"] == FALSE) $this->_showEmpty = FALSE;

        if ($this->_obj->typeOf($var) == "object") {
            $pieces = explode(":", $fieldSpec["type"], 2);
            if (isset($pieces[1])) $this->_objType = $pieces[1] . "Object";
        }

        $this->_fieldPrefix = $fieldSpec["obj"]->prefix;
        $this->_fieldSuffix = $fieldSpec["obj"]->suffix;
    }

    /**
     *
     */
    public function getWrapper($field="")
    {
        if ($field == "" && !$this->_showEmpty) return "";

        if ($this->usingBootstrap()) {

            $addOns = ($this->_fieldPrefix != "" ? " input-prepend" : "");
            if ($this->_fieldSuffix != "") $addOns .= " input-append";
            $addOns = trim($addOns);

            $rtn = sprintf(

                "<div class=\"control-group%s\">\n" .
                "    <label class=\"control-label\">%s%s</label>\n" . // Heading, Required
                "    <div class=\"controls\">" .
                "        %s" . // Add-ons Wrapper
                "        %s%s%s\n" . // Prefix, Field, Suffix
                "        %s" . // Add-ons Wrapper
                "        %s" . // Validation
                "        %s" . // Tip
                "    </div>\n" .
                "</div>\n",

                ($this->_showValidation != TRUE ? "" : ($this->getError() ? " error" : " success")),
                htmlspecialchars($this->_heading),
                ($this->_required ? " <i class=\"icon-asterisk\"></i>" : ""),
                ($addOns == "" ? "" : sprintf("<div class=\"%s\">", $addOns)),
                ($this->_fieldPrefix == "" ? "" : sprintf("<span class=\"add-on\">%s</span>", $this->_fieldPrefix)),
                ($field == "" ? "&nbsp;" : $field),
                ($this->_fieldSuffix == "" ? "" : sprintf("<span class=\"add-on\">%s</span>", $this->_fieldSuffix)),
                ($addOns == "" ? "" : "</div>"),
                ($this->_showValidation == TRUE && $this->getError()
                    ? "<p class=\"help-block\">" . htmlspecialchars($this->getError()) . "</p>\n"
                    : ""
                ),
                ($this->_tip != NULL ? sprintf("<p class=\"help-block\">%s</p>", $this->_tip) : "")

            );

        } else {

            $rtn = sprintf("<p>\n    <label>%s%s%s</label>\n    %s%s%s\n%s</p>\n",
                htmlspecialchars($this->_heading),
                ($this->_required ? "<em>*</em>" : ""),
                ($this->_tip != NULL ? sprintf("<span class=\"tip\">%s</span>", $this->_tip) : ""),
                ($this->_fieldPrefix == "" ? "" : $this->_fieldPrefix . " "),
                ($field == "" ? "&nbsp;" : $field),
                ($this->_fieldSuffix == "" ? "" : " " . $this->_fieldSuffix),
                ($this->_showValidation == TRUE && $this->getError()
                    ? "<" . CONTROL_ERROR_TAG . ">" . htmlspecialchars($this->getError()) . "</" . CONTROL_ERROR_TAG . ">\n"
                    : ""
                )
            );

        }

        return $rtn;
    }

    /**
     *
     */
    public function getObject()
    {
        return $this->_obj;
    }

    /**
     *
     */
    public function getVar()
    {
        return $this->_var;
    }

    /**
     *
     */
    public function getError()
    {
        return ($this->_error != "" ? $this->_error : FALSE);
    }

    /**
     *
     */
    public function setError($error)
    {
        $this->_error = $error;
    }

    /**
     *
     */
    public function getErrorCode()
    {
        return ($this->_error_code != "" ? $this->_error_code : FALSE);
    }

    /**
     *
     */
    public function setErrorCode($code)
    {
        $this->_error_code = $code;
    }

    /**
     *
     */
    abstract public function output();

    /**
     *
     */
    public function process(array $formData)
    {
        $this->setShowValidation(TRUE);

        if (!array_key_exists($this->_prefix . $this->_var, $formData)) {

            $this->setError("Field was missing from the received form data.");
            $this->setErrorCode("missing");
            $this->_obj->{$this->_var} = $formData[$this->_prefix . $this->_var] = FALSE;

        } else {

            $this->_obj->{$this->_var} = $formData[$this->_prefix . $this->_var];

        }
    }

    public function setShowValidation($value)
    {
        $this->_showValidation = $value;
    }

    public function getShowValidation()
    {
        return $this->_showValidation;
    }

    /**
     *
     */
    public function validate()
    {
        $value = $this->_obj->{$this->_var};

        if ($this->getError() != "") return FALSE;

        if ($this->_required == FALSE) {

            if ($value instanceof Collection && $value->count() == 0) return TRUE;
            else if ($value === NULL) return TRUE;

        } else if ((empty($value) && $value !== "0" && $value !== 0) || ($value instanceof Collection && $value->count() == 0)) {

            // This is required, but the field the control hasn't returned any output so we can
            // assume it isn't required.
            if ($this->output() == "" && $this->_showEmpty == FALSE) return TRUE;

            // This is required!
            $this->setError("This field is required.");
            $this->setErrorCode("required");
            return FALSE;

        }

        foreach ($this->_validation as $rule => $opts) {

            $fail = FALSE;
            $message = $code = "";

            switch ($rule) {

                case "object":
                    $modelObject = $opts["object"] . "Object";
                    $fail = (!is_object($value) || !$value instanceof $modelObject);
                    $message = "Please select from the list.";
                    $code = "not_on_list";
                    break;

                case "timestamp":
                    $fail = ($value != NULL && !@date("U", $value));
                    $message = "Please enter a valid date/time.";
                    $code = "time_invalid";
                    break;

                case "beforenow":
                    $fail = ($value != NULL && (!@date("U", $value) || $value > time()));
                    $message = "Please enter a valid date/time.";
                    $code = "time_too_late";
                    break;

                case "afternow":
                    $fail = ($value != NULL && (!@date("U", $value) || $value < time()));
                    $message = "Please enter a valid date/time.";
                    $code = "time_too_early";
                    break;

                case "regexp":
                    $subject = (is_object($value) ? $value->uid() : $value);
                    $fail = (!preg_match($opts["test"], $subject));
                    $message = "This field is invalid.";
                    $code = "no_regexp_match";
                    break;

                case "unique":

                    $className = get_class($this->_obj);
                    $obj = new $className();

                    $collection = $obj->getCollection();
                    $collection->setLimit($this->_var, "=", $value);

                    foreach ($collection->fetchAll() as $other) {

                        $isNew = ($this->_obj instanceof MySqlObject && $this->_obj->isNew());

                        if ($isNew || $other->uid() != $this->_obj->uid()) {

                            $fail = TRUE;
                            $message = "This field must be unique.";
                            $code = "not_unique";
                            break(2);

                        }

                    }

                    break;

            }

            if ($fail) {
                $this->setError((isset($opts["message"]) ? $opts["message"] : $message));
                $this->setErrorCode((isset($opts["error_code"]) ? $opts["error_code"] : $code));
                return FALSE;
            }

        }

        return TRUE;
    }

    public function usingBootstrap()
    {
        return (defined("USING_BOOTSTRAP") && USING_BOOTSTRAP == TRUE);
    }
}
