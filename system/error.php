<?php
/**
 * @Author: Kenyon Haliwell
 * @URL: http://khdev.net/
 * @Date Created: 2/21/11
 * @Date Modified: 12/4/13
 * @Purpose: Replaces PHPs error handling for all E_ level errors
 * @Version: 2.5
 */

/**
 * @Purpose: Replaces error handling for all E_USER errors
 *
 * USAGE:
 *  Enabling it
 *      set_error_handler(array('error', 'handle_error'));
 *      $sys->error = new error;
 *
 *  Throw an error
 *      $sys->error->trigger_error('Some error', 'Error Category');
 */
class error {
  /**
  * @Var: Object
  * @Access: Protected
  */
  protected $sys;
  
  /**
  * @Purpose: Load dependencyInjector into scope
  * @Access: Public
  */
  public function __construct() {
    global $sys;
    $this->sys = $sys;
  }//End __construct
  
  /**
  * @Purpose: Allow debug_print_backtrace() to be used in a string
  * @Access: Public
  * @Return: Array containing backtrace information
  */
  public function debug_string_backtrace() {
    ob_start();
    debug_print_backtrace();
    $trace = ob_get_contents();
    ob_end_clean();
    
    /**
    * Remove first backtrace, which is this function and reorder them to start from 0 again
    */
    $trace = preg_replace ("/(\#0)(.*)[" . __FUNCTION__ . "](.*)/", '', $trace);
    $trace = preg_replace ('/^#(\d+)/me', '\'#\' . ($1 - 1)', $trace);
    
    return $trace;
  }//End debug_string_backtrace
  
  /**
  * @Purpose: Allow debug__backtrace() to be used in a string
  * @Access: Public
  * @Return: Array containing backtrace information
  */
  public function debug_backtrace() {
    $trace = debug_backtrace();
    array_shift($trace);
    $trace = print_r($trace, true);
    $trace = preg_replace("/.config([^)])*/s", '', $trace);
    
    
    /**
    * Remove first backtrace, which is this function and reorder them to start from 0 again
    */
    //$trace = preg_replace ("/(.*)/", '', $trace);
    //$trace = preg_replace ('/^#(\d+)/me', '\'#\' . ($1 - 1)', $trace);
    
    return $trace;
  }//End debug_string_backtrace
  
  /**
  * @Purpose: Used to replace PHPs error handler
  * @Param: int $errno
  * @Param: mixed $errstr
  * @Param: string $errfile
  * @Param: int $errline
  * @Access: Public
  * @Static
  */
  public static function handle_error($errno, $errstr, $errfile, $errline) {
    $error_numbers = array(
      '1' => 'E_ERROR',
      '2' => 'E_WARNING',
      '4' => 'E_PARSE',
      '8' => 'E_NOTICE',
      '16' => 'E_CORE_ERROR',
      '32' => 'E_CORE_WARNING',
      '64' => 'E_COMPILE_ERROR',
      '128' => 'E_COMPILE_WARNING',
      '256' => 'E_USER_ERROR',
      '512' => 'E_USER_WARNING',
      '1024' => 'E_USER_NOTICE',
      '2048' => 'E_STRICT',
      '4096' => 'E_RECOVERABLE_ERROR',
      '8192' => 'E_DEPRECATED',
      '16384' => 'E_USER_DEPRECATED',
      '30719' => 'E_ALL'
    );
    
    echo '<div><span style="font-style: italic">' . $error_numbers[$errno] . ': in ' . $errfile . ' on line ' . $errline . '.</span> ' . $errstr . '</div><br /><br />';
  }//End handle_error
  
  /**
  * @Purpose: Report the error, called by handle_error()
  * @Param: string $error_message
  * @Param: [string $error_type]
  * @Access: Public
  * @Return: Either return true, or Boolean from email_error()
  */
  public function trigger_error($error_message = NULL, $error_type = 'Undefined') {
    if ('dev' === __PROJECT_ENVIRONMENT) {
      trigger_error('<fieldset class="system_alert"><legend>' . $error_type . ' Error</legend><pre>' . htmlentities($error_message, ENT_QUOTES, 'UTF-8', false) . '<hr /><h4>PHP Backtrace</h4>' . $this->debug_backtrace() . '<hr /><h4>PHP Functions Backtrace</h4>' . $this->debug_string_backtrace() . '</pre></fieldset>');
      return true;
    } else {
      echo '<fieldset class="system_alert"><legend>Error</legend>There was an error! Details have been email to the admin.</fieldset>';
      return $this->email_error($error_message, $error_type);
    }
  }//End trigger_error
  
  /**
  * @Purpose: Email the error if __PROJECT_ENVIRONMENT is set to production
  * @Param: string $error_message
  * @Param: string $error_type
  * @Access: Public
  * @Return: Boolean
  */
  public function email_error($error_message, $error_type) {
    if (!$this->sys->config->email_errors) {
      return false;
    }
    
    $to = $this->sys->config->admin_name . ' <' . $this->sys->config->admin_email . '>';
    $subject = 'Error (' . $_SERVER['HTTP_HOST'] . '): ' . htmlentities($error_type, ENT_QUOTES, 'UTF-8', false);
    $email_body = '<fieldset class="system_alert"><legend>' . $error_type . ' Error</legend><pre>' . htmlentities($error_message, ENT_QUOTES, 'UTF-8', false) . '<hr /><h4>PHP Backtrace</h4>' . print_r(debug_backtrace(), true) . '<hr /><h4>PHP Functions Backtrace</h4>' . $this->debug_string_backtrace() . '</pre></fieldset>';
    $headers = "Content-Type: text/html; charset=UTF-8\r\nFrom: " . $this->sys->config->errors_from . " <" . $this->sys->config->errors_from_email . ">";
    
    if (mail($to, $subject, $email_body, $headers)) {
      return true;
    } else {
      return false;
    }
  }//End email_error
}//End error

//End File
