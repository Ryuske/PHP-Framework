<?php
/**
* @Author: Kenyon Haliwell
* @URL: http://khdev.net/
* @Date Created: 2/21/11
* @Date Modified: 11/27/13
* @Purpose: Used to load the appropriate controller
* @Version: 2
*/

/**
* @Purpose: Router class, used to load the appropriate controller
*
* USAGE:
*  initialize the router
*      $sys->router = new router($sys);
*
*  initialize the controller path
*      $sys->router->controller_path(__SITE_PATH . 'controller');
*
*  Load the route to the controller
*      $sys->router->load_route();
*/
class router
{
  /**
  * @Var: Object
  * @Access: Public
  */
  public $sys;

  /**
  * @Var: String
  * @Access: Private
  */
  private $_controllerPath;

  /**
  * @Var: String
  * @Access: Private
  */
  private $_fileName;

  /**
  * @Var: String
  * @Access: Private
  */
  private $_sharedName;

  /**
  * @Var: String
  * @Access: Private
  */
  private $_routeController;

  /**
  * @Var: String
  * @Access: Private
  */
  private $_routeAction;

  /**
  * @Var: Array
  * @Access: Private
  */
  private $_routeArguments = array();

  /**
  * @Purpose: Load dependencyInjector into scope
  * @Param: object $sys
  * @Access: Public
  */
  public function __construct()
  {
    global $sys;
    $this->sys = $sys;
  }//End __construct

  /**
  * @Purpose: Define the path to the controller
  * @Param: string $controller_path
  * @Access: Public
  */
  public function controller_path($controller_path)
  {
    if (!is_dir($controller_path)) {
    $this->sys->error->trigger_error('Controller path not found: ' . $controller_path, 'Router');
    }

    $this->_controllerPath = $controller_path;
  }//End controller_path

  /**
  * @Purpose: Get route options. Controller file, controller action, action arguments
  * @Access: Private
  */
  private function get_route()
  {
    /**** Logic ****
     *
     *  if first is class or directory
     *      if first is class, two is method
     *      if first is directory two is class/directory and class could be the same???
     *  else first is method
     *      two would be parameters
     *
     ***************/
    
    if (!empty($_GET['route'])) {
      $route = $_GET['route'];
    } else {
      $route = '';
    }
    
    $home_aliases = array('', 'home', 'index', 'main');
    $route = preg_replace('/\/{1}$/', '', $route); //Removes trailing / if it exists; Trailing backslash creates odd behavior
    $route_build = explode('/', (string) $route);
    
    $directory = array_shift($route_build);

    //Checks to see if the first route is a directory or not
    if (
        !is_dir($this->_controllerPath . DIRECTORY_SEPARATOR . $directory)
        && !is_dir(__APPLICATIONS_PATH . 'shared' . DIRECTORY_SEPARATOR . 'controller' . DIRECTORY_SEPARATOR . $directory)
    ) {
        $class = $directory;
    } else { //If it was a directory, then loop through until we find the end of the directories
        foreach ($route_build as $next_in_route) {
            if (
                is_dir($this->_controllerPath . DIRECTORY_SEPARATOR . $directory . DIRECTORY_SEPARATOR . $next_in_route)
                || is_dir(__APPLICATIONS_PATH . 'shared' . DIRECTORY_SEPARATOR . 'controller' . DIRECTORY_SEPARATOR . $directory . DIRECTORY_SEPARATOR . $next_in_route)
            ) {
                $directory .= DIRECTORY_SEPARATOR . array_shift($route_build);
            }
        }
        $class = array_shift($route_build);
    }
    
    if ( //Check to see if $class is actually a class, or if it's a method
        !is_readable($this->_controllerPath . DIRECTORY_SEPARATOR . $directory . DIRECTORY_SEPARATOR . $class . '.php')
        && !is_readable(__APPLICATIONS_PATH . 'shared' . DIRECTORY_SEPARATOR . 'controller' . DIRECTORY_SEPARATOR . $directory . DIRECTORY_SEPARATOR . $class . '.php')
    ) {
        $method = $class;
        $class = 'home';
    } else {
        if (in_array($class, $home_aliases)) {
            $method = 'index';
        } else {
            $method = array_shift($route_build);
        }
    }
    //Make blank methods, 'index', 'home' & 'main' all take you to the home page
    $method = (in_array($method, $home_aliases)) ? 'index': $method;
    $parameters = $route_build;
    /*echo 'Dir: ' . $directory . '<br />';
    echo 'Class: ' . $class . '<br />';
    echo 'Method: ' . $method . '<br />';
    echo 'Params: ' . $parameters . '<br />';
    die();*/
    $this->_routeController = (empty($directory)) ? $class : $directory . DIRECTORY_SEPARATOR . $class;
    $this->_routeAction = $method;
    $this->_routeArguments = $parameters;
    
    $this->_fileName = $this->_controllerPath . DIRECTORY_SEPARATOR . $this->_routeController . '.php';
    $this->_sharedName = __APPLICATIONS_PATH . 'shared' . DIRECTORY_SEPARATOR . 'controller' . DIRECTORY_SEPARATOR . $this->_routeController . '.php';
  }//End get_route

  /**
  * @Purpose: Gets the number of required parameters of the action defined by $this->_routeAction
  * @Param: object $controller
  * @Access: Private
  * @Return: Integer containing the number of required arguments
  */
  private function get_number_of_required_parameters($controller)
  {
    $controller = new ReflectionMethod($controller, $this->_routeAction);
    return $controller->getNumberOfRequiredParameters();
  }//End get_number_of_required_parameters

  /**
  * @Purpose: Sets all required arguments that are missing to null
  * @Param: object $controller
  * @Access: Private
  * @Return: true
  */
  private function set_missing_arguments($controller)
  {
    $missing_arguments = $this->get_number_of_required_parameters($controller) - count($this->_routeArguments);

    if (0 < $missing_arguments) {
      for ($i=0; $i<$missing_arguments; $i++) {
        $this->_route_arguments[] = NULL;
      }
    }
    
    return true;
  }//End set_missing_arguments

  /**
  * @Purpose: Used to render a 404 HTTP Response error
  * @Param: object &$controller
  * @Param: string $type
  * @Access: Public
  * @Return: Boolean
  *
  * if $type is 'view' then bypass checking the controller.
  */
  public function call_404(&$controller=NULL, $type=NULL)
  {
    $controller = (is_string($controller)) ? str_replace('/', '_', $controller) : $controller;
    
    if ((is_readable($this->_fileName) || is_readable($this->_sharedName)) || isset($controller) && $type !== 'view') {
      //Check to see if the requested page is an existing method. May have to add is_callable() at some point - however that errors when trying to call an uninitialized method/view
      if (is_object($controller) || method_exists($controller, $this->_routeAction) || (class_exists($controller) && method_exists(new $this->_routeController($this->sys), $this->_routeAction))) {
        return false;
      } else {
        //$this->_fileName = $this->_controllerPath . DIRECTORY_SEPARATOR . 'main.php';
        if (!is_readable($this->_fileName)) {
          //$this->_sharedName = __APPLICATIONS_PATH . 'shared' . DIRECTORY_SEPARATOR . 'controller' . DIRECTORY_SEPARATOR . 'main.php';
          
          if (!is_readable($this->_fileName) /*&& is_readable($this->_sharedName)*/) {
            include_once $this->_sharedName;
          } else {
            return true;
          }
        } else {
          include_once $this->_fileName;
        }
        
        if (class_exists('home') && is_callable(array(new main($this->sys), $this->_routeController))) {
          $this->_routeArguments = array_merge((array) $this->_routeAction, $this->_routeArguments);
          $this->_routeAction = $this->_routeController;
          $this->_routeController = 'home';
          return false;
        }
      }
    }

    $this->_fileName = $this->_controllerPath . DIRECTORY_SEPARATOR . 'userErrors.php';

    if (!is_readable($this->_fileName)) {
      $this->_sharedName = __APPLICATIONS_PATH . 'shared' . DIRECTORY_SEPARATOR . 'controller' . DIRECTORY_SEPARATOR . 'userErrors.php';
    }
    $this->_routeController = 'userErrors';
    $this->_routeAction = 'index';
    $this->_routeArguments = NULL;

    if (is_readable($this->_fileName)) {
      include_once $this->_fileName;
    } elseif (is_readable($this->_sharedName)) {
      include_once $this->_sharedName;
    } else {
      $error = $this->sys->error->trigger_error('Cannot get 404 page. Unable to read: ' . $this->_fileName . ' or ' . $this->_sharedName, 'Router');
    }

    if ($type === 'view') {
      $this->load_route('skip_to_view');
    }

    if(isset($error)) {
      return true;
    } else {
      $controller = new $this->_routeController($this->sys);
      return false;
    }
  }//End call_404

  /**
  * @Purpose: Loads the route to the controller
  * @Access: Public
  */
  public function load_route($action=NULL)
  {
    if ($action !== 'skip_to_view') {
      $this->get_route();
      
      if (is_readable($this->_fileName)) {
        include_once $this->_fileName;
      } elseif(is_readable($this->_sharedName)) {
        include_once $this->_sharedName;
      }

      if (!$this->call_404($this->_routeController)) {
        $controller = new $this->_routeController($this->sys);
      }
    }
    
    if (!$this->call_404($controller)) {
      $this->set_missing_arguments($controller);
    }

    if (empty($this->_routeArguments)) {
      call_user_func(array($controller, $this->_routeAction), NULL);
    } elseif (!is_array($this->_routeArguments)) {
      call_user_func(array($controller, $this->_routeAction), $this->_routeArguments);
    } else {
      call_user_func_array(array($controller, $this->_routeAction), $this->_routeArguments);
    }
  }//End load_route
}//End router

//End File
