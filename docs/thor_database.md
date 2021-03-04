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
$pdoCollection = PdoCollection::createFromConfiguration(Thor::config('database'));

// Send a query to the DBMS :
use Thor\Database\PdoExtension\PdoRequester;
$pdoHandler = $pdoCollection->get('db-connection-identifier');
$requester = new PdoRequester($pdoHandler); // Use PdoTransaction instead of PdoRequester to perform
                                            // the queries in a transaction.
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

* ```static getTableDefinition(): PdoRow```
* ```static getPdoColumnsDefinitions(): array```
* ```static getPrimaryKeys(): array```
* ```static getIndexes(): array```
* ```toPdoArray(): array```
* ```fromPdoArray(array $pdoArray): void```
* ```getPrimary(): array```
* ```getPrimaryString(): string```

#### AdvancedPdoRow ```trait``` ```implements PdoRowInterface```

Bind a table to a class with PHP attributes.

* ```__construct(array $primaries = [])```
* ```final static getTableDefinition(): PdoRow```
* ```final static getPdoColumnsDefinitions(): array```
* ```final static getPrimaryKeys(): array```
* ```final static getIndexes(): array```
* ```final toPdoArray(): array```
* ```final fromPdoArray(array $pdoArray): void```
* ```final getPrimary(): array```
* ```final getPrimaryString(): string```

#### AbstractPdoRow ```class``` ```abstract``` ```use AdvancedPdoRow```

Defines a ```public_id``` column.

* ```__construct(?string $public_id = null, array $primaries = [])```
* ```getPublicId(): ?string```
* ```generatePublicId(): void```

#### Attributes

* ```#[PdoRow]```
    * ```?string $tableName = null```
    * ```array $primary = []```
    * ```?string $auto = null```
* ```#[PdoColumn]```
    * ```string $name```
    * ```string $sqlType```
    * ```string $phpType```
    * ```bool $nullable = true```
    * ```mixed $defaultValue = null```
    * ```?callable $toSqlValue = null```
    * ```?callable $toPhpValue = null```
* ```#[PdoIndex]```
    * ```array $columnNames```
    * ```bool $isUnique = false```
    * ```?string $name = null```
* ```#[PdoForeignKey]```
    * ```string $className```
    * ```array $targetColumns```
    * ```array $localColumns```
    * ```?string $name = null```

#### PdoAttributesReader ```class``` ```final```

* ```__construct(private string $classname)```
* ```static pdoRowInfo(string $className): array```  
  ```#[ArrayShape(['row' => PdoRow::class, 'columns' => 'array', 'indexes' => 'array', 'foreign_keys' => 'array'])```
* ```getAttributes(): array```  
  ```#[ArrayShape(['row' => PdoRow::class, 'columns' => 'array', 'indexes' => 'array', 'foreign_keys' => 'array'])```
  
#### CrudHelper ```class``` ```final```

Performs SQL queries with a ```PdoRequester``` on a specified class implementing ```PdoRowInterface```.

* ```__construct(string $className, PdoRequester $requester)```
* ```static instantiateFromRow(string $className, array $row): mixed```
* ```table(): string```
* ```listAll(): array```
* ```createOne(PdoRowInterface $row): string```
* ```createMultiple(array $rows): bool```
* ```readOne(array $primaries): mixed```
* ```readOneBy(Criteria $criteria): mixed```
* ```readOneFromPid(string $pid): mixed```
* ```readMultipleBy(Criteria $criteria): array```
* ```updateOne(PdoRowInterface $row): bool```
* ```deleteOne(PdoRowInterface $row): bool```

#### Criteria ```class``` ```final```

* ```const GLUE_AND = true```
* ```const GLUE_OR = false```
* ```__construct(array $criteria = [], bool $glue = self::GLUE_AND)```
* ```static compile(array $criteria, bool $glue = self::GLUE_AND): array```  
  ```#[ArrayShape(['sql' => "string", 'params' => "array"])]```
* ```static getWhere(Criteria $criteria): string```
* ```getSql(): string```
* ```getParams(): array```

#### SchemaHelper ```class``` ```final```

* ```__construct(PdoRequester $requester, PdoAttributesReader $reader)```
* ```createTable(): bool```
* ```dropTable(): bool```

### Example : Create a User class linked to a table in DB
