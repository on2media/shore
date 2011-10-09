<?php
/**
 *
 */

/**
 *
 */
class ObjectField
{
    /**
     *
     */
    protected $_obj;
    
    /**
     *
     */
    public $name;
    
    /**
     *
     */
    public $value;
    
    /**
     *
     */
    public $heading;
    
    /**
     *
     */
    public $tip;
    
    /**
     *
     */
    public $required;
    
    /**
     *
     */
    public $prefix;
    
    /**
     *
     */
    public $suffix;
    
    /**
     * Recognised types:
     * 
     * string (default)
     * text
     * unsigned
     * boolean
     * object:<object>
     * timestamp[:<format>] (default format is "jS F Y H:i")
     */
    public $type;
    
    /**
     *
     */
    public function __construct(Object $obj, $name, &$spec)
    {
        $this->_obj = $obj;
        
        $this->name = $name;
        $this->value =& $spec["value"];
        $this->heading = (isset($spec["heading"]) ? $spec["heading"] : var2label($name));
        $this->tip = (isset($spec["on_edit"]["tip"]) ? $spec["on_edit"]["tip"] : NULL);
        $this->required = (isset($spec["required"]) && $spec["required"] == TRUE);
        $this->prefix = (isset($spec["prefix"]) ? $spec["prefix"] : "");
        $this->suffix = (isset($spec["suffix"]) ? $spec["suffix"] : "");
        
        if (!isset($spec["type"])) {
            $this->type = "string";
        } else {
            $pieces = explode(":", $spec["type"], 2);
            $this->type = $pieces[0];
        }
    }
    
    /**
     *
     */
    public function toXml(DOMDocument $doc, DOMElement $root, $laconic=FALSE)
    {
        $root->appendChild($node = $doc->createElement($this->name));
        
        if ($laconic == FALSE) {
            
            $node->appendChild(($el = $doc->createElement("heading")));
            $el->appendChild($doc->createTextNode($this->heading));
            
            $node->appendChild(($el = $doc->createElement("tip")));
            $el->appendChild($doc->createTextNode($this->tip));
            
            $node->appendChild(($el = $doc->createElement("required")));
            $el->appendChild($doc->createTextNode($this->required ? "true" : "false"));
            
            $node->appendChild(($el = $doc->createElement("prefix")));
            $el->appendChild($doc->createTextNode($this->prefix));
            
            $node->appendChild(($el = $doc->createElement("suffix")));
            $el->appendChild($doc->createTextNode($this->suffix));
            
        }
        
        if ($laconic == FALSE) {
            $node->appendChild(($el = $doc->createElement("value")));
        }
        
        $outputValue = $this->_obj->{$this->name};
        
        if ($this->type != "object") {
            
            // no processing required for string, text or unsigned
            
            switch ($this->type) {
                
                case "timestamp":
                    if ($this->value == NULL) return "";
                    $outputValue = date("jS F Y H:i", $this->value);
                    break;
                
                case "boolean":
                    $outputValue = ($this->value ? "true" : "false");
                    break;
                
            }
            
            $outputNode = $doc->createTextNode($outputValue);
            
            
        } else if ($outputValue instanceof Object) { // Object
            
            $foreign = $outputValue->toXml($laconic);
            $x = $foreign->getElementsByTagName("*")->item(0);
            
            $outputNode = $doc->importNode($x, TRUE);
            
        } else { // Object with no value
            
            $outputNode = $doc->createTextNode("");
            
        }
        
        if ($laconic == FALSE) {
            $el->appendChild($outputNode);
        } else {
            $node->appendChild($outputNode);
        }
        
    }
}
