<?php
/**
 * @Author: Kenyon Haliwell
 * @URL: http://battleborndevelopment.com/
 * @Date Created: 2/22/11
 * @Date Modified: 2/22/11
 * @Purpose: To show an example of a model
 * @Version: 1.0
 */

/**
 * @Purpose: Example of a model
 *
 * USAGE:
 *  To use the model:
 *      Within your controller, use:
 *      $hellowWorld = $this->load_model('helloWorld');
 *      $this->system_di->template->helloWorld = $hellowWorld->helloWorld();
 *
 *      In the view:
 *          {helloWorld}
 */
class model_helloWorld
{
    /**
     * @Purpose: Simple function to show basic model usage
     * @Access: Public
     */
    public function helloWorld()
    {
        return 'Hello World!';
    }//end helloWorld
}//End model_helloWorld

//End File
