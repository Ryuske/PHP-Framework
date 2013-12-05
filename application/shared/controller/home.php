<?php
/**
 * @Author: Kenyon Haliwell
 * @URL: http://khdev.net/
 * @Date Created: 2/21/11
 * @Date Modified: 12/3/13
 * @Purpose: Default controller for a website
 * @Version: 1.0
 */

/**
 * @Purpose: Default controller for a website
 * @Extends controller
 */
class home extends controller
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
        $this->sys->template->helloWorld = $hellowWorld->helloWorld();
        $this->sys->template->title = 'Some Title';

        //Parses the HTML from the view
        $this->sys->template->parse('main');
    }//End index
}//End home

//End File
