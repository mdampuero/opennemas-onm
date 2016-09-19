# Connection

A connection defines a connection to a database.

The connection definition includes the following data:

- `name`: A name to refer the connection. This name will be used when configuring
   the repository connections.
- `driver`: The driver to connect to the database.
- `dbname`: The database name.
- `host`: The database host.
- `post`: The port
- `user`: The user to connect to the database.
- `password`: The password to connect to the database.
- `charset`: The charset of the database.

The following example describes a connection.

    connection:
        name: wobble
        driver: mysqli
        dbname: database
        host: localhost
        port: 3306
        user: grault
        password: flob
        charset: UTF-8

After parsing the configuration file, a `Common\ORM\Core\Connection` will be
added to the entity manager. The entity manager will also register that
connection as a service with name `@orm.connection.name`.

For example, for the previous configuration a service called
`@orm.connection.wobble` will be available to use in ORM configuration files
and, after loading the ORM, to use in every `services.yml` file.
