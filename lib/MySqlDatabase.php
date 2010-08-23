<?php
/**
 * MySQL Database Connection
 * 
 * @package core
 */

/**
 * MySqlDatabase class
 */
class MySqlDatabase
{
    /**
     * Constructor
     *
     * This is private so that it's not possible to call new MySqlDatabase() - use
     * MySqlDatabase::getInstance() instead.
     *
     * @access private
     */
    private function __construct()
    {
        
    }
    
    /**
     * Get MySQL Database Instance
     *
     * There is only ever one MySQL database instance - use this to get the active instance.
     *
     * @access public
     * @static
     */
    public static function &getInstance()
    {
        static $instance;
        if (!is_object($instance)) {
            
            try {
                $instance = new PDO("mysql:dbname=" . MYSQL_DB_NAME . ";host=" . MYSQL_HOST . ";port=" . MYSQL_PORT, MYSQL_USERNAME, MYSQL_PASSWORD);
                $instance->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
            } catch (PDOException $e) {
                throw new Exception("Database connection failed: " . $e->getMessage());
            }
            
        }
        return $instance;
    }
    
}
