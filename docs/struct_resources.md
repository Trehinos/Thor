# Resources folder
The resource folder, located by default at ```thor/app/res/```, contains
all serverside resources.
> **get path in PHP** :
> ```php
>   $path = Thor\Globals::RESOURCES_DIR;
> ```

## Yaml files
These folders contain static data files in [YAML](https://en.wikipedia.org/wiki/YAML) organized in two directories : ```static``` and ```config```.

### Static data
Static data are located in ```thor/app/res/static/```.  
Characteristics of static data are : it **doesn't change** between *environments*, it **changes** between *projects*.
> **get path in PHP** :
> ```php
>   $path = Thor\Globals::STATIC_DIR;
> ```

### Configuration
Configuration of the application is located in ```thor/app/res/config/```.  
Characteristics of "config" : it **may change** between *environments*, it **may change** between *projects*.
> **get path in PHP** :
> ```php
>   $path = Thor\Globals::CONFIG_DIR;
> ```


### How to **read a YAML resource file** ?
```php
// With Symfony/Yaml
use Thor\Globals;
use Symfony\Component\Yaml\Yaml;
$config = Yaml::parseFile(Globals::CONFIG_DIR . 'filename.yml');
$static = Yaml::parseFile(Globals::STATIC_DIR . 'filename.yml');


// Static singleton for default configuration files.
use Thor\Thor;
$config = Thor::config('key'); // links to thor/app/res/config/{key}.yml 
$static = Thor::config('key', true); // links to thor/app/res/static/{key}.yml
```

#### Configuration files (keys of ```config()```)
* ```config``` contains the main configuration entries.
* ```database``` contains database connections information.
* ```security``` : ```userPdoRow.pdoRowClass``` and ```userPdoRow.hasPwdHashFor``` are links to
  the user class and ```configuration``` contains the security configuration.
* ```twig``` contains Twig library configuration.

#### Static files  (keys of ```config(, true)```)
* ```daemons/{daemonName}``` config of a daemon.
* ```langs/{lang}``` contains static strings in a specific language.
* ```commands``` contains command information.
* ```kernels``` contains kernels that can be executed by the application.
* ```icons``` contains FontAwesome icon suffixes by categories.
* ```menu``` contains the menu displayed by the application with Thor theme.
* ```routes``` contains the routes of HTTP applications.

## Views
**Twig** view files are located in ```thor/app/res/views/```.  
Edit ```views_dir``` array in ```thor/app/res/config/twig.yml``` to add folders.
> **get paths in PHP** :
> ```php
>  $viewsFoldersArray = Thor\Thor::config('twig')['views_dir'];
> ```
