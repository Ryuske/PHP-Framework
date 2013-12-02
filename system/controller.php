<?php
/**
* @Author: Kenyon Haliwell
* @URL: http://khdev.net/
* @Date Created: 2/21/11
* @Date Modified: 12/2/13
* @Purpose: Controller class; used for loading models
* @Version: 2
*/

/**
* @Purpose: Controller class
* @Abstract
*
* USAGE:
*  Loading a model
*      In a class extending this one (controller) use:
*      $hellowWorld = $this->load_model('helloWorld');
*      echo $hellowWorld->helloWorld();
*/
abstract class controller {
  /**
  * @Var: Object
  * @Access: Protected
  */
  protected $sys;
  
  /**
  * @Purpose: Load dependencyInjector into scope
  * @Param: object $sys
  * @Access: Public
  */
  public function __construct($sys) {
    $this->sys = $sys;
  }//End __construct
  
  /**
  * @Purpose: Used to load models into the controller
  * @Param: string $model
  * @Param: string $path
  * @Access: Public
  * @Return: The model class as an object
  */
  public function load_model($model, $path='') {
    $model_path = str_replace('_', DIRECTORY_SEPARATOR, $model);
    
    if (!empty($path)) {
      $model_path = $path . DIRECTORY_SEPARATOR . $model_path;
    }
    
    $site_model = __SITE_PATH . 'model' . DIRECTORY_SEPARATOR . $model_path . '.php';
    $shared_model = __APPLICATIONS_PATH . 'shared' . DIRECTORY_SEPARATOR . 'model' . DIRECTORY_SEPARATOR . $model_path . '.php';
    
    if (is_readable($site_model)) {
      include_once $site_model;
    } elseif (is_readable($shared_model)) {
      include_once $shared_model;
    } else {
      $this->sys->error->trigger_error('Cannot load model (' . $model_path . '). Unable to read: ' . $site_model . ' or ' . $shared_model, 'Model');
    }
    
    $model_class = 'model_' . $model;
    return new $model_class;
  }//End load_model
  
  /**
  * @Purpose: Require that controllers have an index function
  * @Abstract
  */
  abstract function index();
  
  /**
  * @Purpose: Disable controllers from using the method name main since it is an alias of index
  * @Final
  */
  final function main() {}
  
  /**
  * @Purpose: Disable controllers from using the method name home since it is an alias of index
  * @Final
  */
  final function home() {}
}//End controller

//End File
