# Thor database module

The **database** module is divided in 2 submodules :

* ```PdoExtension``` which provides a way to easily connect and request a database.
* ```PdoTable``` which is analog as a light ORM.

## Pdo extension submodule

This submodule goal is to provide a stable API to connect a database and send SQL queries easily.

### PdoExtension public API

#### PdoHandler ```class``` ```final```

This class goal is to handle a PDO constructor parameters set and construct it on demand.

* ```__construct(string $dsn, ?string $user = null, ?string $password = null, int $defaultCase = PDO::CASE_NATURAL)```
* ```getPdo(): PDO```

The PDO object is created with these options :
```php
[
    PDO::ATTR_CASE => $this->defaultCase,
    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
    PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION
]
```

#### PdoCollection ```class``` ```final```

This class is a collection of ```PdoHandler```. It can be constructed from ```thor/app/res/config/database.yml```.

* ```__construct()```
* ```static createFromConfiguration(array $dbConfig): PdoCollection```
* ```add(string $connectionName, PdoHandler $handler): void```
* ```get(string $connectionName = 'default'): ?PdoHandler```
* ```all(): array```

#### PdoRequester ```class```

This class executes a request or performs a request and returns a result as a native ```PDOStatement```.

* ```__construct(PdoHandler $handler)```
* ```getPdoHandler(): PdoHandler```
* ```execute(string $sql, array $parameters): bool```
* ```executeMultiple(string $sql, array $parameters): bool```
* ```request(string $sql, array $parameters): PDOStatement```

#### PdoTransaction ```class``` ```final``` ```extends PdoRequester```

Can replace a ```PdoRequester``` anywhere to send queries as an SQL Transaction if the used driver supports it.

* ```__construct(PdoHandler $handler, bool $autoTransaction = true)```
* ```__destruct()```
* ```begin(): void```
* ```commit(): void```
* ```rollback(): void```

### Database configuration

The database configuration file is ```thor/app/res/config/database.yml```.  
This file contains all DB connections information as DSN, user and password.

For each DB connection, add an entry :

```yaml
db-connection-identifier:
  dsn: "driver:dsn"
  user: db-user
  password: db-password
  case: upper | lower | [natural]
```

### Handle configuration and request DB

```php
// Retrieve all connection information
use Thor\Thor;
use Thor\Database\PdoExtension\PdoCollection;
$pdoCollection = PdoCollection::createFromConfiguration(
    Thor::getInstance()->loadConfig('database')
);

// Send a query to the DBMS :
use Thor\Database\PdoExtension\PdoRequester;
$pdoHandler = $pdoCollection->get('db-connection-identifier');
$requester = new PdoRequester($pdoHandler);
$result = $requester->request('SELECT * FROM User WHERE id=?', ['1'])->fetchAll();
```

#### Controllers and commands shortcuts

In a controller extending ```Thor\Http\BaseController```:

```php
$pdoHandler = $this->getServer()->getHandler('db-connection-identifier');
$requester = new PdoRequester($pdoHandler);
```

In a command extending ```Thor\Cli\Command``` :

```php
$pdoHandler = $this->cli->pdos->get('db-connection-identifier');
$requester = new PdoRequester($pdoHandler);
```

## PdoTable submodule

This submodule goal is to provide a stable API for MySql/MariaDB only (for now).  
With these classes, you can define [Data Access Objects](https://en.wikipedia.org/wiki/Data_access_object) easily.

### PdoTable public API

#### PdoRowInterface ```interface```

Define a PdoRow class contract.

#### AdvancedPdoRow ```trait``` ```implements PdoRowInterface```

Bind a table to a class with PHP attributes.

#### AbstractPdoRow ```class``` ```abstract``` ```use AdvancedPdoRow```

Defines a ```public_id``` column.

#### Attributes

* ```#[PdoRow]```
* ```#[PdoColumn]```
* ```#[PdoIndex]```
* ```#[PdoForeignKey]```

#### PdoAttributesReader ```class``` ```final```

#### CrudHelper ```class``` ```final```

#### Criteria ```class``` ```final```

#### SchemaHelper ```class``` ```final```

### Example : Create a User class linked to a table in DB