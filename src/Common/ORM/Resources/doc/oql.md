# OQL

OQL (from Openhost Query Language) is a query-based language created to provide
an uniform infrastructure-independent mechanism to find resources from any data
source. It is based in Atlassian's JIRA JQL.

OQL was firstly created to pass complex queries as URL parameters so it has to
be simple enough to fit URL standards and complex enough to allow advanced
queries.

OQL focuses on:

* Easy to read
* Easy to write
* Strong field validation against schema reference
* Technology independency

It is useful to filter a list of results basing on a set of parameters but it
can not specify the resource to search by itself. OQL is just an simplification
of the way of filtering results from the data source, it is not a replacement.
To specify the resource to search you have to still using other mechanism.

For example, you can search an user called 'Carlos' from 'Madrid' with the
following OQL query:

    name ~ '%Carlos%' AND city = 'Madrid'

The way filters are written is clear and simple but how do you specify that you
are looking for users and not any other type of resource? As it was said, OQL is
just for filtering.

To set the type of resource you have to implement other components that allow
you to specify the resource type. This are the components that will use OQL
parsers and translators.

## Language details

This section describes how to write OQL queries and describes the list of
available connectors and operators.

### Conditions

A OQL condition has the following structure:

    <field> <operator> <field|value>

Where:

* **&lt;field&gt;**: Is the name of the property or attribute to filter by. The matcher will validate this value basing on the data source reference schema.
* **&lt;operator&gt;**: Indicates the filtering operation.
* **&lt;value&gt;**: Is the value used to filter by.

> It is recommemded to use whitespaces between field and operators and between
> operators and values. Some queries will not work without them.

#### Field

The parameter name has to match the following pattern.

    [a-zA-Z0-9\_\.]+

According to that pattern the following examples are valid parameters:

    name
    foo.bar
    user_id

#### Operators

The following table collects the list of allowed OQL operators.

| Operator | Description                |
|:--------:|:---------------------------|
| =        | Equals to                  |
| ~        | Like                       |
| <        | Less than                  |
| <=       | Less or equals to          |
| >=       | Greater than               |
| >=       | Greater or equals to       |
| !=       | Distinct                   |
| in       | In set                     |
| !in      | Not in set                 |
| is       | Is                         |
| !is      | Is                         |
| regexp   | Matches the pattern        |
| !regex   | Does not match the pattern |

#### Values

Depending on their type, values have to match one or another pattern.

##### Boolean

    /true|false/

##### Datetime

    /\'\d{4}\-\d{2}\-\d{2} \d{2}\:\d{2}\:\d{2}\'|\"\d{4}\-\d{2}\-\d{2} \d{2}\:\d{2}\:\d{2}\"/

##### Null value

    /null/

##### Float

    /-?[0-9]+\.[0-9]+/

##### Integer

    /-?[0-9]+/

##### String

    /\'[^\']*\'|\"[^\"]*\"/

##### Array

The type array is an special type. It must be a comma-separated list of values
between brackets, where the values can be of any previously defined type.

    /\[<value>(, <values)*\]/


To create a long complex OQL query you need to connect simpler and smaller OQL
queries. This section describes the available connectors and pseudo-connectors
that can be used to connect OQL queries.

#### Connectors

The connectors allow to join two or more conditions into a single OQL query. The
following table collects the list of allowed OQL connectors.

| Connector | Description                                        |
|:----------|:---------------------------------------------------|
| and       | Connects two or more conditions with a logical and |
| or        | Connects two or more conditions with a logical or  |

Here some examples with connectorsc in the OQL query.

    id = 1 and created !is null
    name ~ 'Foo' or name = "Bar"

#### Groupers

The groupers allow to change the way the OQL is evaluated. There are two types
of groupers.

##### Value groupers

Group two or more values to evaluate them as an array.

| Grouper | Description          |
|:--------|:---------------------|
| [       | Opening array marker |
| ]       | Closing array marker |

Here some examples with value groupers in the OQL query

    id in [ 1, 2, 3 ]
    name in [ 'foo', 'bar' ]

##### Condition groupers

Group two or more conditions to change the evaluation order.

| Grouper | Description                    |
|:--------|:-------------------------------|
| (       | Opening condition group marker |
| )       | Closing condition group marker |

Here some examples with conditions groupers in the OQL query.

    (id = 1 and created !is null)
    id > 3 and (name ~ 'Foo' or name is null)

#### Modifiers

After specifing a list of filters, the OQL can add some modifiers to the OQL to
return the results on a concrete order or limit the amount of results.

The following table collects the list of allowed OQL modifiers.

| Grouper  | Description                                                |
|:---------|:-----------------------------------------------------------|
| order by | Return the results order by a field                        |
| asc      | Used with order by, return results in ascendant order      |
| desc     | Used with order by, return results in descendant order     |
| limit    | Return only a limited number of results                    |
| offset   | Used with limit, return results ignoring the first results |

Here some examples with modifiers in the OQL query.

    clicks > 1000 limit 3 offset 2
    order by id asc
