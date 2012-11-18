<?php
/**
 * @Author: Kenyon Haliwell
 * @URL: http://battleborndevelopment.com/
 * @Date Created: 2/21/11
 * @Date Modified: 3/5/11
 * @Purpose: Default controller for a website
 * @Version: 1.0
 */

/**
 * @Purpose: Default controller for a website
 * @Extends controller
 */
class main extends controller
{
    /**
     * @Purpose: Default function to be run when class is called
     * @Access: Public
     */
    public function index()
    {
        //Loads the model helloWorld
        $hellowWorld = $this->load_model('helloWorld');
        
        //Sets a template variable (used via {} inside a view)
        $this->system_di->template->helloWorld = $hellowWorld->helloWorld();
        $this->system_di->template->title = 'Some Title';
        
        //Parses the HTML from the view
        $this->system_di->template->parse('main');
    }//End index
}//End main

//End File
