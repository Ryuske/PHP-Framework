### 12/2/13 - Commit 4 ###
* Added a default application/shared directory with example an example controller, model and view

### 12/2/13 - Commit 3 ###
* Fixed tabs in system files
* Removed out-dated code

### 12/2/13 - Commit 1-2 ###
* Made it so that token arrays work (i.e. {foo[0]} returns 'bar')
* Bugfixes with token arrays

### 11/27/13 - Commit 5 ###
* Rename $system_di to $sys
* Changed the way $sys is passed to new classes. Hopefully reducing the RECURSIVE array deal on dumps

### 11/27/13 - Commit 2-4 ###
* Worked on README.md formatting

### 11/27/13 - Commit 1 ###
* Moved TODO out of README and into TODO
* Converted README, TODO and CHANGELOG into Github MD formats
* Moved /applications to /application
* Changed controller main.php to home.php
* Pushed framework to version 2

### 11/26/13 - Commit 1 ###
* Fixed an issue with router.php where if your controller wasn't in a directory it wouldn't work
* Fixed a couple other issues in router.php

### 11/25/13 - Commit 1 ###
* Refactored system/router.php->get_router()

### 11/18/2013 - Commit 1 ###
* Fixed general issues with Databasing and Templates

### 11/30/2012 - Commit 1 ###
* Changed permissions
* Fixed issue where if you went to www.example.com the framework would break

### 11/18/2012 - Commit 3 ###
* Changed /applications to /application
* Removed TODO from README
* Added TODO file
* Refactored CHANGELOG

### 11/18/2012 - Commit 2 ###
* Fixed some issues with the /plugins directory & not commiting plugins
* Fixed issue when seeing if an uninitialized view exists. /system/router.php:171

### 11/18/2012 - Commit 1 ###
* Updated all file comment headers to incorporate the correct url. (khdev.net)
