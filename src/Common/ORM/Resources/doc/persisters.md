# Persisters

Persisters are components to store and remove data from data sources. By
default, every data source should provide a generic persister to store and
remove entities from it.

To execute custom actions to persist, remove or parse data from the data source
a custom persister has to be created and added to the entity configuration file.

> By convention, persisters are created in
> `<DataSource>\Persister\<Persister>` but they can be created anywhere as
> configuration file includes the full class name.

All persisters have to extend abstract Persister class. The definition of
this class is included below.

    public class Persister {
        /**
         * Saves an entity.
         *
         * @param Entity $entity The entity to save.
         */
        abstract public function create(Entity &$entity);

        /**
         * Removes an entity.
         *
         * @param Entity $entity The entity to remove.
         *
         * @throws EntityNotFoundException If the entity doesn't exist.
         */
        abstract public function remove(Entity $entity);
    }

> Not all persisters will provide an `update()` method so it have to be
> implemented when required.

## Define a persister

Persisters are defined in the persisters section in the \*.yml files for
entities. The definition must follow this format.

    entity:
        persisters:
            <name>:
                class:     Full/Class/Name
                arguments: [ <arg-1>, <arg-2>, ... ]

When calling `getPersister()` from `EntityManager`, this definition will
become the following.

    $persister = new Name(<arg-1>, <arg-2>);

> Note that persisters don't include the persister name in the list of
> arguments as repositories do.

## Use a persister

Basing on the metadata for entities, the `EntityManager` knows which
persisters are available to use. To get a persister for an Entity the
`EntityManager` provides the method `getPersister()`.

    $em->getPersister(<entity name>, <persister name>?)

The entity name is the name defined in the \*.yml file. It can be passed in
lowercase or uppercase as `EntityManager` will try to do the conversion.

> To avoid potential errors when requiring persisters it is recommended to use
> the entity name defined in the \*.yml file literally.

The persister name is optional. If it is not provided, the first persister in
the list will be returned.

Using a persister directly is not recommended. To persist and remove entities
the `EntityManager` provides the methods `persist()` and `remove()`. They
internally use a persister.

    $em->persist(<entity>, <persiter-name>?);

    // Save an entity
    $em->persist($entity);
    $em->persist($entity, 'foo');

    // Update an entity
    $em->persist($entity);
    $em->persist($entity, 'foo');

    // Remove an entity
    $em->remove($entity);
    $em->remove($entity, 'foo');

> It is recommended to use the `persist()` method from the `EntityManager`
> because it will automagically know when to use `save()` method or `update()`
> method from persister.

### Save an entity

To save a new entity to the data source persisters provide the function
`save()`. The action `persist()` from `EntityManager` can also be used if the
entity was never saved.

    // Save using a persister
    $persister->save(<entity>):

    $persister->save($entity);

    // Save using the EntityManager
    $em->persist(<entity>, <persister-name>?);

    $em->persist($entity);
    $em->persist($entity, 'foo');

### Update an entity

To update a new entity to the data source persisters provide the function
`update()`. The action `persist()` from `EntityManager` can also be used if the
entity was already saved.

    // Update using a persister
    $persister->update(<entity>):

    $persister->update($entity);

    // Update using the EntityManager
    $em->persist(<entity>, <persister-name>?);

    $em->persist($entity);
    $em->persist($entity, 'foo')

### Remove an entity

To remove an entity from the data source persisters provide the function
`remove()`. The action `remove()` from `EntityManager` can also be used.

    // Remove using a persister
    $persister->remove(<entity>):

    $persister->remove($entity);

    // Remove using the entity manager
    $em->remove(<entity>, <persister-name>);

    $em->remove($entity);
    $em->remove($entity, 'foo');
