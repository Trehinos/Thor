# Thor documentation

## Application

- [Resources](struct_resources.md) : static data and views.
  - [ⓘ How to : read a configuration/resource file](struct_resources.md#how-to-read-a-yaml-resource-file-)

## [Entry points, Kernel and application](struct_entries.md)

- [HTTP entry point : Routing & Controllers](struct_entries.md#the-http-entry-point)
- [CLI entry point : CliKernel & Commands](struct_entries.md#cli-entry-point)

## Thor libraries

- [Database](thor_database.md)
  - [Database configuration](thor_database.md#database-configuration)
  - [PdoExtension](thor_database.md#pdoextension-public-api)
  - [PdoTable](thor_database.md#pdotable-public-api)
  - [ⓘ How to : create an user DAO](thor_database.md#example--create-a-user-class-linked-to-a-table-in-db)
- [Http module](thor_http.md) and [routing](thor_routing.md)
- [Cli module](thor_cli.md)
    - [Console utility class](thor_cli.md#console-class-final)
    - [Create a daemon](thor_cli.md#example--create-a-daemon-and-cron-daemonscheduler)
    - [ⓘ How to : define a command](thor_cli.md#how-to-define-a-command-)
- Logger
- Extend Twig and Html utilities
- Security
- Validation utilities


## Variable files

- Cache
- Logs
