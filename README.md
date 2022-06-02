# Thor

**Thor** is designed as a PHP *framework* as well as a web *software*.  
This project's goal is to provide a base project for **PHP developers** to develop corporate projects.

**This project is still in active development** : public API may change, but tagged versions are usable as is.  
**In further releases, the installation process will be simplified.**

&copy; 2020-2022 Sébastien GELDREICH

## Key features

* Main modules of **Thor** :
    * **Database** module :
        * **PdoExtension** module : Connection handler, requester, transaction, *extends PDO*.
        * **PdoTable** module :
            * Concept of `PdoTable`, an Entity-like object representing a table row.
                * attributes `#[PdoRow]`, `#[PdoIndex]`, `#[PdoColumn]`, `#[PdoForeignKey]`.
            * `CrudHelper` : performs CRUD operations on DB,
            * `SchemaHelper` : performs DQL operations on DB,
            * `BasePdoRow`/`PdoRowTrait`/`PdoRowInterface` : models to create DAOs with CrudHelper.
    * **Http** module (with `PSR-7`) : **Request** -> **Server** -> **Router** -> **Controller** -> **Response**.
        * Use the `#[Route]` attribute,
        * **Security** module : with the `#[Authorization]` attribute to protect routes within a simple permission
          system,
    * **CLI** module : performs custom CLI commands ; offers a console color/formatting utility.
    * **Daemons** scheduler module.

* Other features :
    * **Twig** template system.
    * **Multilingual** static and **dynamic** strings (`"xxx"|_()` Twig filter and `DICT` global variable).
    * Extensible application with custom **kernels**.
    * PSR inspired interfaces.
    * Base **web application** to develop a corporate work :
        * Index, legal, about and changelog pages.
        * **Menu** system with icons and authorizations.
        * Page loading in **AJAX**  to reduce payloads.
        * **Users** management (create/edit/change password/delete), login, logout.
        * **Permissions** management (`#[Authorization]` PHP attribute on controllers, `authorized()` twig function in
          views).
    * Console commands to **control the application** :
        * `user/` : `create` / `edit` / `delete` / `list`.
        * `core/` : `setup` / `install` / `update` / `uninstall` / `set-env`.
        * `clear/` : `cache` / `logs`.
        * `route/` : `set` / `list`.
        * `database/migrate`
    * Daemons and daemons control :
        * `start` / `stop` / `status` / `run` : enable, disable, execute or get info of a daemon.
        * `kill` / `reset` : stop execution or reset state.

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

* Symfony/VarDumper 5.3
* Symfony/Yaml 5.3
* Twig/Twig 3.3
* Datatables/Editor 2.0.5

### Web vendors (not included)

* [JQuery 3.6.0](https://code.jquery.com/jquery-3.5.1.min.js)
* [Bootstrap 5.1.3](https://getbootstrap.com/docs/5.1/getting-started/download/)
* [Fontawesome 5.15.1](https://fontawesome.com/) PRO icons **licence required**
* [DataTables 1.11.3](https://datatables.net/download/#bs5/dt-1.11.3/af-2.3.7/b-2.1.1/cr-1.5.5/date-1.1.1/fc-4.0.1/fh-3.2.1/kt-2.6.4/r-2.2.9/sc-2.0.5/sl-1.3.4)

## Documentation

* [Setup THOR](https://github.com/Trehinos/Thor/wiki/Setup)
* [Classes reference](https://github.com/Trehinos/Thor/wiki/documentation)
* [Complete documentation](https://github.com/Trehinos/thor/wiki)

## License

License : [MIT](LICENSE)  
&copy; 2020-2022 Sébastien Geldreich
