# Overview

The ORM layer uses some unique terms to refer to the different parts of the ORM.
These terms should be clear before continuing reading the current documentation.

**Converter**

Component to convert data between data sources and entities.

**Entity**

Object that represents concepts from real life. It will be eventually stored in
a data source.

**EntityManager**

Main component of the ORM. Provides methods to:

- Get entities metadata.
- Get repositories for entities.
- Persist and remove entities.

**Loader**

Component to load the ORM configuration.

**Metadata**

Object that completely defines an entity (properties, types, data source, etc.).

**OQL**

From Openhost Query Language, is a platform-independent query language to search
entities in data sources.

**Persister**

Component that provides methods to save, update or remove entities from a data
source.

**Schema**

Object that groups entities. It represents a data source.

**Repository**

Component that provides methods to get entities from a data source.

## Quick start
This section offers a quick vision of how to work with the ORM.

### Getting the `EntityManager`
The ORM defines a service `orm.manager`. It is a class `EntityManager`
initialized with the configuration defined in the data model.

    $em = $this->get('orm.manager');

### Getting entities
Repositories are used to get entities from a data source. The following example
shows how to get an entity with id = 1.

    $repository = $this->get('orm.manager')->getRepository('Entity');

    $entity = $repository->find(1);

If there are more than one repository defined in the configuration, the
repository name to use can be passed to `getRepository` as parameter.

    $repository = $this->get('orm.manager')->getRepository('Entity', 'repository');

    $entity = $repository->find(1);

### Saving entities
To save an entity the `EntityManager` provides a method `persist`.

    // Get entity from somewehere
    ...

    $this->get('orm.manager')->persist($entity);

Internally, the `EntityManager` will use a persister defined in the entity
metadata.

If there are more than one persister defined in the configuration, the persister
name to use can be passed to `persist` as parameter.

    // Get entity from somewehere
    ...

    $this->get('orm.manager')->persist($entity, 'persister');

### Removing entities
To remove an entity the `EntityManager` provides a method `remove`.

    // Get entity from somewehere
    ...

    $this->get('orm.manager')->remove($entity);

Internally, the `EntityManager` will use a persister defined in the entity
metadata.

If there are more than one persister defined in the configuration, the persister
name to use can be passed to `remove` as parameter.

    // Get entity from somewehere
    ...

    $this->get('orm.manager')->remove($entity, 'persister');
