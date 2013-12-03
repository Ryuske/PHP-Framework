<?php
/**
 * @Author: Kenyon Haliwell
 * @URL: http://khdev.net/
 * @Date Created: 2/21/11
 * @Date Modified: 2/26/14
 * @Purpose: Used to display user defined errors. By default it will display a 404 error.
 * @Version: 1.0
 */

/**
 * @Purpose: Used to display user defined errors. By default it will display a 404 error.
 * @Extends controller
 */
class userErrors extends controller
{
    /**
     * @Purpose: Default function to be run when class is called
     * @Access: Public
     */
    public function index()
    {
        $this->system_di->template->parse('404');
    }//End index
}//End errors

//End File
