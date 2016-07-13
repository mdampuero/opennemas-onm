<?php
/**
 * This file is part of the Onm package.
 *
 * (c) Openhost, S.L. <developers@opennemas.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace Common\ORM\File\Repository;

use Common\Cache\Core\Cache;
use Common\ORM\Core\Entity;
use Common\ORM\Core\Oql\Php\PhpTranslator;
use Common\ORM\Core\Exception\EntityNotFoundException;
use Common\ORM\Core\Metadata;
use Common\ORM\Core\Repository;
use Common\ORM\Database\Data\Converter\BaseConverter;
use Symfony\Component\Finder\Finder;
use Symfony\Component\Yaml\Yaml;

class BaseRepository extends Repository
{
    /**
     * The entity converter.
     *
     * @var BaseConverter
     */
    protected $converter;

    /**
     * The array of entities in the repository.
     *
     * @var array
     */
    public $entities = [];

    /**
     * The entity metadata.
     *
     * @var Metadata.
     */
    protected $metadata;

    /**
     * Initializes the Loader.
     *
     * @param ServiceContainer $cache The service container.
     * @param string           $paths The path to folders to load from.
     * @param Metadata         $metadata The entity metadata.
     *
     * @throws InvalidArgumentException If the path is not valid.
     */
    public function __construct($container, $paths, Metadata $metadata, Cache $cache)
    {
        if (empty($paths)) {
            throw new \InvalidArgumentException(
                _('Unable to initialize the file repository. No folder specified.')
            );
        }

        $this->container = $container;
        $this->cache     = $cache;
        $this->converter = new BaseConverter($metadata);
        $this->metadata  = $metadata;
        $this->paths     = $paths;
        $this->translator = new PhpTranslator($metadata);

        $this->load();
    }

    /**
     * {@inheritdoc}
     */
    public function countBy($oql = '')
    {
        list($filter, $order, $size, $offset) = $this->translator->translate($oql);

        $entities = $this->entities;

        if (!empty($filter)) {
            $entities = [];

            foreach ($this->entities as $entity) {
                if (eval($filter)) {
                    $entities[] = $entity;
                }
            }
        }

        return count($entities);
    }

    /**
     * {@inheritdoc}
     */
    public function find($id)
    {
        if (empty($id)) {
            throw new \InvalidArgumentException();
        }

        $id = $this->metadata->normalizeId($id);

        foreach ($this->entities as $entity) {
            $data = array_intersect_key($entity->getData(), $id);

            if ($data === $id) {
                return $entity;
            }
        }

        throw new EntityNotFoundException($this->metadata->name, $id);
    }

    /**
     * {@inheritdoc}
     */
    public function findBy($oql = '')
    {
        list($filter, $order, $size, $offset) = $this->translator->translate($oql);

        $entities = $this->entities;

        if (!empty($filter)) {
            $entities = [];

            foreach ($this->entities as $entity) {
                if (eval($filter)) {
                    $entities[] = $entity;
                }
            }
        }

        if (!empty($order)) {
            $this->sort($entities, $order);
        }

        if (!empty($size) && !empty($entities)) {
            return array_values(array_chunk($entities, $size)[$offset]);
        }

        return array_values($entities);
    }

    /**
     * {@inheritdoc}
     */
    public function findOneBy($oql = '')
    {
        $oql = preg_replace('/limit\s*\d+/', '', $oql) . ' limit 1';
        $rs  = $this->findBy($oql);

        if (!empty($rs)) {
            return array_pop($rs);
        }

        throw new EntityNotFoundException($this->metadata->name);
    }

    /**
     * Evaluates an entity property basing on a comparison method and the value
     * to compare to.
     *
     * @param Entity $entity   The entity to evaluate.
     * @param string $method   The method name.
     * @param string $property The property to evaluate.
     * @param mixed  $value    The value to compare to.
     *
     * @return boolean True if the property value passes the comparison. False
     *                 otherwise.
     */
    protected function evaluate($entity, $method, $property, $value)
    {
        if (is_array($entity->{$property})) {
            foreach ($entity->{$property} as $v) {
                if ($this->{$method}($v, $value)) {
                    return true;
                }
            }

            return false;
        }

        return  $this->{$method}($entity->{$property}, $value);
    }

    /**
     * Checks if the expected value is equals to the current value.
     *
     * @param mixed $current  The current value.
     * @param mixed $expected The expected value.
     *
     * @return boolean True if the expected value is equals to the current
     *                 value. False otherwise.
     */
    protected function isEquals($current, $expected)
    {
        return trim($expected, '"') === $current;
    }

    /**
     * Checks if the expected value is greater than the current value.
     *
     * @param mixed $current  The current value.
     * @param mixed $expected The expected value.
     *
     * @return boolean True if the expected value is greater than the current
     *                 value. False otherwise.
     */
    protected function isGreat($current, $expected)
    {
        return trim($expected, '"') > $current;
    }

    /**
     * Checks if the expected value is greater than or equals to the current
     * value.
     *
     * @param mixed $current  The current value.
     * @param mixed $expected The expected value.
     *
     * @return boolean True if the expected value is greater than or equals to
     *                 the current value. False otherwise.
     */
    protected function isGreatEquals($current, $expected)
    {
        return trim($expected, '"') >= $current;
    }

    /**
     * Checks if the expected value is in the current array of values.
     *
     * @param mixed $current  The current value.
     * @param mixed $expected The expected value.
     *
     * @return boolean True if the expected value is in the current array of
     *                 False otherwise.
     */
    protected function isInArray($current, $expected)
    {
        if (!is_array($current)) {
            return false;
        }

        return in_array($expected, $current);
    }

    /**
     * Checks if the expected value is lesser than current value.
     *
     * @param mixed $current  The current value.
     * @param mixed $expected The expected value.
     *
     * @return boolean True if the expected value is lesser the current value.
     *                 False otherwise.
     */
    protected function isLess($current, $expected)
    {
        return trim($expected, '"') < $current;
    }

    /**
     * Checks if the expected value is lesser than or equals to the current
     * value.
     *
     * @param mixed $current  The current value.
     * @param mixed $expected The expected value.
     *
     * @return boolean True if the expected value is lesser than or equals to
     *                 the current value. False otherwise.
     */
    protected function isLessEquals($current, $expected)
    {
        return trim($expected, '"') <= $current;
    }

    /**
     * Checks if the current value contains the expected value.
     *
     * @param mixed $current  The current value.
     * @param mixed $expected The expected value.
     *
     * @return boolean True if the current value contains the expected value
     *                 False otherwise.
     */
    protected function isLike($current, $expected)
    {
        return strpos($current, $expected) !== false;
    }

    /**
     * Checks if the expected value is not equals to the current value.
     *
     * @param mixed $current  The current value.
     * @param mixed $expected The expected value.
     *
     * @return boolean True if the expected value is not equals to the current
     *                 value. False otherwise.
     */
    protected function isNotEquals($current, $expected)
    {
        return !$this->isEquals($current, $expected);
    }

    /**
     * Checks if the expected value is not in the current array of values.
     *
     * @param mixed $current  The current value.
     * @param mixed $expected The expected value.
     *
     * @return boolean True if the expected value is not in the current array of
     *                 False otherwise.
     */
    protected function isNotInArray($current, $expected)
    {
        return !$this->isInArray($current, $expected);
    }

    /**
     * Checks if the current value does not contain the expected value.
     *
     * @param mixed $current  The current value.
     * @param mixed $expected The expected value.
     *
     * @return boolean True if the current value does not contain the expected
     *                 value. False otherwise.
     */
    protected function isNotLike($current, $expected)
    {
        return !$this->isLike($current, $expected);
    }

    /**
     * Loads all items in the repository.
     */
    protected function load()
    {
        $cacheId        = \underscore($this->metadata->name) . '.' . DEPLOYED_AT;
        $this->entities = $this->cache->get($cacheId);

        if (!empty($this->entities)) {
            return;
        }

        $finder = new Finder();

        foreach ($this->paths as $path) {
            $path = $this->container->getParameter('kernel.root_dir')
                . DS . '..' . DS . $path . DS;

            $finder->files()->in($path)->name('*.yml');

            foreach ($finder as $file) {
                $this->loadEntity($file->getRealPath());
            }
        }

        $this->cache->set($cacheId, $this->entities);
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
        $config['extension']['path'] = substr($path, strpos($path, '/public') + 7);

        if (empty($config)) {
            return;
        }

        $name  = array_keys($config)[0];
        $class = 'Common\\ORM\\Entity\\' . \classify($name);

        try {
            if (class_exists($class)) {
                $entity = new $class($this->converter->objectify($config[$name]));

                $this->entities[] = $entity;
            }
        } catch (\Exception $e) {
        }
    }

    /**
     * Checks if the current value matches the expected value.
     *
     * @param mixed $current  The current value.
     * @param mixed $expected The expected value.
     *
     * @return boolean True if the current value matches the expected value.
     *                 False otherwise.
     */
    protected function match($current, $expected)
    {
        return preg_match_all('/' . $expected . '/', $current) > 0;
    }

    /**
     * Checks if the current value does not match the expected value.
     *
     * @param mixed $current  The current value.
     * @param mixed $expected The expected value.
     *
     * @return boolean True if the current value does not match the expected
     *                 value. False otherwise.
     */
    protected function notMatch($current, $expected)
    {
        return !$this->match($current, $expected);
    }

    /**
     * Sorts the list of entities basing on the sorting criteria.
     *
     * @param array $entities The list of entities.
     * @param array $order    The sorting criteria.
     */
    protected function sort(&$entities, $order)
    {
        $property  = array_shift($order);
        $direction = array_shift($order);

        usort($entities, function ($a, $b) use ($property, $direction) {
            if ($direction === 'desc') {
                return $a->{$property} < $b->{$property};
            }

            return $a->{$property} >= $b->{$property};
        });
    }
}
