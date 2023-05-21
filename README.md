# Thor

**Thor** is designed as a PHP *framework* as well as a web *software*.  
This project's goal is to provide a base project for **PHP developers** to develop corporate projects.

&copy; 2020-2023 Sébastien GELDREICH

## Key features

- A complete **Framework** (Routing, Controllers, HTTP client, Database, Cli),
- A **daemon** scheduler to run automated tasks periodically, 
- PHP8.1 **attributes** (`#[Route]`, `#[Authorization]`, `#[PdoTable]`, `#[PdoColumn]`, `#[PdoIndex]`, `#[PdoForeignKey]`)
- **Twig** templating
- **Custom kernels** to add new **entry points** (default are `web`, `daemon` and `cli`).
- Pre-developed **features** :
  - Base **web application** :
    - Index, legal page, changelog and about,
    - **Menu system** with icons and authorizations,
    - Pages loaded with AJAX,
    - Users management and authentication, with a permission system,
  - **Cli** commands :
    - User : `create`, `edit`, `delete`, `list`,
    - Core : `setup`, `update`, `migrate`, `set-env`
    - Clear : `cache`, `logs`,
    - Route : `list`
    - Daemons : `status`, `state`, `run`, `kill`
    - `project/document` : **auto-documentation of your project** in markdown ([an example here](https://asgard.trehinos.eu/Trehinos/Thor/-/wikis/documentation)) 

## Dependencies

### Environment

* `GNU/Linux` or `Windows` system. Tested on `Virtualbox`, `XAMPP` and `WSL2`.
* PHP 8.1 + PDO + DBMS PDO drivers
* PHP-EXT `calendar` `curl` `ftp` `intl` `json` `mbstring` `openssl` `pdo` `ssh2` `zip`
* PDO compatible DBMS
* HTTP server
* Composer

### Server-side vendors

#### Installed with `composer update`

- phpoffice/phpspreadsheet: ```1.x```
- datatables.net/editor-php: ```2.x```
- symfony/var-dumper: ```5.x```
- symfony/yaml: ```5.x```
- twig/twig: ```3.x```
- phpunit/phpunit : ```9.x```

## Documentation

* [Complete documentation](https://asgard.trehinos.eu/Trehinos/Thor/-/wikis/home)
  * [Setup THOR](https://asgard.trehinos.eu/Trehinos/Thor/-/wikis/setup)
  * [Classes reference](https://asgard.trehinos.eu/Trehinos/Thor/-/wikis/documentation)

## License

License : [MIT](LICENSE)  
&copy; 2020-2023 Sébastien Geldreich
