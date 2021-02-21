# Thor database module

## Pdo extension classes

### PdoExtension public API

#### PdoHandler

* ```__construct(string $dsn, ?string $user = null, ?string $password = null, int $defaultCase = PDO::CASE_NATURAL)```
* ```getPdo(): PDO```

#### PdoCollection

* ```__construct()```
* ```static createFromConfiguration(array $dbConfig): PdoCollection```
* ```add(string $connectionName, PdoHandler $handler): void```
* ```get(string $connectionName = 'default'): ?PdoHandler```
* ```all(): array```

#### PdoRequester

* ```__construct(PdoHandler $handler)```
* ```getPdoHandler(): PdoHandler```
* ```execute(string $sql, array $parameters): bool```
* ```executeMultiple(string $sql, array $parameters): bool```
* ```request(string $sql, array $parameters): PDOStatement```

#### PdoTransaction ```extends PdoRequester```

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

### AdvancedPdoRow : bind a table to a class with PHP attributes

```#[PdoRow]``` ```#[PdoColumn]``` ```#[PdoIndex]```

#### PdoColumn

#### PdoIndex

## Sql generation

### CrudHelper and Criteria

### SchemaHelper

## Example : Create a User class linked to a table in DB