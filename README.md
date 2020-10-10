# THOR v0.2-dev
**Thor** is designed as a PHP *framework* as well as a web *software*.  
This project's goal is to provide a base project for business projects.

**This project is still in development. Further releases installation process will be
simplified.**

&copy; 2020 Trehinos

## Build on
### Environment
* Pdo compatible DBMS
* HTTP server
* PHP 7.4+ / Pdo / DBMS PHP drivers
* Composer

### Vendors
*Installed with ```composer update```* :
* Symfony/VarDumper 5
* Symfony/Yaml 5
* Twig 3

*To download and set up separately* :
* Fontawesome PRO icons (min version 5.13.1)  
  *You can use the free version, but some icons are not going to work without modifications*.
* Bootstrap 4.5

## THOR setup
1. Download the project and unzip it. Say it has been unzipped in ```thor/```.
2. Go to ```thor/``` in a terminal and type ```composer update```.
3. Launch ```thor/engine/sql/setup.sql``` in an SQL environment to set up Thor DB (**mimir**) and edit ```thor/engine/config/database.yml```.
4. Create a virtualhost which has ```thor/web/``` as *DocumentRoot*.
5. Copy fontawesome ```all.min.js``` in ```thor/web/assets/fontawesome/js/all.min.js```
6. Copy ```bootstrap.min.js``` in ```thor/web/assets/bootstrap/js/bootstrap.min.js```
7. Copy ```bootstrap.min.css``` in ```thor/web/assets/bootstrap/css/bootstrap.min.css```
8. The user who runs PHP has to be allowed to write in ```thor/var/``` folder.

## Configuration & static files
All configuration files are in ```thor/app/res/config/``` or in ```thor/app/res/static/```:
### Config
* ```config.yml``` contains the main parameters as the language, the environment and the application name...
* ```database.yml``` contains DB connections information.
* ```twig.yml``` contains TWIG configuration.
### Static
* ```db_definition.yml``` contains tables information.
* ```menu.yml``` contains the menu settings.
* ```routes.yml``` contains routes patterns and target controllers information.
* ```security.yml``` contains security configuration.
* Languages files

To add a config file, create a new **YAML** file in the configuration folder.
Then, you can read this file by writing :
```php
use Thor\Globals;
use Symfony\Component\Yaml\Yaml;

$config = Yaml::parse(Globals::CONFIG_DIR . 'filename.yml');
```  

## Features
* Framework
    * Security
        * User management interface (create).
    * PDO handler and requester.
    * CrudHelper and PdoRowInterface.
    * HTTP request/response cycle handling :
        * Routing (path info matches regexp).
        * Twig ```url(route_name, route_params)``` function.
        * Class with methods controllers.  
          The class MUST extend ```Thor\Controller\BaseController```.
            * TWIG template engine
            * TWIG utility functions and filters
    * Web frameworks : Bootstrap and FontAwesome.
* Software (web application) as a base project.

## TODO
* Documentation
* Db Definition (create table, stop using static function)
* Security
    * Edit/delete/change password
    * Login, check, logout
    * Firewall (pathinfo pattern)
    * Permissions and roles
    * User groups
* Form (class hierarchy and html dump)
* Form to database CRUD actions engine
* CLI/REPL/Automaton contexts
    * ConsoleControl / ConsoleReader
    * Command
    * Argument
    * Process
