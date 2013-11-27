<?php
/**
 * @Author: Kenyon Haliwell
 * @URL: http://khdev.net/
 * @Date Created: 2/21/11
 * @Date Modified: 11/27/13
 * @Purpose: Used to store and retrieve object references
 * @Version: 2
 */

/**
 * @Purpose: dependencyInjection class, used to store and retrieve object references
 *
 * USAGE:
 *  Creating a new injection
 *      $data = array('Greetings' => array('ohayo', 'konnichiwa', 'oyasumi'));
 *      $sys = dependencyInjection::initialize();
 *
 *  Using set() to store object
 *      $sys->set('Japanese Greetings', $data);
 *
 *  Using get() to retrieve object
 *      $japanese_greetings = $sys->get('Japanese Greetings');
 *
 */
class dependencyInjection
{
    /**
     * @Var Object
     * @Access: Private
     * @Static
     */
    private static $_storeObjects;

    /**
     * @Purpose: Private constructor: Only allow 1 instance
     * @Access: Private
     * @Final
     */
    final private function __construct() {}

    /**
     * @Purpose: Disallow cloning of dependencyInjection
     * @Access: Private
     * @Final
     */
    final private function __clone() {}

    /**
     * @Purpose: Initialize unique instance of $_storeObjects
     * @Access: Public
     * @Static
     * @Return: Object $_storeObjects
     */
    public static function initialize()
    {
        if (NULL === self::$_storeObjects) {
            self::$_storeObjects = new self();
        }

        return self::$_storeObjects;
    }//End initialize

    /**
     * @Purpose: Set a new object using $key as the key
     * @Param: string $key
     * @Param: mixed $object
     * @Access: Public
     * @Static
     */
    public static function set($key, $object)
    {
        $instance = self::initialize();
        $instance->$key = $object;
    }//End set

    /**
     * @Purpose: Get the object referenced by $key
     * @Param: string $key
     * @Access: Public
     * @Return: Mixed return, null if key does not reference an object
     */
    public function get($key)
    {
        return array_key_exists($key, $instance) ? $instance->$key : NULL;
    }//End get

    /**
     * @Purpose: Used for debugging, print out everything stored in $_storeObjects
     * @Access: Public
     */
    public function dump()
    {
        if ('dev' === __PROJECT_ENVIRONMENT) {
            echo '<fieldset class="system_alert"><legend>Current DI Values</legend><pre>' . print_r(self::$_storeObjects, true) . '</pre></fieldset>';
        }
    }//End dump
}//End dependencyInjection

//End File
