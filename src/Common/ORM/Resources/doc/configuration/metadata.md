# Metadata

Metadata defines an entity completely. It contains information about supported
properties, allowed types for each property, which properties are required and
how to store the information in one or more data sources.

A connection definition has to include the following data:

- `name`: The name of the entity. It is also the name of the class for entities
that have to follow the metadata restrictions.
- `properties`: A key-value list of known properties where key is the name of
the property and value is the supported type.
- `converters`: The list of converters that can convert this entity.
- `persisters`: The list of persisters that can persist this entity.
- `repositories`: The list of repositories that can get entities defined by this
metadata.
- `mapping`: The information to know how to convert, create, read, update and
delete entities from different data sources.

The following example describes an entity.

    entity:
        name: Foo
        properties:
            qux:    integer
            wubble: string
            glorp:  string
        converters:
            default:
                class:     Foo\Bar\Converter
                arguments: [ '@orm.metadata.foo' ]
        repositories:
            database:
                class:     Foo\Bar\Database\Repository\BaseRepository
                arguments: [ '@orm.connection.garply', '@orm.metadata.foo' ]
        persisters:
            database:
                class:     Common\ORM\Database\Persister\BasePersister
                arguments: [ '@orm.connection.garply', '@orm.metadata.foo' ]
        mapping:
            database:
                table: foo
                columns:
                    qux:
                        type:    bigint
                        options: { default: null, autoincrement: true }
                    wubble:
                        type:    string
                        options: { default: null, length: 100, notnull: true }
                    glorp:
                        type:    string
                        options: { default: null, length: 255, notnull: true }

## Name

The `name` property of Metadata is used to refer to the definition of an entity.
This `name` can be used when configuring converters, repositories or persisters
or when calling to `getMetadata()` method defined in `EntityManager`.

As a Metadata defines entities (types, a class that implements the defined type
has to be created. For example, to define and use an entity with name `Foo`, a
class `Foo` has to be created in `Common\Core\Entity` (recommended).

### Using entity Metadata as parameter

As stated before, the Metadata name is also used to pass the entity definition
to a converter, persister or repository.

To do so, refer to the definition by following the format `orm.metadata.<name>`,
where name is the name of the entity in lowercase.

> The entity defined above is used when defining converters as
> `@orm.metadata.foo`.

### Getting Metadata from other services

If some external service needs the Metadata for an entity, the `EntityManager`
provides the method `getMetadata(<name>)` that can be used as follows:

    $this->get('orm.manager')->getMetadata(<name>);

> For example, `$this->get('orm.manager')->getMetadata('Foo')` returns the
> Metadata for the entity `Foo`.

## Properties

An entity is meant to store one or more properties. Properties can support any
type or have a fixed type associated. To define the list of properties and types
you can use the `properties` property in Metadata.

Property are defined as follows:

    entity:
        properties:
            <property>: <type>

The supported types are: `array`, `integer`, `float`, `datetime`, `string`,
`object` and `entity::<name>`.

The `entity:<name>` is an especial type to define a property that stores another
entity defined in the data model.

> For example, `entity::Foo` defines a property to store an object of class
> `Foo`.

Defining types for properties is the easiest way to increase the security level
when handling data from external sources.

For more information about data types, see [Types](./mapping/types.md)

### Enumeration

If the data model needs to define a limited list of valid values for a property,
a enumeration can be defined in the Metadata itself so, when validing the entity
the value of the property will be checked against the enumeration.

An entity can define one or more enumerations and they only will be available
for the current entity.

Enumerations are defined as follows:

    entity:
        enum:
            <property>: [ <value-1>, <value-2>, ... ]

### Required properties

If an entity requires one or more properties, a list of required properties can
be defined as follows:
    entity:
        required:
            - <property-1>
            - <property-2>
            - ...

## Converters

Converters are components to convert data between data sources. A converted is
defined by a name, to get it from the `EntityManager`, its class and the
arguments needed to create a new instance.

    entity:
        converters:
            <name>:
                class:     Foo\Bar\Converter
                arguments: [ <arg-1>, <arg-2>, ... ]

To get a converter for an entity, the `EntityManager` provides the method
`getConverter(<name>)`

> For example, `$this->get('orm.manager')->getConverter('Foo')` returns a
> converter to convert entities of type `Foo`.

For more information about converters, see [Converters](./mapping/converters.md).

## Persisters

Persisters are components to save, updated and remove entities from data
sources. A persister is defined by a name, to get it from the `EntityManager`,
its class and the arguments needed to create a new instance.

    entity:
        persisters:
            <name>:
                class:     Foo\Bar\Persister
                arguments: [ <arg-1>, <arg-2>, ... ]

To get a persister for an entity, the `EntityManager` provides the method
`getPersister(<name>)`

> For example, `$this->get('orm.manager')->getPersister('Foo')` returns a
> persister to persist entities of type `Foo`.

For more information about persisters, see [Persisters](./persisters.md).

## Repositories

Repositories are components to find entities in data sources. A repository is
defined by a name, to get it from the `EntityManager`, its class and the
arguments needed to create a new instance.

    entity:
        repositories:
            <name>:
                class:     Foo\Bar\Repository
                arguments: [ <arg-1>, <arg-2>, ... ]

To get a repository for an entity, the `EntityManager` provides the method
`getRepository(<name>)`

> For example, `$this->get('orm.manager')->getRepository('Foo')` returns a
> repository to find entities of type `Foo`.

For more information about repositories, see [Repositories](./repositories.md).

## DataSets

DataSets are components to get and set data to a data source but treating that
data source as a key-value list. As they are basically key-value lists, they
only support searches by key.

Entities that use datasets are pseudo-entities, only keys and values are
manipulated, so no properties are defined in the metadata.

A dataset is defined by a name, to get it from the `EntityManager`,
its class and the arguments needed to create a new instance.

    entity:
        datasets:
            <name>:
                class:     Foo\Bar\DataSet
                arguments: [ <arg-1>, <arg-2>, ... ]

To get a dataset for an entity, the `EntityManager` provides the method
`getDataSet(<name>)`

> For example, `$this->get('orm.manager')->getDataSet('Foo')` returns a
> persister to get/set values of type `Foo`.

For more information about persisters, see [DataSets](./datasets.md).

## Mapping

The mapping information defines how entities have to be stored in the data
source. The mapping section has to include all mapping information for every
data source an entity can be stored in.

    entity:
        mapping:
            <datasource>:
                <datasource-mapping>

Every data source has its own format and they will be explained deeply in
[Mapping](./mapping.md).
