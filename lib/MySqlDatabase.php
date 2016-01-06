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
     * DB reference
     * @var PDO|NULL
     */
    public static $instance = NULL;

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
     * If no config is passed in we default to use defined constants
     *
     * @access public
     * @static
     * @return PDO $instance A PDO instance representing a connection to a database
     */
    public static function getInstance()
    {
        if (!is_object(self::$instance)) {

            if (!defined("MYSQL_DB_NAME") || !defined("MYSQL_HOST") || !defined("MYSQL_PORT") || !defined("MYSQL_USERNAME") || !defined("MYSQL_PASSWORD")) {
                throw new PDOException('Some or all of MYSQL_* constants not defined.');
            }

            $diverOptions = array();
            if (defined("RUN_SET_NAMES_UTF8_QUERY") && RUN_SET_NAMES_UTF8_QUERY) {
                $diverOptions = array(PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES utf8");
            }

            try {
                self::$instance = new PDO("mysql:dbname=" . MYSQL_DB_NAME . ";host=" . MYSQL_HOST . ";port=" . MYSQL_PORT, MYSQL_USERNAME, MYSQL_PASSWORD, $diverOptions);
                self::$instance->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
            } catch (PDOException $e) {
                throw new Exception("Database connection failed: " . $e->getMessage());
            }

        }
        return self::$instance;
    }

}
