<?php
/**
 * This file is part of the Onm package.
 *
 * (c)  OpenHost S.L. <developers@openhost.es>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 *
 * @author Openhost Developers <onm-dev@openhost.es>
 *
 */
namespace Framework\Command;

use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Doctrine\DBAL\Schema\Schema;
use Symfony\Component\Yaml\Yaml;

use Onm\DatabaseConnection;

class DatabacheCheckSchemaCommand extends ContainerAwareCommand
{
    /**
     * Path to the file with the master schema for an instance.
     *
     * @var string
     */
    protected $instanceSchemaPath = 'db/schema-instance.yml';

    /**
     * Path to the file with the master schema for the manager database.
     *
     * @var string
     */
    protected $managerSchemaPath = 'db/schema-manager.yml';

    /**
     * Configures the current command.
     */
    protected function configure()
    {
        $this
            ->setName('database:check-schema')
            ->setDescription('Handles the current database schema.')
            ->addArgument(
                'database',
                InputArgument::REQUIRED,
                'Database.'
            )
            ->addOption(
                'manager',
                null,
                InputOption::VALUE_NONE,
                'Whether to check manager schema or an instance schema (default: instance)'
            );
    }

    /**
     * Executes the current command.
     *
     * @param InputInterface  $input  An InputInterface instance
     * @param OutputInterface $output An OutputInterface instance
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $database = $input->getArgument('database');

        $this->path = $this->instanceSchemaPath;
        if ($input->getOption('manager')) {
            $this->path = $this->managerSchemaPath;
        }

        // $this->dumpSchema('onm-instances');

        $master = Yaml::parse(file_get_contents(APPLICATION_PATH.'/'.$this->path));
        $master = $this->createSchema($master);

        $schema = $this->getSchema($database);
        $conn = getService('dbal_connection');
        $conn = $conn->selectDatabase($database);

        $sql = $schema->getMigrateToSql($master, $conn->getDatabasePlatform());

        if (count($sql) > 0) {
            $output->writeln("use `$database`;");
            foreach ($sql as $value) {
                $output->writeln($value.';');
            }
        }
    }

    // /**
    //  * Cleans column options.
    //  *
    //  * @param  array $options Column options.
    //  * @return array          Cleaned column options.
    //  */
    // private function cleanOptions($options)
    // {
    //     unset($options['name']);
    //     unset($options['type']);

    //     if ($options['length'] == null) {
    //         unset($options['length']);
    //     }

    //     if ($options['notnull'] == true) {
    //         unset($options['notnull']);
    //     }

    //     if ($options['precision'] == 10) {
    //         unset($options['precision']);
    //     }

    //     if ($options['scale'] == 0) {
    //         unset($options['scale']);
    //     }

    //     if ($options['fixed'] == false) {
    //         unset($options['fixed']);
    //     }

    //     if ($options['unsigned'] == false) {
    //         unset($options['unsigned']);
    //     }

    //     if ($options['autoincrement'] == false) {
    //         unset($options['autoincrement']);
    //     }

    //     if ($options['columnDefinition'] == null) {
    //         unset($options['columnDefinition']);
    //     }

    //     if ($options['comment'] == '') {
    //         unset($options['comment']);
    //     }

    //     return $options;
    // }

    /**
     * Creates a Schema from the master schema file.
     *
     * @param  array  $input Master schema as array.
     * @return Schema        Master schema.
     */
    private function createSchema($input)
    {
        $schema = new Schema();

        foreach ($input as $table => $definition) {
            $table = $schema->createTable($table);

            // Populate column definitions
            foreach ($definition['columns'] as $field => $value) {
                $options = array_key_exists('options', $value) ? $value['options'] : array();
                $table->addColumn($field, $value['type'], $options);
            }

            // Populate index definitions
            foreach ($definition['index'] as $field => $value) {
                if (!array_key_exists('name', $value)) {
                    $value['name'] = null;
                }
                if (array_key_exists('primary', $value) && $value['primary']) {
                    $table->setPrimaryKey($value['columns'], $value['name']);
                } elseif (array_key_exists('unique', $value) && $value['unique']) {
                    $table->addUniqueIndex($value['columns'], $value['name']);
                } else {
                    $table->addIndex($value['columns'], $value['name']);
                }
            }
        }

        return $schema;
    }

    /**
     * Dumps the given database schema.
     *
     * @param string $database Database name.
     */
    // private function dumpSchema($database)
    // {
    //     $schema = array();

    //     $conn = getService('dbal_connection');
    //     $conn = $conn->selectDatabase($database);

    //     $sm = $conn->getSchemaManager();

    //     foreach ($sm->listTables() as $table) {
    //         $name = $table->getName();
    //         $columns = array();
    //         foreach ($table->getColumns() as $column) {
    //             $options = $this->cleanOptions($column->toArray());

    //             $columns[$column->getName()] = array(
    //                 'type'    => $column->getType()->getName(),
    //                 'options' => $options
    //             );
    //         }

    //         $indexes = array();
    //         foreach ($sm->listTableIndexes($name) as $index) {
    //             $newIndex = array(
    //                 'name'    => $index->getName(),
    //                 'columns' => $index->getColumns(),
    //                 'primary' => $index->isPrimary(),
    //                 'unique'  => $index->isUnique(),
    //                 'flags'   => array()
    //             );
    //             if ($index->hasFlag('fulltext')) {
    //                 $newIndex['flags']['fulltext'] = true;
    //             }

    //             $indexes[] = $newIndex;
    //         }

    //         $schema[$name] = array(
    //             'columns' => $columns,
    //             'index'   => $indexes
    //         );
    //     }

    //     $yml = Yaml::dump($schema, 4);
    //     file_put_contents($this->path, $yml);
    // }

    /**
     * Gets the schema for the given database.
     *
     * @param  string $database The database name.
     * @return array            The database schema.
     */
    private function getSchema($database)
    {
        $conn = getService('dbal_connection');
        $conn = $conn->selectDatabase($database);

        $sm = $conn->getSchemaManager();

        return $sm->createSchema();
    }
}
