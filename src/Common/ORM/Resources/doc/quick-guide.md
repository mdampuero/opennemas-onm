# Quick guide

This section is a quick guide to use on your day-by-day that includes basic
usage examples of the ORM.


## EntityManager

This section includes examples for the most commonly used EntityManager actions.

### Get entity manager from container

The following example describes how to get the EntityManager from the service
container.

    $this->get('orm.manager');

### Get a connection from the EntityManager

The following example describes how to get a connection from the EntityManager.

    $this->get('orm.manager')->getConnection(<name>);

    $this->get('orm.manager')->getConnection('manager');

> The name is defined in the \*.yml file for the connection.

### Get entity metadata from the EntityManager

The following example describes how to get the entity metadata for an Entity
from the EntityManager.

    $this->get('orm.manager')->getMetadata(<Entity name>);

    $this->get('orm.manager')->getMetadata('User');

> The Entity name is defined in the \*.yml for the Entity.

### Get a converter from the EntityManager

The following example describes how to get a converter from the EntityManager.

    $this->get('orm.manager')->getConverter(<Entity name>, <converter name>?);

    $this->get('orm.manager')->getConverter('User');
    $this->get('orm.manager')->getConverter('User', 'manager');

> The coverter name is defined in the converters section in the \*.yml file for
> the entity.

### Get a repository from the EntityManager

The following example describes how to get a repository for entities to execute
searches in the data source.

    $this->get('orm.manager')->getRepository(<Entity name>, <repository name>?);

    $this->get('orm.manager')->getRepository('Instance');
    $this->get('orm.manager')->getRepository('User', 'manager');

> The repository name is defined in the repositories section in the  \*.yml for
> the Entity.

### Persist an Entity to the data source

The following example describes how to persist (create or update) entities to
the data source.

    $this->get('orm.manager')->persist(<Entity>);

    $this->get('orm.manager')->persist($entity);


## Connections

This section includes examples for the most commonly used database actions.

### Save data in database

The following example describes how to execute an insert query in database using
a database connection directly.

    $connection->insert(<table>, <data>, <types>);

    $connection->insert([ 'name' => 'Greg' ], 'users', [ \PDO::PARAM_STR ]);

### Update data in database

The following example describes how to execute an update query in database using
a database connection directly.

    $connection->update(<table>, <data>, <id>, <types>);

    $connection->update('users', [ 'name' => 'Greg' ], [ 'id' => 1 ], [ \PDO::PARAM_STR ]);


### Delete datata from database

The following example describes how to execute a delete query from database using
a database connection directly.

    $connection->delete(<table>, <id>);

    $connection->delete('users', [ 'id' => 1 ]);

### Execute a query
The following example describes how to execute a SQL query in database using
a database connection directly.

    $connection->executeQuery(<sql>, <params>, <types>);

    $connection->executeQuery('SELECT COUNT(*) FROM users WHERE id > ?', [ 1 ], [ \PDO::PARAM_INT ]);


## Metadata

This section includes examples for the most commonly used database actions.

### Get id from an Entity
The following example describes how to get the id from an Entity by using the
Entity metadata class.

    $metadata->getId(<Entity>);

    $metadata->getId($user); // Returns 1
    $metadata->getId([ 'id' => 1, 'name' => 'Foo' ]); // Returns 1

### Get prefixed id from an Entity (used for caches)
The following example describes how to get the prefixed id from an Entity by
using the Entity metadata class.

    $metadata->getId(<Entity>);

    $metadata->getPrefixedId($user); // Returns user-1
    $metadata->getPrefixedId([ 'id' => 1 ]); // Returns user-1

## Repositories

This section includes examples for the most commonly used repository actions.

### Find an entity by id

The following example describes how to find entities by id.

    $repository->find(<id>);

    $repository->find(3);
    $repository->find([ 'notification_id' => 3, 'instance_id' => 4 ]);

> This action will throw an exception if there are no entities with the given
> id.

### Count the number of entities that match a criteria

The following example describes how to get the number of entities that match a
criteria.

    $repository->countBy(<oql>);

    $repository->countBy('id > 4');

### Find a list of entities that match a criteria

The following example describes how to find the list of entities that match a
criteria.

    $repository->findBy(<oql>);

    $repository->findBy('name ~ "John"')

> This action will return an empty list if there are no entities that match the
> criteria.

### Find the first entity that matches a criteria

The following example describes how to get the first Entity that matches a
criteria.

    $repository->findOneBy(<oql>);

    $repository->findOneBy('created !is null');

> This action will throw an exception if there are no entities that match the
> criteria.

## Converters

This section includes examples for the most commonly used converter actions.

### Convert data from request values to Entity values

The following example describes how to convert data from a request and use them
to create a new entity.

    $converter->objectify(<data>);

    $data   = $converter->objectify($request->request->all());
    $entity = new Entity($data);

### Convert data from Entity to data source values

The following example describes how to convert data from an Entity to persist
them to the data source.

    $converter->responsify(<data>);

    $data = $converter->responsify($entity);
    $data = $converter->responsify([ 'name' => 'Foo' ]);

### Convert data from data source values to Entity values

The following example describes how to convert data from a data source to Entity
values.

    $converter->objectifyStrict(<data>);

    $data   = $converter->objectifyStrict([ 'name' => 'Foo' ]);
    $entity = new Entity($data);


### Convert data from Entity values to response values

The following example describes how to convert data from Entity to return them
in a Symfony response object.

    $converter->responsify(<data>);

    $data = $converter->responsify($entity);
    $data = $converter->responsify([ 'name' => 'Foo' ]);

    return new JsonResponse([ 'entity' => $data ]);
