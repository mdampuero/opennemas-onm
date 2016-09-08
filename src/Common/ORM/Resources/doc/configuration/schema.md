# Schema

A schema defines a data source by grouping all entities the source data can work
with.

A schema definition has to include the following data:

- `name`: A name to refer the schema.
- `entities`: All entities that the schema groups.

> To define the schema properly, all entities included in the schema have to be
> defined in the ORM configuration.

The following example describes an schema with 2 entities.

    schema:
        name: myschema
        entities:
            - Content
            - Author

After parsing the configuration file, a `Common\ORM\Core\Schema\Schema` will be
added to the entity manager. This schema can be used as argument for the
`orm:schema:check` command.

> Even though a schema can define any data source the ORM can work with, right
> now, it is only used to describe a database.
