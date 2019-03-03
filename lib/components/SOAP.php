<?php
/**
 * Script: SOAP.php
 * Date:  Monday, September 19, 2011 17:13:09
 * Description: SOAP Server component
 * Author: Rich Gray rich06@gmail.com
 * Copyright: Glooware
 */

/**
 *
 */
class SOAPComponent extends Component
{
    /**
     *
     */
    public function __construct(Controller $controller)
    {
        parent::__construct($controller);
        
        use_soap_error_handler(TRUE);
        
        // Switch off WSDL caching when in development
        if (!IS_LIVE) ini_set("soap.wsdl_cache_enabled", 0);
    }
    
    /**
    * Start SOAP listener and dispatch SOAP request
    * 
    * @return  void
    */
    public function serviceRequest($wsdl)
    {
        $soapServer = new SoapServer(_BASE . $wsdl, array(
            "soap_version"  =>  SOAP_1_2
        ));
        $soapServer->setObject($this->_controller);
        $soapServer->handle();
    }
    
    /**
     *
     */
    public function verify(ShoreObject $obj)
    {
        if (!$obj->validate()) {
            
            $errors = array();
            
            foreach ($obj->getControls() as $control) {
                
                if ($control->getError()) $errors[] = get_class($obj) . "::" . $control->getVar() . ": " . $control->getError();
                
            }
            
            $this->raiseException("Unable to verify data:\n" . implode("\n", $errors));
            
        }
        
        return TRUE;
    }

    /**
    * Finds the foreign key UID from the $field value
    *
    * @param  ShoreObject
    * @param  string
    * @param  string
    * @param  boolean
    * @return int
    */
    public function getForeignKey(ShoreObject $obj, $data, $field=NULL, $lookup=TRUE)
    {
        $isId = (is_numeric($data) || $field === NULL);
        
        $obj->getCollection()->setLimit(($isId ? "id" : $field), "=", $data);
        
        if (!$matchedObj = $obj->getCollection()->fetchFirst()) {
            
            // If missing for a simple lookup and the description was passed - then create row
            
            if ($lookup && !$isId && !$obj instanceof MapObject) {
                
                $obj->$field = $data;
                
                if ($this->verify($obj)) {
                    
                    $obj->save();
                    return $obj->uid();
                    
                }
                
            }
            
            $this->raiseException('Unable to find '.get_class($obj).' from passed identifier -> '.$data);
        }
        
        return $matchedObj->uid();
    }
    
    /**
    * Function to raise a SoapFault
    * 
    * @param  string
    * @return  void
    */
    public function raiseException($message)
    {
        throw new SoapFault(date("Ymd_His"), $message);
    }
}
