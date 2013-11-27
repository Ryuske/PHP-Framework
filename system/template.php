<?php
/**
 * @Author: Kenyon Haliwell
 * @URL: http://khdev.net/
 * @Date Created: 2/22/11
 * @Date Modified: 11/27/13
 * @Purpose: Template class used to parse views
 * @Version: 2
 */

/**
 * @Purpose: Used to parse views
 *
 * USAGE:
 *  Initializing template
 *      $system_di->template = new template($system_di);
 *
 *  Storing & retrieving variables to template
 *      $system_di->template->somevar = 'Some value';
 *      To access somevar in a view, use {somevar}
 *      *note* {somevar} does NOT need to be wrapped in PHP
 */
class template
{
    /**
    * @Var: Object
    * @Access: Public
    */
    public $system_di;

    /**
    * @Var: Array
    * @Access: Private
    */
    private $_variables = array();

    /**
    * @Purpose: Load dependencyInjector into scope
    * @Param: object $system_di
    * @Access: Public
    */
    public function __construct($system_di)
    {
        $this->system_di = $system_di;
    }//End __construct

    /**
     * @Purpose: Enables object-like ability to set variables
     * @Param: string $key
     * @Param: string $value
     * @Access: Public
     */
    public function __set($key, $value)
    {
        $this->_variables[$key] = $value;
    }//End __set

    /**
     * @Purpose: Enabled object-like ability to get variables
     * @Param: string $key
     * @Acess: Public
     */
    public function __get($key) {
        if (array_key_exists($key, $this->_variables)) {    
            return $this->_variables[$key];
        }
        
        return '';
    } //End __get

    /**
    * @Purpose: Parse the view
    * @Param: string $view
    * @Param: [Boolean $return]
    * @Access: Public
    * @Return: HTML contents of view, or true
    */
    public function parse($view, $return=false)
    {
        $view = str_replace('_', DIRECTORY_SEPARATOR, $view);
        $path_to_view = __SITE_PATH . 'view' . DIRECTORY_SEPARATOR . $view . '.php';

        if (!is_readable($path_to_view)) {
            $path_to_shared_view = __APPLICATIONS_PATH . 'shared' . DIRECTORY_SEPARATOR . 'view' . DIRECTORY_SEPARATOR . $view . '.php';
            if (!is_readable($path_to_shared_view)) {
                $pseudo_controller = NULL;
                $this->system_di->router->call_404($pseudo_controller, 'view');
                $_404 = true;
            } else {
                $path_to_view = $path_to_shared_view;
            }
        }

        if (!isset($_404)) {
          foreach($this->_variables as $key => $value) {
              $$key = $value;
              $tokens[] = $key;
          }

          ob_start();
          include_once $path_to_view;
          $view_contents = ob_get_contents();
          ob_end_clean();

          if (!empty($tokens)) {
              foreach ($tokens as $token) {
                  if (!is_object($$token)) {
                    $view_contents = str_replace('{' . $token . '}', (string)$$token, $view_contents);
                  }
              }
          }

          if (true === $return) {
              return $view_contents;
          } else {
              echo $view_contents;
              return true;
          }
        }
    }//End parse
}//End template
//End File
