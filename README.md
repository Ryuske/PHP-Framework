# Index #
### I. [About](#about) ###
### II. [Installation](#installation) ###
### III. [Terminology](#terminology) ###
### IV. [Standards](#standards) ###

# About #
    Author: Kenyon Haliwell
    URL: http://khdev.net/
    Version: 1.1
    
    This framework was created to be very small, flexible and portable. It uses PHP5 coding standards.
    In addition to being small and flexible, it is designed to be a framework and only a framework.
    It is not designed to be a Content Management System, or anything of the like, only a framework.

# Installation #
    Installing the framework is fairly simple. All you have to do is extract the framework somewhere
    non-web accessible. So for example, /var/www/framework and then you're going to move example.com
    to /var/www/mysite.com and you'll have to update line 25 on /var/www/mysite.com/index.php to
    reflect where ever you put it. Try and keep the same format that's there already, just with the
    correct path.
    
    Once you've done that you're going to have to go to your root framework directory (<framework_root>
    is what we'll call it). You're going to move <framework_root>/htaccess_example to
    <framework_root>/.htaccess all this does is make it so that pretty URLs work. Under
    <framework_root>/applications move example.com to mysite.com, under configuration you're going to
    copy, not move, shared.php to mysite.com.php and under mysite.com.php you're going to update the
    settings to reflect your actual information.
    
    After that, you're ready to go. You should be able to go to mysite.com and you should see
    "Hello World".

# Terminology #
    URL (Uniform Resource Locator): This is the domain (i.e. http://google.com)
    URI (Uniform Resource Identifier): This is the file path (i.e. /some/file.php)
    Taxonomy:   The art of classification (taxis meaning order or arrangement;
                nomos meaning law or science)
    Taxa: Taxonomic units
    Taxon: Singular taxa
    
    Use all terminology appropriately! A URL is *NOT* the same as a URI!!

# Standards #
    PHP Should generally abide by PHP5 Standards unless otherwise started (http://pear.php.net/manual/en/).
    All files should be properly tabulated.
    Tabulation should use spaces over tabs. 2 spaces.
    HTML should also be tabulated, each tag should be tabulated correspondingly.
    
    PHP files should omit the closing PHP tag (?>) if the file would normally be ending there and
    instead replaced with the comment //End File and exactly one blank space past that
    
    Never use shortcode PHP: <?=$var?> should be <?php echo $var; ?>
    
    Ternary PHP is acceptable.
    
    When doing logical comparison, the variable should always be to the right
    (i.e. 'large' == $size; 1 > $number). This way if you forget an equals sign, it will error instead
    of returning true.
    
    Classes should start lowercase, and words be separated by uppercase (i.e. myClass)*
    
    Models should be prefixed with model_ and start lowercase, and words be seperated by uppercase (i.e. model_myModel)*
    
    Functions should be all lowercase, and words be separated by an underscore (i.e. my_function())
    
    Variables should also be lowercase and separated by an underscore (i.e. $some_var)
    
    Globals should be all uppercase, start with a double underscore, and words be separated by an underscore(i.e. __GLOBAL_FUNCTION)
    
    System variables are denoted with system_ (i.e. $system_di would be the variables for the Dependency Injector)
    
    File names should be the same as the class name
    If you wish to have directories under controller, model or view, they need to be formatted the same way as a class. (i.e. myFolder)
    *Classes and models under a folder should be named like this (myFolder_fileName)
    
    Variables defined in a class but outside of a function should precede with an underscore and words be separated by
    uppercase (i.e. private $_someVar), unless you're extending viability of an already existing variable (i.e. private $system_di)
    
    All code should be commented to easily be able to tell where a function starts, ends, and anything that can not be
    deciphered at a quick glance should also be commented.
    Classes and functions that you cannot see the end of should end with a comment: //End <class/function name>
    
    The only PHP that should exist in views is tokens ({some token}) or simple statements (if, switch, for, etc)
    
    All PHP files should have the following at the very top:
    /**
     * @Author: Author name
     * @URL: URL of author or project
     * @Date Created: Date the file was created (month/day/year, 2/20/11)
     * @Date Modified: The date the file was last modified (same as @Date Created)
     * @Purpose: Purpose of the file
     * @Version: The version of the file, somewhere there should be a change log. Either here, in a txt file or on your website
     *
     * Extra commentary if needed.
     */
    
    All PHP classes and functions should be commented as following:
    /**
     * @Purpose: Purpose of the class/function (unless it's a variable, then omit)
     * @Return: What is expected to return once the function has completed (i.e. <Boolean, string, etc> <what causes the condition>)
     *
     * Extra commentary if needed.
     */
     
    To get URI taxonomy (taxa) to work, make sure .htaccess is installed and Apache can access it.