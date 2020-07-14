# THOR
**Thor** is designed as a PHP *framework* as well as a web *software*.  
This project's goal is to provide a base project for business projects.

**This project is still in development. Further releases installation process will be
simplified.**

## Setup THOR
1. Download the project and unzip it. Say it has been unzipped in ```thor/```.
2. Go to ```thor/``` in a terminal and type ```composer update```.
3. Launch ```thor/engine/sql/setup.sql``` in a SQL environment to setup Thor DB.

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
