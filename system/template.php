<?php
/**
 * @Author: Kenyon Haliwell
 * @URL: http://khdev.net/
 * @Date Created: 2/22/11
 * @Date Modified: 12/4/13
 * @Purpose: Template class used to parse views
 * @Version: 2.5
 */

/**
 * @Purpose: Used to parse views
 *
 * USAGE:
 *  Initializing template
 *      $sys->template = new template($sys);
 *
 *  Storing & retrieving variables to template
 *      $sys->template->somevar = 'Some value';
 *      To access somevar in a view, use {somevar}
 *      *note* {somevar} does NOT need to be wrapped in PHP
 */
class template {
  /**
  * @Var: Object
  * @Access: Protected
  */
  protected $sys;

  /**
  * @Var: String
  * @Access: Private
  */
  private $_path_to_view = '';

  /**
  * @Var: String
  * @Access: Boolean
  */
  private $_404 = false;

  /**
  * @Var: Array
  * @Access: Private
  */
  private $_variables = array();

  /**
  * @Purpose: Load dependencyInjector into scope
  * @Param: object $sys
  * @Access: Public
  */
  public function __construct() {
    global $sys;
    $this->sys = $sys;
  }//End __construct

  /**
  * @Purpose: Enables object-like ability to set variables
  * @Param: string $key
  * @Param: string $value
  * @Access: Public
  */
  public function __set($key, $value) {
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
   * @Purpose: Enable checking if a variable is set or not
   * @Param: string $key
   * @Access: Public
   */
  public function __isset($key) {
    return isset($this->_variables[$key]);
  }
  
  /**
  * @Purpose: Parse the view
  * @Param: string $view
  * @Param: [Boolean $return]
  * @Access: Public
  * @Return: HTML contents of view, or true
  */
  public function parse($view, $return=false) {
    $this->view_path($view);

    if (!$this->_404) {
      $view_html = $this->parse_tokens();

      if (true === $return) {
        return $view_html;
      } else {
        echo $view_html;
        return true;
      }
    }
  }//End parse

  /**
  * @Purpose: Figure out view to load
  * @Param: (string) $view
  * @Access: Protected
  */
  protected function view_path($view) {
    $view = str_replace('_', DIRECTORY_SEPARATOR, $view);
    $path_to_view = __SITE_PATH . 'view' . DIRECTORY_SEPARATOR . $view . '.php';

    if (!is_readable($path_to_view)) {
      $path_to_shared_view = __APPLICATIONS_PATH . 'shared' . DIRECTORY_SEPARATOR . 'view' . DIRECTORY_SEPARATOR . $view . '.php';
      if (!is_readable($path_to_shared_view)) {
        $pseudo_controller = NULL;
        $this->sys->router->call_404($pseudo_controller, 'view');
        $this->_404 = true;
      } else {
        $path_to_view = $path_to_shared_view;
      }
    }

    $this->_path_to_view = $path_to_view;
  } //End view_path

  /**
  * @Purpose: Parse tokens out of view
  * @Access: Protected
  * @Return: HTML contents of view
  */
  protected function parse_tokens() {
    ob_start();
    include_once $this->_path_to_view;
    $view_html = ob_get_contents();
    ob_end_clean();

    foreach($this->_variables as $key => $value) {
      if (is_array($value)) {
        $$key = $value;
        $tokens[$key] = $$key;
      }

      $$key = $value;

      if (!is_object($$key)) {
        $tokens[] = $key;
      }
    }

    if (!empty($tokens)) {
      foreach ($tokens as $key=>$token) {
        preg_match_all("/({.*})/iUs", $view_html, $matches);
        $matches = (is_array($matches[0])) ? $matches[0]: $matches;
        foreach ($matches as $match) {
          $token_string = str_replace(array('{', '}'), '', $match);
          preg_match_all("/\[.*\]/iUs", $token_string, $array_match);
          preg_match_all("/[^\[]*/", $token_string, $array_to_check);

          if (!empty($array_match[0]) && is_array($token) && $array_to_check[0][0] == $key) {
            $variable = $token;
            $array_match = $array_match[0];

            foreach ($array_match as $item) {
              $item = str_replace(array('[', ']', '\''), '', $item);

              if (is_array($variable) && array_key_exists($item, $variable)) {
                $variable = $variable[$item];
              }
            }

            if ('dev' === __PROJECT_ENVIRONMENT && is_array($variable)) {
              $view_html = str_replace($match, print_r($variable, True), $view_html);
            } elseif (!is_array($variable)) {
              $view_html = str_replace($match, $variable, $view_html);
            } else {
              $view_html = str_replace($match, '', $view_html);
            }
          } elseif (!is_array($token) && !is_array($$token) && !is_object($$token)) {
            $view_html = str_replace('{' . $token . '}', (string) $$token, $view_html);
          }
        }
      }
    }

    return $view_html;
  } //End parse_tokens
}//End template

//End File
