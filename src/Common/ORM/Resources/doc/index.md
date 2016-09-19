# ORM

The ORM (Object-Relational Mapping) bundle defines components that allow you to
define a platform-independent data model where:

- Data could be stored in one or more sources.
- Sources could have the same or different types.
- Contents could be stored in one or more sources.

It was built basing on concepts present in Doctrine ORM but focusing
on simplicity and performance. Some points like repositories and schemas will be
familiar but they may not be equivalent to the Doctrine ones.

This ORM focuses on:

- **Simplicity**
    - Easy to define entities to data model.
    - Easy to create entities from user requests.
    - Easy to get and set entities from the data source.
- **Scalability**
    - Easy to add new entities to data model.
    - Easy to add custom data handling components.
- **Adaptability**
    - Easy to define an existing data source.
    - Easy to create custom compoenents for edge cases.
- **Security**
    - Strong data validation.
    - Secure data handling.

This documentation will cover all points to get a complete vision of how the ORM
works and how to extend it for concrete cases. It includes the following:

0. [Quick guide](./quick-guide.md)
1. [Overview](./overview.md)
2. [Configuration](./configuration.md)
    1. [Connection](./configuration/connection.md)
    2. [Metadata](./configuration/metadata.md)
    3. [Schema](./configuration/schema.md)
3. [Repositories](./repositories.md)
4. [Persisters](./persisters.md)
5. [DataSets](./datasets.md)
6. [Data: types and conversion](./data.md)
8. [OQL](./oql.md)
