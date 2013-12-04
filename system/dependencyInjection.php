<?php
/**
 * @Author: Kenyon Haliwell
 * @URL: http://khdev.net/
 * @Date Created: 2/21/11
 * @Date Modified: 12/4/13
 * @Purpose: Used to store and retrieve object references
 * @Version: 2.5
 */

/**
 * @Purpose: dependencyInjection class, used to store and retrieve object references
 *
 * USAGE:
 *  Creating a new injection
 *      $data = array('Greetings' => array('ohayo', 'konnichiwa', 'oyasumi'));
 *      $sys = new dependencyInjection();
 *
 *  Setting an object
 *      $sys->Japanese_Greetings = $data;
 *
 *  Retrieve and object
 *      $japanese_greetings = $sys->Japanese_Greetings;
 *
 */
class dependencyInjection {
  /**
  * @Purpose: Private constructor: Only allow 1 instance
  * @Access: Private
  * @Final
  */
  final public function __construct() {}
  
  /**
  * @Purpose: Disallow cloning of dependencyInjection
  * @Access: Private
  * @Final
  */
  final private function __clone() {}
  
  /**
  * @Purpose: Used for debugging, print out everything stored in $_storeObjects
  * @Access: Public
  */
  public function dump() {
    if ('dev' === __PROJECT_ENVIRONMENT) {
      echo '<fieldset class="system_alert"><legend>Current DI Values</legend><pre>' . print_r($this, true) . '</pre></fieldset>';
    }
  }//End dump
}//End dependencyInjection

//End File
