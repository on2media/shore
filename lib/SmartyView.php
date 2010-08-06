<?php
/**
 * Smarty view
 *
 * @package core
 */

/**
 * SmartyView class
 */
class SmartyView extends View
{
    /**
     * Stores the Smarty class
     *
     * @var Smarty|null
     */
    protected $_smarty = NULL;
    
    /**
     * Stores the template filename.
     * @var  string
     */
    protected $_template = "";
    
    /**
     * Stores the layout template filename.
     *
     * @var string|null
     */
    protected $_layout = NULL;
    
    /**
     * Initiates a Smarty instance and configures it. Sets the template to use and assigns any
     * variables stored in the session and any global variables (e.g. _BASE and _PAGE).
     *
     * @param  string  $template  Filename of the template to use.
     */
    public function __construct($template)
    {
        $this->_smarty = new Smarty();
        
        $this->_smarty->template_dir = DIR_VIEWS . DS;
        
        $base = realpath(dirname(__FILE__)) . DS;
        
        $this->_smarty->compile_dir   =  $base . "cache" . DS . "smarty_compiled" . DS;
        $this->_smarty->config_dir    =  $base . "cache" . DS . "smarty_configs" . DS;
        $this->_smarty->cache_dir     =  $base . "cache" . DS . "smarty_cache" . DS;
        
        $this->setTemplate($template);
        
        $this->assign_array(array(
            "base"   =>  _BASE,
            "url"    =>  _PAGE,
            "admin"  =>  (defined("ADMIN_URL") ? ADMIN_URL ."/" : "")
        ));
        
        if (defined("AUTH_SESSION") && defined("AUTH_MODEL")) {
            $authModel = AUTH_MODEL;
            if (isset($_SESSION[AUTH_SESSION]) && $_SESSION[AUTH_SESSION] instanceof $authModel) {
                $this->assign("current_user", $_SESSION[AUTH_SESSION]);
            }
        }
        
        @session_start();
        if (isset($_SESSION["Smarty"]) && is_array($_SESSION["Smarty"])) {
            $this->assign_array($_SESSION["Smarty"]);
        }
    }
    
    /**
     * Defines the template to use.
     *
     * @param  string  $template
     */
    public function setTemplate($template)
    {
        if (!file_exists($this->_smarty->template_dir . $template)) {
            $template = realpath(dirname(__FILE__)) . DS . "views" . DS . $template;
        }
        
        $this->_template = $template;
    }
    
    /**
     * Defines the layout to use. A layout is wrapped around the template. The layout template
     * should contain a {$content} variable where the template content should be inserted.
     *
     * @param  string|null  $layout  The layout template to use or NULL to not use a layout.
     */
    public function setLayout($layout=NULL)
    {
        if ($layout != NULL && !file_exists($this->_smarty->template_dir . $layout)) {
            $layout = realpath(dirname(__FILE__)) . DS . "views" . DS . $layout;
        }
        $this->_layout = $layout;
    }
    
    /**
     * Returns the layout template.
     * @return  string|null  Returns the layout template filename or NULL if no layout is set.
     */
    public function getLayout()
    {
        return $this->_layout;
    }
    
    /**
     * Calls Smarty's assign() method to assign a variable to the template.
     *
     * @see Smarty::assign()
     */
    public function assign($tpl_var, $value=NULL, $nocache=FALSE, $scope=SMARTY_LOCAL_SCOPE)
    {
        $this->_smarty->assign($tpl_var, $value, $nocache, $scope);
    }
    
    /**
     * Assigns an array of variables to the template.
     *
     * @param  array  $array  An array of variables.
     */
    public function assign_array($array)
    {
        foreach ($array as $key => $value) $this->_smarty->assign($key, $value);
    }
    
    /**
     * Assigns a variable to the session so that it is available to the next template that is
     * initiated.
     *
     * @param  string  $key  The variable name.
     * @param  mixed  $value  The variable value.
     */
    public static function assign_session($key, $value)
    {
        @session_start();
        $_SESSION["Smarty"][$key] = $value;
    }
    
    /**
     * Returns the processed template or layout.
     *
     * @return  string
     */
    public function output()
    {
        @session_start();
        unset($_SESSION["Smarty"]);
        
        if ($this->_layout != NULL) {
            
            $content = $this->_smarty->fetch($this->_template);
            $this->assign("content", $content);
            
            return $this->_smarty->fetch($this->_layout);
            
        }
        
        return $this->_smarty->fetch($this->_template);
    }
}