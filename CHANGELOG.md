### 1/7/14 - Commit 2 ###
* Found a better way to handle errors in general

### 1/7/14 - Commit 1 ###
* Strip www. from HTTP_HOST when loading the controllers and config files
* Stripped information about config values from debug_backtrace() on error reporting

### 12/18/13 - Commit 1 ###
* Changed system/router.php to load_helpers() instead of load_traits() so you can now load traits or interfaces

### 12/10/13 - Commit 1 ###
* Fixed issue if there was trailing space in front of a database query on select

### 12/9/13 - Commit 1 ###
* Fixed issue with not being able to use empty() on $sys->template->my_var because __isset() wasn't being used

### 12/5/13 - Commit 5 ###
* Added the ability to load traits

### 12/5/13 - Commit 4 ###
* Removed TODO.md in favor of GitHub Issues

### 12/5/13 - Commit 3 ###
* Fixed issue in system/template.php where it threw error array to string

### 12/5/13 - Commit 2 ###
* Fixing .gitignore - making repos play nice with each other

### 12/5/13 - Commit 1 ###
* Messed up applications/shared/controller files and they werent in the right directory

### 12/4/13 - Commit 4 ###
* Pushed documentation to wiki
* Wrote documentation

### 12/4/13 - Commit 2-3 ###
* Broke system/template.php->parse() into multiple methods
* Fixed some potentional bugs with not pre-definiting class-scope $sys variable
