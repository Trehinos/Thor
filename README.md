# Thor

**Thor** is designed as a PHP *framework* as well as a web *software*.  
This project's goal is to provide a base project for **PHP developers** to develop corporate projects.

**This project is still in active development** : public API may change, but tagged versions are usable as is.  
**In further releases, the installation process will be simplified.**

&copy; 2020-2021 Sébastien GELDREICH

## Key features

* Complete but lightweight framework :
    * PSR inspired interfaces (compliant but with stronger type bindings) and implementation :
        * `PSR-3` Logger
        * `PSR-4` Auto-loading (with **composer**)
        * `PSR-7` HTTP Message
        * `PSR-12` Code Style
        * `PSR-15` HTTP Handler
        * `PSR-16` SimpleCache (*in memory* implementation)
        * `PSR-18` HTTP Client
    * Databases utility classes using **PDO** :
        * **PdoExtension** : Connection handler, requester, transaction.
        * **PdoTable** :
            * `CrudHelper` : performs CRUD operations on DB,
            * `SchemaHelper` : performs DQL operations on DB,
            * `BasePdoRow`/`PdoRowTrait`/`PdoRowInterface` : models to create DAOs with CrudHelper :
            * attributes `#[PdoRow]`, `#[PdoIndex]`, `#[PdoColumn]`, `#[PdoForeignKey]`.
    * Http cycle handling : **Router and controllers** (attribute `#[Route]`).
    * **CLI commands** handling, console color/formatting utility.
    * Static **logger** and **configuration**.
    * **Twig** template system.
    * **Multilingual** static and **dynamic** strings (`|_()` Twig filter and `DICT` global variable).
    * Extensible application with **kernels**.
* Base **web application** to develop a corporate work :
  ![Thor web UI illustration](https://i.ibb.co/R4q28Pg/ui.png)
    * Index, legal, about and changelog pages.
    * **Menu** system with icons and authorizations.
    * Page loading in **AJAX**  to reduce payloads.
    * **Users** management (create/edit/change password/delete), login, logout.
    * **Permissions** management (`#[Authorization]` PHP attribute on controllers, `autorized()` twig function in views).
* Console commands to **control the application** :
    * `user/` : `create` / `edit` / `delete` / `list`.
    * `core/` : `setup` / `install` / `update` / `uninstall` / `set-env`.
    * `clear/` : `cache` / `logs`.
    * `route/` : `set` / `list`.
    * `database/migrate`
* Daemons and daemons control (e.g. `daemon/status -all` command) :  
  ![Daemons status illustration](https://i.ibb.co/y84GkDy/daemons.png)
    * `start` / `stop` / `status` : enable or disable a daemon.
    * `kill` / `reset` : stop execution or reset state.

## Dependencies

### Environment

* `GNU/Linux` or `Windows` system. Tested on `Virtualbox`, `XAMPP` and `WSL2`.
* PHP 8.1 + PDO + DBMS PDO drivers
* PHP-EXT `calendar` `curl` `ftp` `intl` `json` `ldap` `mbstring` `openssl` `pdo` `ssh2` `zip`
* PDO compatible DBMS
* HTTP server
* Composer

### Server-side vendors

#### Installed with `composer update`

* Symfony/VarDumper 5.3
* Symfony/Yaml 5.3
* Twig/Twig 3.3
* NuSphere/NuSoap 0.9.6
* Datatables/Editor 2.0.5
* PhpOffice/PhpSpreadsheet 1.19

### Web vendors (not included)

* [JQuery 3.5.1](https://code.jquery.com/jquery-3.5.1.min.js)
* [Popper 1.16](https://cdn.jsdelivr.net/npm/popper.js@1.16.0/dist/umd/popper.min.js)
* [Fontawesome 5.13.1](https://fontawesome.com/) PRO icons **licence required**
* [Bootstrap 5.1](https://getbootstrap.com/docs/5.1/getting-started/introduction/)

## Documentation

* [Setup THOR](https://github.com/Trehinos/Thor/wiki/Setup) 
* [Complete documentation](https://github.com/Trehinos/thor/wiki)

## License

License : [MIT](LICENSE)  
&copy; 2020-2021 Sébastien Geldreich
