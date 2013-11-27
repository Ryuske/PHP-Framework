<?php
/**
 * @Author: Kenyon Haliwell
 * @URL: http://khdev.net/
 * @Date Created: 2/20/11
 * @Date Modified: 11/27/13
 * @Purpose: Implements site configurations into objects usable by the framework
 * @Version: 2
 */

/**
 * @Purpose: Store && Retrieve configuration values
 *
 * USAGE:
 *  Initializing the configs
 *      $sys->config = configuration::initialize();
 *
 *  Getting values
 *      echo $sys->config->some_value;
 *
 *  TODO: Add support for multidimensional arrays
 */
class configuration
{

    /**
     * @Var: Object
     * @Access: Private
     * @Static
     */
    public static $_configInstance;

    /**
     * @Var: String
     * @Access: Private
     * @Static
     */
    public static $_configFile;

    /**
     * @Var: Array
     * @Access: Private
     * @Static
     */
    public static $_configValues;

    /**
     * @Purpose: Privare constructor: only allow 1 instance
     * @Access: Private
     * @Final
     */
    public final function __construct()
    {
        $shared_configValues = __CONFIG_PATH . 'shared.php';
        $site_configValues = __CONFIG_PATH . self::$_configFile . '.php';
        if (is_readable($shared_configValues)) {
            $shared_configValues = include_once $shared_configValues;
        } else {
            $shared_configValues = array();
        }
        if (is_readable($site_configValues)) {
            $site_configValues = include_once $site_configValues;
        } else {
            $site_configValues = array();
        }
        if (is_array($shared_configValues) || is_array($site_configValues) ) {
            self::$_configValues = array_merge($shared_configValues, $site_configValues);
        } else {
            if ('dev' === __PROJECT_ENVIRONMENT) {
                echo '<fieldset class="system_alert"><legend>Config Error</legend>Error Loading Config Files<hr />' . $shared_configValues . ' or ' . $site_configValues . '</fieldset>';
            }
        }
    }//End __construct

    /**
     * @Purpose: Disallow cloning of configuration class
     * @Access: Private
     * @Final
     */
    final private function __clone() {}

    /**
     * @Purpose: Enables object-like ability to get config values
     * @Param: string $key
     * @Access: Public
     * @Return: If found, return the referenced value, otherwise null
     */
    public function __get($key)
    {
        return array_key_exists($key, self::$_configValues) ? self::$_configValues[$key] : NULL;
    }//End __get

    /**
     * @Purpose: Initialize unique instance of $_configInstance
     * @Access: Public
     * @Access: Static
     * @Return: Object $_configInstance
     */
    public static function initialize()
    {
        if (NULL === self::$_configInstance) {
            $class = __CLASS__;
            self::$_configInstance = new $class;
        }

        return self::$_configInstance;
    }//End initialize

    /**
     * @Purpose: Set the path location of the config file
     * @Access: Public
     * @Access: Static
     * @Return: True
     */
    public static function set_file($file_name)
    {
        self::$_configFile = $file_name;
        return true;
    }//End set_file

    /**
     * @Purpose: Used for debugging, print out everything stored in $_configValues
     * @Access: Public
     */
    public function dump()
    {
        if ('dev' === __PROJECT_ENVIRONMENT) {
            echo '<fieldset class="system_alert"><legend>Current Config Values</legend><pre>', print_r(self::$_configValues, true), '</pre></fieldset>';
        }
    }//End dump
}//End configuration

//End File
