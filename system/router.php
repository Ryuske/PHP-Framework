<?php
/**
* @Author: Kenyon Haliwell
* @URL: http://battleborndevelopment.com/
* @Date Created: 2/21/11
* @Date Modified: 7/22/11
* @Purpose: Used to load the appropriate controller
* @Version: 1.1.2
*/

/**
* @Purpose: Router class, used to load the appropriate controller
*
* USAGE:
*  initialize the router
*      $system_di->router = new router($system_di);
*
*  initialize the controller path
*      $system_di->router->controller_path(__SITE_PATH . 'controller');
*
*  Load the route to the controller
*      $system_di->router->load_route();
*/
class router
{
  /**
  * @Var: Object
  * @Access: Public
  */
  public $system_di;

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
  * @Param: object $system_di
  * @Access: Public
  */
  public function __construct($system_di)
  {
    $this->system_di = $system_di;
  }//End __construct

  /**
  * @Purpose: Define the path to the controller
  * @Param: string $controller_path
  * @Access: Public
  */
  public function controller_path($controller_path)
  {
    if (!is_dir($controller_path)) {
    $this->system_di->error->trigger_error('Controller path not found: ' . $controller_path, 'Router');
    }

    $this->_controllerPath = $controller_path;
  }//End controller_path

  /**
  * @Purpose: Get route options. Controller file, controller action, action arguments
  * @Access: Private
  */
  private function get_route()
  {
    if (!empty($_GET['route'])) {
      $route = $_GET['route'];
    } else {
      $route = 'main';
    }

    $route_build = explode('/', (string)$route);
    $this->_routeController = array_shift($route_build);

    $this->_routeController = preg_replace('/\/{1}$/', '', $this->_routeController);
    while (is_dir($this->_controllerPath . DIRECTORY_SEPARATOR . $this->_routeController . DIRECTORY_SEPARATOR) || is_dir(__APPLICATIONS_PATH . 'shared' . DIRECTORY_SEPARATOR . 'controller' . DIRECTORY_SEPARATOR . $this->_routeController . DIRECTORY_SEPARATOR)) {
      $this->_routeController = $this->_routeController . '_' . array_shift($route_build);
    }

    $this->_routeAction = array_shift($route_build);
    $this->_routeArguments = $route_build;

    if (empty($this->_routeController) || substr($this->_routeController, -1) === '_') {
      $this->_routeController = $this->_routeController . 'main';
    }
    if (empty($this->_routeAction)) {
      $this->_routeAction = 'index';
    }

    $this->_fileName = $this->_controllerPath . DIRECTORY_SEPARATOR . str_replace('_', DIRECTORY_SEPARATOR, $this->_routeController) . '.php';
    $this->_sharedName = __APPLICATIONS_PATH . 'shared' . DIRECTORY_SEPARATOR . 'controller' . DIRECTORY_SEPARATOR . str_replace('_', DIRECTORY_SEPARATOR, $this->_routeController) . '.php';
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
    if ((is_readable($this->_fileName) || is_readable($this->_sharedName)) || isset($controller) && $type !== 'view') {
      if (is_callable(array($controller, $this->_routeAction)) || (class_exists($controller) && is_callable(array(new $this->_routeController($this->system_di), $this->_routeAction)))) {
        return false;
      } else {
        $this->_fileName = $this->_controllerPath . DIRECTORY_SEPARATOR . 'main.php';
        if (!is_readable($this->_fileName)) {
          $this->_sharedName = __APPLICATIONS_PATH . 'shared' . DIRECTORY_SEPARATOR . 'controller' . DIRECTORY_SEPARATOR . 'main.php';
          if (!is_readable($this->_fileName)) {
            include_once $this->_sharedName;
          }
        } else {
          include_once $this->_fileName;
        }
        if (is_callable(array(new main($this->system_di), $this->_routeController))) {
          $this->_routeArguments = array_merge((array) $this->_routeAction, $this->_routeArguments);
          $this->_routeAction = $this->_routeController;
          $this->_routeController = 'main';
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
      $error = $this->system_di->error->trigger_error('Cannot get 404 page. Unable to read: ' . $this->_fileName . ' or ' . $this->_sharedName, 'Router');
    }

    if ($type === 'view') {
      $this->load_route('skip_to_view');
    }

    if(isset($error)) {
      return true;
    } else {
      $controller = new $this->_routeController($this->system_di);
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
        $controller = new $this->_routeController($this->system_di);
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
