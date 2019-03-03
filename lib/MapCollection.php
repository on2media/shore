<?php
/**
 * Map Collection
 *
 * @package core
 */

/**
 * Extends the Collection class and handles requests to array-based (map) models.
 */
class MapCollection extends Collection
{
    /**
     *
     */
    protected $_limits = array();

    /**
     * Calls the parent constructor, but forces the model to be a MapObject by utilising type
     * hinting.
     */
    public function __construct(MapObject $obj)
    {
        parent::__construct($obj);
    }

    /**
     *
     */
    public function setLimit($field, $condition, $value)
    {
        $this->_limits[] = array(
            "field" => $field,
            "condition" => $condition,
            "value" => $value
        );

        return TRUE;
    }

    /**
     * @todo Handle... $this->_start   $this->_range   $this->_order
     */
    public function fetchAll()
    {
        $this->_dataSet = array();

        $objClass = get_class($this->_obj);

        foreach ($this->_obj->getValues() as $data) {

            $obj = new $objClass();
            foreach ($data as $key => $value) $obj->$key = $value;
            $this->_dataSet[$obj->uid()] = $obj;

        }

        foreach ($this->_limits as $limit) {

            foreach ($this->_dataSet as $key => $item) {

                $remove = FALSE;

                switch (strtoupper($limit["condition"])) {

                    case "=":
                        $remove = ($item->{$limit["field"]} != $limit["value"]);
                        break;

                    case "!=":
                    case "<>":
                        $remove = ($item->{$limit["field"]} == $limit["value"]);
                        break;

                    case "IN":
                    case "NOT IN":
                        $matches = 0;
                        foreach ($limit["value"] as $aval) {
                            $matches += ($item->{$limit["field"]} == $aval ? 1 : 0);
                        }
                        $remove = (strtoupper($limit["condition"]) == "IN" ? $matches == 0 : $matches > 0);
                        break;

                }

                if ($remove) unset($this->_dataSet[$key]);

            }

        }

        $this->_total = $this->count();
        return $this;
    }
}
