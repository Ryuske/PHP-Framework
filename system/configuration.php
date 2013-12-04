<?php
/**
* @Author: Kenyon Haliwell
* @URL: http://khdev.net/
* @Date Created: 2/20/11
* @Date Modified: 12/4/13
* @Purpose: Implements site configurations into objects usable by the framework
* @Version: 2.5
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
*      echo $sys->config->some_array['array_key'];
*/
class configuration {
  /**
  * @Var: Array
  * @Access: Private
  * @Static
  */
  public $_configValues;
  
  /**
  * @Purpose: Private constructor: only allow 1 instance
  * @Access: Private
  * @Final
  */
  public final function __construct($config_file) {
    $shared_configValues = __CONFIG_PATH . 'shared.php';
    $site_configValues = __CONFIG_PATH . $config_file . '.php';
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
      $this->_configValues = array_merge($shared_configValues, $site_configValues);
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
  public function __get($key) {
    return array_key_exists($key, $this->_configValues) ? $this->_configValues[$key] : NULL;
  }//End __get
  
  /**
  * @Purpose: Used for debugging, print out everything stored in $_configValues
  * @Access: Public
  */
  public function dump() {
    if ('dev' === __PROJECT_ENVIRONMENT) {
      echo '<fieldset class="system_alert"><legend>Current Config Values</legend><pre>', print_r($this->_configValues, true), '</pre></fieldset>';
    }
  }//End dump
}//End configuration

//End File
