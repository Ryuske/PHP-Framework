<?php
/**
 * @Author: Kenyon Haliwell
 * @URL: http://khdev.net/
 * @Date Created: 2/20/11
 * @Date Modified: 12/4/13
 * @Purpose: Core functions.
 * @Version: 2.5
 */

/**
 * @Purpose: Function to autoload classes.
 * @Param: string $class_name, the name of the class to be loaded
 * @Return: Boolean, true if class is readable
 */
function __autoload($class_name) {
  $class_path = str_replace('_', DIRECTORY_SEPARATOR, $class_name);
  $system_class = __SYSTEM_PATH . $class_path . '.php';
  $plugin_class = __PLUGINS_PATH . $class_path . '.php';
  
  if (is_readable($system_class)) {
    include $system_class;
    return true;
  }
  if (is_readable($plugin_class)) {
    include $plugin_class;
    return true;
  }
  return false;
}//End __autoload

//End File
