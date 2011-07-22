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
    public function __construct($template, $dir="")
    {
        @header("Content-Type: text/html;charset=utf-8");
        
        $this->_smarty = new Smarty();
        
        if ($dir != "" && substr($dir, -1) != DS) $dir .= DS;
        
        if ($dir != "" && defined("DIR_USERVIEWS")) {
            $this->_smarty->template_dir = DIR_USERVIEWS . DS . $dir;
        } else {
            $this->_smarty->template_dir = DIR_VIEWS . DS . $dir;
        }
        
        $base = realpath(dirname(__FILE__)) . DS;
        
        $this->_smarty->compile_dir   =  $base . "cache" . DS . "smarty_compiled" . DS;
        $this->_smarty->config_dir    =  $base . "cache" . DS . "smarty_configs" . DS;
        $this->_smarty->cache_dir     =  $base . "cache" . DS . "smarty_cache" . DS;
        
        $this->setTemplate($template);
        
        $this->assign_array(array(
            "base"        =>  _BASE,
            "base_http"   =>  _BASE_HTTP,
            "base_https"  =>  _BASE_HTTPS,
            "here"        =>  _PAGE,
            "admin"       =>  (defined("ADMIN_URL") ? ADMIN_URL ."/" : "")
        ));
        
        $session = Session::getInstance();
        $this->assign("current_user", $session->getUser());
        
        $session = Session::getInstance();
        if ($session->getSmarty() && is_array($session->getSmarty())) {
            $this->assign_array($session->getSmarty());
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
        } else {
            $template = _PATH . $this->_smarty->template_dir . $template;
        }
        
        $this->_template = $template;
    }
    
    /**
     *
     */
    public function getTemplate()
    {
        return $this->_template;
    }
    
    /**
     * Defines the layout to use. A layout is wrapped around the template. The layout template
     * should contain a {$content} variable where the template content should be inserted.
     *
     * The path is relative to the template directory.
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
        $session = Session::getInstance();
        $vars = $session->getSmarty();
        $vars[$key] = $value;
        $session->setSmarty($vars);
    }
    
    /**
     * Returns the processed template or layout.
     *
     * @return  string
     */
    public function output()
    {
        $session = Session::getInstance();
        $session->unsetSmarty();
        
        if ($this->_layout != NULL) {
            
            $this->assign("template", $this->_template);
            
            $content = $this->_smarty->fetch($this->_template);
            $this->assign("content", $content);
            
            return $this->_smarty->fetch($this->_layout);
            
        }
        
        return $this->_smarty->fetch($this->_template);
    }
}
