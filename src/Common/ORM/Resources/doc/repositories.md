# Repositories

Repositories are components to count and find entities in data sources. By
default, every data source should provide a generic repository to count and find
entities by using OQL queries.

To execute custom actions to count, find or parse data from the data source a
custom repository has to be created and added to the entity configuration file.

> By convention, repositories are created in
> `<DataSource>\Repository\<Repository>` but they can be created anywhere as
> configuration file includes the full class name.

All repositories have to extend abstract Repository class. The definition of
this class is included below.

    public class Repository {
        /**
         * Counts the number of entities that match the criteria.
         *
         * @param strign $oql The criteria.
         *
         * @return integer The number of entities.
         */
        abstract public function countBy($oql = '');

        /**
         * Finds an entity by id.
         *
         * @param mixed $id The entity id.
         *
         * @return Entity The entity.
         *
         * @throws EntityNotFoundException  If the entity is not found.
         * @throws InvalidArgumentException If the given id is invalid.
         */
        abstract public function find($id);

        /**
         * Finds entities that match a criteria.
         *
         * @param string $oql The criteria.
         *
         * @return array The list of entities.
         */
        abstract public function findBy($oql = '');

        /**
         * Returns the first entity that matches a criteria.
         *
         * @param string $oql The criteria.
         *
         * @return Entity The entity.
         *
         * @throws EntityNotFoundException If the entity is not found.
         */
        abstract public function findOneBy($oql = '');
    }

## Define a repository

Repositories are defined in the repositories section in the \*.yml files for
entities. The definition must follow this format.

    entity:
        repositories:
            <name>:
                class:     Full/Class/Name
                arguments: [ <arg-1>, <arg-2>, ... ]

The `EntityManager` will pass the repository name as the first parameter in the
list of arguments for the constructor so the <arg-1> in the \*.yml is the 2nd
argument in the constructor, the <arg-2> is the 3rd and so on.

    $repository = new Name(<name>, <arg-1>, <arg-2>);

## Use a repository

Basing on the metadata for entities, the `EntityManager` knows which
repositories are available to use. To get a repository for an Entity the
`EntityManager` provides the method `getRepository()`.

    $em->getRepository(<entity name>, <repository name>?)

The entity name is the name defined in the \*.yml file. It can be passed in
lowercase or uppercase as `EntityManager` will try to do the conversion.

> To avoid potential errors when requiring repositories it is recommended to use
> the entity name defined in the \*.yml file literally.

The repository name is optional. If it is not provided, the first repository in
the list will be returned.

### Count entities that match a criteria

To count the number of entities that match a criteria repositories provide the
function `countBy()`

    $repository->countBy(<oql>):

    // Count all entities that include 'Foo' in the name
    $repository->countBy('name ~ "Foo"');

> The criteria is passed as an OQL query.

### Find entities by id

To find entities by id repositories provide the function `find()`.

If the entity has a simple key only the value has to be passed as argument. If
the entity has a composite key a key-value array has to be passed as argument
where keys are the name of the keys and values the required values.

    $repository->find(<id>);

    // Find an entity with id = 1
    $repository->find(1);

    // Find an entity with composite key foo_id = 1 and bar_id = 3
    $repository->find([ 'foo_id' => 1, 'bar_id' => 3 ]);


### Find entities that match a criteria

To find entities that match a criteria repositories provide the function
`findBy()`.

    $repository->findBy(<oql>);

    // Find all entities that include 'Foo' in the name
    $repository->findBy('created !is null');

> The criteria is passed as an OQL query.
