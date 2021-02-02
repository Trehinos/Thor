# Resources folder
The resource folder, located by default at ```thor/app/res/```, contains
all serverside resources.
> **PHP** : ```Thor\Globals::RESOURCES_DIR```. 

## Yaml files
These folders contain static data files in [YAML](https://en.wikipedia.org/wiki/YAML) organized in two directories : ```static``` and ```config```.

> How to **read a YAML resource file** ?
> ```php
> use Thor\Thor;
> use Thor\Globals;
> use Symfony\Component\Yaml\Yaml;
> 
> // With Symfony/Yaml
> $config = Yaml::parseFile(Globals::CONFIG_DIR . 'filename.yml');
> $static = Yaml::parseFile(Globals::STATIC_DIR . 'filename.yml');
> 
> // Static singleton for default configuration files.
> $config = Thor::getInstance()->loadConfig('config file key');
> $static = Thor::getInstance()->loadConfig('static file key');
> ```

### Static data
Static data are located in ```thor/app/res/static/```.  
Characteristics of static data are : they **never change** between *environments*, they **shall change** between *projects*.
> **PHP** : ```Thor\Globals::STATIC_DIR```.

### Configuration
Configuration of the application is located in ```thor/app/res/config/```.  
Characteristics of "config" : it **may change** between *environments*, it **may change** between *projects*.
> **PHP** : ```Thor\Globals::CONFIG_DIR```.

## SQL
SQL scripts are located in ```thor/app/res/sql/```.
> **PHP** : ```Thor\Globals::RES_DIR . 'sql/```.

## Views
**Twig** view files are located in ```thor/app/res/views/```.  
Edit ```views_dir``` array in ```thor/app/res/config/twig.yml``` to add folders.
> **PHP** : ```Thor\Thor::getInstance()->loadConfig('twig')['views_dir'][$index]'```.
