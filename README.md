# THOR
A docker-compose system which provide a HTTP/PHP(Pdo/Twig)/MySQL development environment.  
**Thor** is designed as a PHP *framework* as well as a web *software.

*This repository is under ```thor/code``` directory in a **Thor** release.*

## Setup THOR
1. Download the project and unzip it. Say it has been unzipped in ```thor/```.
2. Go to ```thor/``` directory from a command line.
3. Type ```docker-composer up```. System is up.

## Use THOR
Now installed, with **Thor** you can :
* From a web browser : go to ```127.0.0.1:6788``` to go to **Thor** web context.
* From file system, go to ```thor/code``` to extends **Thor** framework and develop your own app from it.
* Use PhpMyAdmin at ```127.0.0.1:6706```. Database name is **mimir**.

## Features
* Framework
    * Twig.
    * PDO handler and requester.
    * HTTP request/response cycle handling :
        * Routing (path info matches regexp).
        * Twig ```url(route_name, route_params)``` function.
        * Class with methods controllers. The class MUST extend ```Thor\Controller\BaseController```.
    * Web frameworks : Bootstrap and FontAwesome.
* Software

## TODO
* Documentation
* Users, authentication, firewall
* Form (class hierarchy and html dump)
* Database
    * Table representation
    * EntityEngine (Table to object from a specified class)
* Form to database CRUD actions engine
