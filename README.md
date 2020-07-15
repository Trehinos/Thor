# THOR
**Thor** is designed as a PHP *framework* as well as a web *software*.  
This project's goal is to provide a base project for business projects.

**This project is still in development. Further releases installation process will be
simplified.**

## Requires
**To set up or download separately** :
* DBMS
* HTTP server
* PHP 7.4+
* Composer
* Fontawesome PRO icons (min version 5.13.1)  
  *You can use the free version, but some icons are not going to work without modifications*.
* Bootstrap

## THOR setup
1. Download the project and unzip it. Say it has been unzipped in ```thor/```.
2. Go to ```thor/``` in a terminal and type ```composer update```.
3. Launch ```thor/engine/sql/setup.sql``` in an SQL environment to set up Thor DB and edit ```thor/engine/config/database.yml```.
4. Create a virtualhost which point on ```thor/web/index.php```
5. Copy fontawesome ```all.min.js``` in ```thor/web/assets/fontawesome/js/all.min.js```
6. Copy ```bootstrap.min.js``` in ```thor/web/assets/bootstrap/js/bootstrap.min.js```
7. Copy ```bootstrap.min.css``` in ```thor/web/assets/bootstrap/css/bootstrap.min.css```
8. The user who runs PHP has to be allowed to write in ```thor/var/``` folder.

## Features
* Framework
    * Twig.
    * PDO handler and requester.
    * CrudHelper and PdoRowInterface.
    * HTTP request/response cycle handling :
        * Routing (path info matches regexp).
        * Twig ```url(route_name, route_params)``` function.
        * Class with methods controllers. The class MUST extend ```Thor\Controller\BaseController```.
    * Web frameworks : Bootstrap and FontAwesome.
* Software (web application) as a base project.

## TODO
* Documentation
* Users, authentication, firewall
* Form (class hierarchy and html dump)
* Form to database CRUD actions engine
