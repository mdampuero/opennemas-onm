# DataSets

DataSets are components to get, set and remove values from data sources.

Repositories and persisters are designed to work with entities. DataSets are
designed to work with single values. They provide an abstration layer that allow
to treat the data source like a key-value storage.

By default, if the data source supports it, every data source should provide a
generic dataset to get and set values .

> By convention, datasets are created in
> `<DataSource>\DataSet\<DataSet>` but they can be created anywhere as
> configuration file includes the full class name.

All datasets have to extend abstract DataSet class. The definition of
this class is included below.

    public class DataSet {
        /**
         * Removes one or more values from the data set.
         *
         * @param mixed $key A key or an array of keys.
         */
        abstract public function remove($key);

        /**
         * Returns one or more values from the data set.
         *
         * @param mixed $key     A key or an array of keys and default values.
         * @param mixed $default When using a single key, the value to use by
         *                       default.
         *
         * @return mixed The value or an array with the found values.
         */
        abstract public function get($key, $default = null);

        /**
         * Saves one or more values to the data set.
         *
         * @param mixed $key   A key or an array of keys and values to save.
         * @param mixed $value When using a single key, the value to save
         */
        abstract public function set($key, $value = null);
    }

> Not all datasets will provide an `update()` method so it have to be
> implemented when required.

## Define a dataset

DataSets are defined in the datasets section in the \*.yml files for
entities. The definition must follow this format.

    entity:
        datasets:
            <name>:
                class:     Full/Class/Name
                arguments: [ <arg-1>, <arg-2>, ... ]

When calling `getDataSet()` from `EntityManager`, this definition will
become the following.

    $dataset = new Name(<name>, <arg-1>, <arg-2>);

> Note that datasets don't include the dataset name in the list of arguments as
> repositories do.

## Use a dataset

Basing on the metadata for entities, the `EntityManager` knows which
datasets are available to use. To get a dataset for an Entity the
`EntityManager` provides the method `getDataSet()`.

    $em->getDataSet(<entity name>, <dataset name>?)

The entity name is the name defined in the \*.yml file. It can be passed in
lowercase or uppercase as `EntityManager` will try to do the conversion.

> To avoid potential errors when requiring datasets it is recommended to use
> the entity name defined in the \*.yml file literally.

The dataset name is optional. If it is not provided, the first dataset in
the list will be returned.

### Save values to the data source

To save values to the data source datasets provide the function `set()`. This
action can handle single key-value or an array of keys-values.

    $dataset->set(<key>, <value>):
    $dataset->set(<key-value>):

    $dataset->set('foo', 'bar');
    $dataset->set([ 'foo' => 'bar', 'qux' => 'frog' ]);

### Get values from data source

To get values from the data source datasets provide the function `get()`. This
action can handle single keys or an array of keys. It also supports default
values to return if the searched key(s) are not found in the data source.

    $dataset->get(<key>, <default>?):

    $dataset->get('foo');
    $dataset->get('foo', 'waldo');
    $dataset->get([ 'foo', 'qux' ]);
    $dataset->get([ 'foo', 'qux' ], [ 'waldo', 'fubar' ]);

### Remove values from data source

To remove values from the data source datasets provide the function
`remove()`. This action can handle single keys or an array of keys.

    $dataset->remove(<key(s)>):

    $dataset->remove('foo');
    $dataset->remove([ 'foo', 'qux' ]);
