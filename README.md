# Thor

**Thor** is designed as a PHP *framework* as well as a web *software*.  
This project's goal is to provide a base project for **PHP developers** to develop corporate projects.

**This project is still in active development** : public API may change, but tagged versions are usable as is.  
**In further releases, the installation process will be simplified.**

&copy; 2020-2022 Sébastien GELDREICH

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
    - `project/document` : **auto-documentation of your project** in markdown ([an example here](https://lab.frogg.it/Trehinos/Thor/-/wikis/documentation)) 

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

### Web vendors (not included)

> These vendors only have to be installed if you want to use Thor to build a web application **AND**
> you use the pre-developed views.  
> Feel free to use your own libraries/vendors if you don't use Thor's pre-developed views.

* [JQuery 3.6.0](https://code.jquery.com/jquery-3.5.1.min.js)
* [Bootstrap 5.1.3](https://getbootstrap.com/docs/5.1/getting-started/download/)
* [Fontawesome 5.15.1](https://fontawesome.com/) PRO icons **licence required**
* [DataTables 1.11.3](https://datatables.net/download/#bs5/dt-1.11.3/af-2.3.7/b-2.1.1/cr-1.5.5/date-1.1.1/fc-4.0.1/fh-3.2.1/kt-2.6.4/r-2.2.9/sc-2.0.5/sl-1.3.4)

## Documentation

* [Complete documentation](https://lab.frogg.it/Trehinos/Thor/-/wikis/home)
  * [Setup THOR](https://lab.frogg.it/Trehinos/Thor/-/wikis/setup)
  * [Classes reference](https://lab.frogg.it/Trehinos/Thor/-/wikis/documentation)

## License

License : [MIT](LICENSE)  
&copy; 2020-2022 Sébastien Geldreich
