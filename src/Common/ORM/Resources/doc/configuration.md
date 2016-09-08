# Configuration

The ORM bases on a definition of the data model (written in YML notation) to
provide a set of components to fetch and save entities from different data
sources (database, files, external services, etc.), data validation, database
schema migrations and more.

The ORM configuration can define database connections, entities and schemas that
will be ready-to-use after parsing the configuration files.

A good data model definition will simplify the way entities are validated and
persisted to the data source.

> **Configuration path**
>
> The path to the ORM configuration is defined by the parameter `orm.config_path`
> in the file `Resources/config/services.yml`.
>
> To keep bundle dependencies to the minimum, it is recommended to use
> `Resources/config/orm` as the configuration path.

This section will include the following informatino related to configuration.
1. [Connection](./configuration/connection.md)
2. [Metadata](./configuration/metadata.md)
3. [Schema](./configuration/schema.md)

## Configuring the ORM

The ORM configuration contains connections, metadata and schemas.

### Connections

To add a new connection create `*.yml` file in the `orm.config_path` with a
connection definition.

To know how to define a connection see
[Connection](./configuration/connection.md).

### Metadata

To add entity metadata create `*.yml` file in the `orm.config_path` with an
entity definition.

To know how to define entity metadata see
[Metadata](./configuration/metadata.md).

### Schemas

To add a new schema create `*.yml` file in the `orm.config_path` with a
schema definition.

To know how to define a schema see [Schema](./configuration/schema.md).

## Loading the ORM configuration

The ORM configuration is loaded by the `Loader` component when the `orm.manager`
service is required. It will all `*.yml` files included in `orm.config_path`.

> It is recommended to keep connecions in `orm/connection`, entities in
> `orm/entity` and schemas in `orm/schema`.

The `Loader` will use the parameter `orm.default` as default values for the
loaded items. The parameter can include default values for connections, entities
and schemas.

    parameters:
        orm.default:
            connection:
                driver: mysqli
                dbname: glork
                host: localhost
                port: 3306
                user: fred
                password: wobble
                charset: UTF-8
            schema:
                entities:
                    - Entity

The values in `orm.default` parameter will be merged with values included in
every `*.yml` to get the final data for items in configuration.

So, a connection defined in `connection.yml` like this:

    connection:
        name: cdb00
        dbname: corge

will look like this after merging data with `orm.default` values:

    connection:
        name: cdb00
        driver: mysqli
        dbname: corge
        host: localhost
        port: 3306
        user: fred
        password: wobble
        charset: UTF-8

> Note that values in `orm.default` will be overwritten by values in
> `connection.yml`.
