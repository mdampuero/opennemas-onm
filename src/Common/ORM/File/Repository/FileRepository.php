<?php
/**
 * This file is part of the Onm package.
 *
 * (c) Openhost, S.L. <onm-devs@openhost.es>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace Framework\ORM\File\Repository;

use Symfony\Component\Finder\Finder;
use Symfony\Component\Yaml\Yaml;
use Framework\ORM\Core\Entity;
use Framework\ORM\Core\Repository;
use Framework\ORM\Validation\Validator;

class FileRepository extends Repository
{
    /**
     * The array of entities in the repository.
     *
     * @var array
     */
    public $entities = [];

    /**
     * Initializes the Loader.
     *
     * @param ServiceContainer $cache The service container.
     * @param string           $paths The path to folders to load from.
     *
     * @throws InvalidArgumentException If the path is not valid.
     */
    public function __construct($container, $paths)
    {
        if (empty($paths)) {
            throw new \InvalidArgumentException(
                _('Unable to initialize the file repository. No folder specified.')
            );
        }

        $this->container = $container;
        $this->paths     = $paths;

        $this->load();
    }

    /**
     * Return all entities in the repository.
     *
     * @return array The list of entities in the repository.
     */
    public function findAll()
    {
        return $this->entities;
    }

    /**
     * Loads all items in the repository.
     */
    protected function load()
    {
        $finder = new Finder();

        foreach ($this->paths as $path) {
            $path = $this->container->getParameter('kernel.root_dir')
                . DS . '..' . DS . $path . DS;

            $finder->files()->in($path)->name('*.yml');

            foreach ($finder as $file) {
                $this->loadEntity($file->getRealPath());
            }
        }
    }

    /**
     * Load an entity from a file.
     *
     * @param string $path The path to the entity file.
     */
    protected function loadEntity($path)
    {
        $config = Yaml::parse(file_get_contents($path));
        $path   = str_replace(basename($path), '', $path);

        // TODO: Find another solution to include current theme path for themes
        $config['path'] = substr($path, strpos($path, '/themes'));

        if (empty($config)) {
            return;
        }

        $entityName = array_keys($config)[0];
        $class      = 'Framework\\ORM\\Entity\\' . \classify($entityName);

        try {
            if (class_exists($class)) {
                $entity = new $class($config[$entityName]);

                $this->container->get('orm.validator')->validate($entity);

                $this->entities[] = $entity;
            }
        } catch (\Exception $e) {
            var_dump($e->getMessage());
        }
    }
}
