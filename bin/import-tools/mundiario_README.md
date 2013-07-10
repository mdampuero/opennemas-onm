# Steps for import mundiario


`php bin/console migrate:wordpress originDB finalDB`


## Mundiario
- Execute: `php bin/console migrate:wordpress wp-mundiario 71`
- Enter password root
- Default db table prefix => wp_
- Default origin url => http://www.mundiario.com
- Default origin media dir => /opt/backup_opennemas/mundiario/wp-content/uploads/



## Galiciamundiario
- Execute: `php bin/console migrate:wordpress wp-mundiario 81`
- Enter password root
- Default db table prefix => wp_2_
- Default origin url => http://www.mundiario.com/galicia
- Default origin media dir => /opt/backup_opennemas/mundiario/wp-content/blogs.dir/2/
- Default origin common media  => /opt/backup_opennemas/mundiario/wp-content/uploads/
- Instance name => galiciamundiario


## Emprendedoresmundiario
- Execute: `php bin/console migrate:wordpress wp-mundiario 80`
- Enter password root
- Default db table prefix => wp_3_
- Default origin url => http://www.mundiario.com/emprendedores
- Default origin media dir => /opt/backup_opennemas/mundiario/wp-content/blogs.dir/3/
- Default origin common media  => /opt/backup_opennemas/mundiario/wp-content/uploads/
- Instance name => empremundiario