# Data

The ORM handles data from different types and sources so it has to provide all
components required to perform data conversion and to grant that the handled
data are valid.

The data handling in the ORM is based always on the model. It is implemented in
PHP so the supported data types and data conversions have to be done with this
in mind.

## Types

As it is said, ORM must support all data types PHP language supports. It also
includes special types that only make sense in the context of the ORM.

The following list includes all types the ORM supports as types for entity
properties defined in \*.yml files.

* **array**: The property only supports array values.
* **boolean**: The property only supports boolean values.
* **datetime**: The property only supports DateTime objects.
* **entity**:**:&lt;entity-name&gt;**: The property only supports othe entities from the
  data model.
* **enum**: The property only supports values defined in an enumeration declared in
  the \*.yml file.
* **float**: The property only supports float values.
* **integer**: The property only supports integer values.
* **object**: The property only supports object values.
* **string**: The property only supports string values.

## Data conversion

The data conversion is done in two ways:
- Explicity when user gets data from request and creates a new entity with them.
- Implicity when `EntityManager` finds/persists entities in/to the data source.

To perform data conversion explicity the ORM provides converters with the basic
conversion fuctions but every data source should provide custom functions for
custom data conversions.

### Converters

Converters are components in charge of the data conversion basing on the
entities metadata.

The basic `Converter` provides functions to:
* `objectify`: Convert data from a Symfony request to valid values for entities.
* `responsify`: Convert data from entities to valid vaules for Symfony
  responses.

> Converters can handle single entities or an array of entities.

To execute data conversion, converters use `DataMapper` components. The mapper
the converter have to use for every property depends on the types defined in the
entity metadata.

The following list includes mappers included in the ORM.

* `ArrayDataMapper`: Converts data to array. Supports conversion of
  comma-separated strings (`simple_array`), JSON strings (`array_json`) and
  array serialized strings (`string|text`) to arrays.
* `BooleanDataMapper`: Converts data to boolean. Supports conversion of integers
  and strings to boolean.
* `DatetimeDataMapper`: Converts data to DateTime objects. Supports conversion
  of strings and DateTime to DateTime. If a property is defined as `datetimetz`
  in the metadata, the mapper will save the current DateTime in UTC timezone.
* `DoubleDataMapper`: Converts data to float, like float data mapper. It exists
  because some value types are identified as `double` instead of `float`
  sometimes.
* `EntityDataMapper`: Converts data to ORM entities. Supports conversions of
  array serialized strings to Entity.
* `EnumDataMapper`: Just for compatibility purposes. Tries to converts
  everything to string.
* `FloatDataMapper`: Converts data to float. Supports conversions of any type
  that can be casted to float.
* `IntegerDataMapper`: Converts data to integer. Supports conversions of any
  type that can be casted to integer.
* `NullDataMapper`: Just for compatibility purposes. When the value to convert
  is null it will converted to an empty array, empty string or 0 basing on the
  entity metadata.
* `ObjectDataMapper`: Converts data to objects. Supports conversions of
  serialized objects strings to objects.
* `StringDataMapper`: Converts data to strings. Supports conversions of any type
  that can be casted to string.

#### Convert from unknown source to data source

These are the steps executed when persisting data to a data source.

1. Get data from the unknown source.
2. Convert data from unknown source to entity values.
    1. Find out types of the source values.
    2. Check types defined in entity metadata properties.
    3. Convert.
3. Persist entity to data source
    1. Check types defined in entity metadata properties.
    2. Check types defined in entity metadata mapping for data source.
    3. Convert
    4. Save in data source

For example, when getting a request from the user and persiting it to a database
this are the steps executed.

**request** &rarr; objectify() &rarr; **entity** &rarr; databasify() &rarr; **data-source**

#### Convert from data source to external

These are the steps executed when getting data from a data source.

1. Find and get data from the data source.
2. Convert data from data source to entity values.
    1. Check types defined in entity metadata mapping for data source.
    2. Check types defined in entity metadata properties.
    3. Convert.
3. Create entities from the converted data
4. Return entities to external
    1. Check types defined in properties.
    2. Execute the custom external conversion function.
    3. Return the data.

For example, when getting an entity from the data source and returning it as a
HTTP response, this are the steps executed.

**data-source** &rarr; objectifyStrict() &rarr; **entity** &rarr; responsify() &rarr; **response**
