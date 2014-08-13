<?php

/**
 * This file is part of the Onm package.
 *
 * (c)  OpenHost S.L. <developers@openhost.es>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Onm\Instance;

use Repository\BaseManager;
use Onm\Database\DbalWrapper;
use Onm\Cache\CacheInterface;
use Onm\Instance\Instance;
use Onm\Exception\AssetsNotCopiedException;
use Onm\Exception\AssetsNotDeletedException;
use Onm\Exception\AssetsNotRestoredException;
use Onm\Exception\BackupException;
use Onm\Exception\DatabaseNotCreatedException;
use Onm\Exception\DatabaseNotDeletedException;
use Onm\Exception\DatabaseNotRestoredException;
use Onm\Exception\InstanceNotConfiguredException;
use Onm\Exception\InstanceNotDeletedException;
use Onm\Exception\InstanceNotFoundException;
use Onm\Exception\InstanceNotRestoredException;
use Repository\SettingManager;

/**
 * Class for manage ONM instances.
 *
 * @package    Onm
 * @subpackage Instance
 */
class InstanceManager extends BaseManager
{
    /**
     * The current instance.
     *
     * @var Instance
     */
    public $current_instance;

    /*
     * Get available templates.
     *
     * @return array An array of public available templates for the instance.
     */
    public static function getAvailableTemplates()
    {
        foreach (glob(SITE_PATH . DS . 'themes' . DS . '*') as $value) {
            $parts = preg_split("@/@", $value);
            $name  = $parts[count($parts) - 1];

            if (file_exists($value.'/init.php')) {
                $themeInfo        =  include_once($value.'/init.php');
                $templates[$name] = $themeInfo;
            }
        }

        unset($templates['admin']);
        unset($templates['manager']);

        return $templates;
    }

    /**
     * Initializes the InstanceManager.
     *
     * @param DbalWrapper    $dbConn The custom DBAL wrapper.
     * @param CacheInterface $cache  The cache instance.
     */
    public function __construct(DbalWrapper $conn, CacheInterface $cache, SettingManager $sm)
    {
        $this->conn  = $conn;
        $this->cache = $cache;
        $this->sm    = $sm;
    }

    /**
     * Checks for repeated internal name and returns it, corrected if necessary.
     *
     * @param string $instance The instance to check.
     */
    public function checkInternalName(&$instance)
    {
        $this->conn->selectDatabase('onm-instances');

        $internalName = $instance->internal_name;
        if (empty($internalName)) {
            $domain = explode('.', $instance->domains[0]);
            $internalName = $domain[0];
        }

        $internalName = strtolower($internalName);

        // Check if the generated InternalShortName already exists
        $sql = "SELECT count(*) as internal_exists FROM instances "
             . "WHERE `internal_name` REGEXP '"
             . $internalName . "[0-9]*'";
        $rs = $this->conn->fetchAssoc($sql);

        if ($rs && $rs['internal_exists'] > 0) {
            $internalName .= $rs['internal_exists'];
        }

        $instance->internal_name = $internalName;
    }

    /**
     * Checks if a contact email is already in use.
     *
     * @param string $mail The email to check.
     *
     * @return boolean True if the email is already in use. Otherwise, returns
     *                 false.
     */
    public function emailExists($email)
    {
        $instances = $this->countBy(
            array('contact_mail' => array(array('value' => $email)))
        );

        if ($instances > 0) {
            return true;
        }

        return false;
    }

    /**
     * Counts the instances for content given a criteria
     *
     * @param array $criteria The criteria used to search.
     *
     * @return integer The number of found instances.
     */
    public function countBy($criteria)
    {
        $instances = array();

        // Building the SQL filter
        $filterSQL  = $this->getFilterSQL($criteria);

        // Executing the SQL
        $sql = "SELECT COUNT(id) FROM `instances` "
            ."WHERE $filterSQL";

        $this->conn->selectDatabase('onm-instances');
        $rs = $this->conn->fetchArray($sql);

        if (!$rs) {
            return false;
        }

        return $rs[0];
    }

    /**
     * Finds one instance from the given a instance id.
     *
     * @param integer $id Instance id.
     *
     * @return Instance The matched instance.
     */
    public function find($id)
    {
        $previousNamespace = $this->cache->getNamespace();
        $this->cache->setNamespace('instance');

        $cacheId = "instance" . $this->cacheSeparator . $id;
        $entity  = null;

        if (!$this->hasCache()
            || ($entity = $this->cache->fetch($cacheId)) === false
            || !is_object($entity)
        ) {
            $entity = new Instance();
            $entity->id = $id;

            $this->refresh($entity);

            if ($this->hasCache()) {
                $this->cache->save($cacheId, $entity);
            }
        }

        $this->cache->setNamespace($previousNamespace);

        return $entity;
    }

    /**
     * Searches for content given a criteria
     *
     * @param array   $criteria        The criteria used to search.
     * @param array   $order           The order applied in the search.
     * @param integer $elementsPerPage The max number of elements.
     * @param integer $page            The current page.
     * @param integer $offset          The offset to start with.
     *
     * @return array The matched elements.
     */
    public function findBy($criteria, $order = null, $elementsPerPage = null, $page = null, $offset = 0)
    {
        $instances = array();

        // Building the SQL filter
        $filterSQL  = $this->getFilterSQL($criteria);

        $orderBySQL  = '`internal_name` ASC';
        if (!empty($order)) {
            $orderBySQL = $this->getOrderBySQL($order);
        }
        $limitSQL = $this->getLimitSQL($elementsPerPage, $page, $offset);

        // Executing the SQL
        $sql = "SELECT id FROM `instances` "
            ."WHERE $filterSQL ORDER BY $orderBySQL $limitSQL";

        $this->conn->selectDatabase('onm-instances');
        $rs = $this->conn->fetchAll($sql);

        $ids = array();
        foreach ($rs as $item) {
            $ids[] = $item['id'];
        }

        return $this->findMulti($ids);
    }

    /**
     * Find multiple contents from a given array of instance ids.
     *
     * @param array $data Array of instance ids.
     *
     * @return array Array of contents.
     */
    public function findMulti($data)
    {
        $previousNamespace = $this->cache->getNamespace();
        $this->cache->setNamespace('instance');

        $ids  = array();
        $keys = array();
        foreach ($data as $value) {
            $ids[]  = 'instance' . $this->cacheSeparator . $value;
            $keys[] = $value;
        }

        $instances = array_values($this->cache->fetch($ids));

        $cachedIds = array();
        foreach ($instances as $instance) {
            $cachedIds[] = 'instance' . $this->cacheSeparator . $instance->id;
        }

        $missedIds = array_diff($ids, $cachedIds);

        foreach ($missedIds as $instance) {
            list($prefix, $instanceId) = explode($this->cacheSeparator, $instance);

            $instance = $this->find($instanceId);
            if ($instance) {
                $instances[] = $instance;
            }
        }

        $ordered = array();
        foreach ($keys as $id) {
            $i = 0;
            while ($i < count($instances) && $instances[$i]->id != $id) {
                $i++;
            }

            if ($i < count($instances)) {
                $ordered[] = $instances[$i];
            }
        }

        $this->cache->setNamespace($previousNamespace);
        return $ordered;
    }

    /*
     * Gets one Database connection
     *
     * @return DbalWrapper The database connection.
     */
    public function getConnection()
    {
        return $this->conn;
    }

    /**
     * Counts total contents in for an instance.
     *
     * @param array $settings The instance settings.
     */
    public function getExternalInformation(Instance &$instance)
    {
        $database = $instance->getDatabaseName();

        $cacheId = 'instance' . $this->cacheSeparator . $instance->id
            . $this->cacheSeparator . 'information';

        $instance->external = $this->cache->fetch($cacheId);

        if (!$instance->external) {
            $this->conn->selectDatabase($database);
            $sql = "SELECT * FROM `settings`";

            $rs = $this->conn->executeQuery($sql);

            $settings = array();
            if ($rs !== false) {
                foreach ($rs as $value) {
                    $settings[$value['name'] ] = unserialize($value['value']);
                }
            }

            $instance->external = $settings;
        }
    }

    /**
     * Check if an instance already exists.
     *
     * @param string $name The instance internal name.
     *
     * @return boolean True, if instance exists. Otherwise, returns false.
     */
    public function instanceExists($name)
    {
        $instances = $this->countBy(
            array('internal_name' => array(array('value' => $name)))
        );

        if ($instances > 0) {
            return true;
        }

        return false;
    }

    /**
     * Returns the instance object for manager.
     *
     * @return Instance The instance.
     */
    public function loadManager()
    {
        $instance = new Instance();
        $instance->internal_name = 'onm_manager';
        $instance->activated = true;

        $instance->settings = array(
            'TEMPLATE_USER' => '',
            'BD_DATABASE'   => 'onm-instances',
        );

        $this->instance = $instance;

        return $instance;
    }

    /**
     * Updates the instance data.
     *
     * @param array $data The instance data.
     *
     * @return boolean True, if instance was restored successfully. Otherwise,
     *                 returns false.
     */
    public function update($data)
    {
        $instance = $this->findBy(
            array('internal_name' => array(array('value' => $data['internal_name']))),
            array('id' => 'asc')
        );

        if (is_array($data['domains'])) {
            $data['domains'] = implode(',', $data['domains']);
        }

        $sql = "UPDATE instances SET name=?, internal_name=?, "
             . "domains=?, main_domain=?, activated=?, contact_mail=?, settings=? WHERE id=?";
        $values = array(
            $data['name'],
            $data['internal_name'],
            $data['domains'],
            array_key_exists('main_domain', $data) ? $data['main_domain'] : 0,
            $data['activated'],
            $data['user_mail'],
            serialize($data['settings']),
            $data['id']
        );

        $rs = $this->conn->executeQuery($sql, $values);
        if (!$rs) {
            return false;
        }

        $this->deleteCacheForInstancedomains($data['domains']);

        return true;
    }

    /**
     * Saves an instance to database.
     *
     * @param Instance $instance The instance to save.
     */
    public function persist(Instance &$instance)
    {
        $previousNamespace = $this->cache->getNamespace();
        $this->cache->setNamespace('instance');

        $ref = new \ReflectionClass($instance);
        $properties = array();
        foreach ($ref->getProperties() as $property) {
            $properties[] = $property->name;
        }

        $values = array();
        foreach ($properties as $key) {
            $value = $instance->{$key};
            if (is_array($value)) {
                if ($key == 'domains') {
                    $values[$key] = "'" . implode(',', $value) . "'";
                } else {
                    $values[$key] = "'" . serialize($value) . "'";
                }
            } elseif (!is_null($value)) {
                $values[$key] = "'$value'";
            } else {
                $values[$key] = "NULL";
            }
        }

        unset($values['id']);
        if (is_null($instance->id)) {
            $sql = 'INSERT INTO instances('
                . implode(', ', array_keys($values)) . ') VALUES ('
                . implode(', ', array_values($values)) .')';
        } else {

            $sql = 'UPDATE instances SET ';

            foreach ($values as $key => $value) {
                $sql .= $key . ' = ' . $value . ',';
            }

            $sql = rtrim($sql, ',') . ' WHERE id = ' . $instance->id;
        }

        $this->conn->selectDatabase('onm-instances');
        $this->conn->executeQuery($sql);

        // Update instance id and database after INSERT
        if (is_null($instance->id)) {
            $instance->id = $this->conn->lastInsertId();
            $instance->settings['BD_DATABASE'] = $instance->id;

            $sql = 'UPDATE instances SET settings = \''
                . serialize($instance->settings) . '\''
                . ' WHERE id = ' . $instance->id;
            $this->conn->executeQuery($sql);
        }

        // Delete cache for domains
        foreach ($instance->domains as $domain) {
            $this->cache->delete($domain, 'instance');
        }

        $this->cache->delete('activated_modules', $instance->internal_name);

        // Delete instance from cache
        $this->cache->delete('instance' . $this->cacheSeparator . $instance->id);

        $this->cache->setNamespace($previousNamespace);
    }

    /**
     * Reloads the instance properties from database.
     *
     * @param Instance $instance The instance object.
     */
    public function refresh(Instance &$instance)
    {
        if (!$instance->id) {
            throw new InstanceNotFoundException(
                "Could not find instance with id = 'null'"
            );
        }

        $sql = 'SELECT * FROM instances WHERE id = ' . $instance->id;

        $this->conn->selectDatabase('onm-instances');
        $rs = $this->conn->fetchAssoc($sql);

        if (!$rs) {
            throw new InstanceNotFoundException(
                "Could not find instance with id = '$instance->id'"
            );
        }

        $instance = new Instance();
        foreach ($rs as $key => $value) {
            if (is_array($instance->{$key})) {
                if ($key == 'domains') {
                    $instance->{$key} = explode(',', $value);

                    foreach ($instance->domains as $k => $v) {
                        $instance->domains[$k] = trim($v);
                    }
                } else {
                    $instance->{$key} = unserialize($value);
                }
            } else {
                $instance->{$key} = $value;
            }
        }
    }

    /**
     * Deletes the instance from database.
     *
     * @param Instance $instance The instance to remove.
     */
    public function remove($instance)
    {
        $this->conn->selectDatabase('onm-instances');

        $sql = "DELETE FROM instances WHERE id=?";
        $rs = $this->conn->executeQuery($sql, array($instance->id));

        if (!$rs) {
            throw new InstanceNotDeletedException(
                "Could not delete instance reference."
            );
        }

        // Delete cache for domains
        foreach ($instance->domains as $domain) {
            $this->cache->delete($domain, 'instance');
        }

        // Delete instance from cache
        $this->cache->delete('instance' . $this->cacheSeparator . $instance->id);
    }

    /**
     * If data, create an user in new database.
     *
     * @param  array $database The database name.
     * @param  array $data     The user data.
     */
    public function createUser($database, $data)
    {
        $this->conn->selectDatabase($database);

        if (isset($data['username'])
            && isset ($data['email'])
            && isset ($data['password'])
            && isset ($data['token'])
        ) {
            // Insert user into instance database
            $sql = "INSERT INTO users (`username`, `token`, `sessionexpire`,
                `email`, `password`, `name`, `fk_user_group`)
                VALUES (?,?,?,?,?,?,?)";

            $values = array(
                $data['username'], $data['token'], 60, $data['email'],
                md5($data['password']), $data['username'], 5
            );

            if (!$this->conn->executeQuery($sql, $values)) {
                throw new UserNotCreatedException(
                    'Could not create the user'
                );
            }

            // Add category privileges to the new user
            $userId = $this->conn->lastInsertId();
            $sql = "INSERT INTO `users_content_categories` "
                ."(`pk_fk_user`, `pk_fk_content_category`) "
                ."VALUES ($userId, 0), ($userId, 22), ($userId, 23),"
                . "($userId, 24), ($userId, 25), ($userId, 26), ($userId, 27),"
                . "($userId, 28), ($userId, 29), ($userId, 30), ($userId, 31)";

            if (!$this->conn->executeQuery($sql)) {
                throw new UserNotCreatedException(
                    'Could not create the user'
                );
            }
        }
    }

    /**
     * Configures the instance with the given data.
     *
     * @param array  $data     The instance settings.
     * @param string $database The database name.
     */
    public function configureInstance($data, $database)
    {
        $namespace = $this->cache->getNamespace();

        $this->cache->setNamespace($data['internal_name']);
        $this->sm->setConfig(array('database' => $database));

        if (!$this->sm->set('contact_IP', $data['contact_IP'])) {
            throw new InstanceNotConfiguredException(
                'The instance could not be configured'
            );
        }

        if (!$this->sm->set('contact_mail', $data['contact_mail'])) {
            throw new InstanceNotConfiguredException(
                'The instance could not be configured'
            );
        }

        if (!$this->sm->set('site_name', $data['name'])) {
            throw new InstanceNotConfiguredException(
                'The instance could not be configured'
            );
        }

        if (!$this->sm->set('site_created', $data['site_created'])) {
            throw new InstanceNotConfiguredException(
                'The instance could not be configured'
            );
        }

        $this->sm->invalidate('site_title');
        $title = $data['name'] . ' - ' . $this->sm->get('site_title');
        if (!$this->sm->set('site_title', $title)) {
            throw new InstanceNotConfiguredException(
                'The instance could not be configured'
            );
        }

        $this->sm->invalidate('site_description');
        $description = $data['name'].' - '.$this->sm->get('site_description');
        if (!$this->sm->set('site_description', $description)) {
            throw new InstanceNotConfiguredException(
                'The instance could not be configured'
            );
        }

        $this->sm->invalidate('site_keywords');
        $keywords = $data['name'].' - '.$this->sm->get('site_keywords');
        if (!$this->sm->set('site_keywords', $keywords)) {
            throw new InstanceNotConfiguredException(
                'The instance could not be configured'
            );
        }

        if (!$this->sm->set('site_agency', $data['internal_name'].'.opennemas.com')) {
            throw new InstanceNotConfiguredException(
                'The instance could not be configured'
            );
        }

        if (isset ($data['time_zone'])) {
            if (!$this->sm->set('time_zone', $data['time_zone'])) {
                throw new InstanceNotConfiguredException(
                    'The instance could not be configured'
                );
            }
        }

        $this->cache->setNamespace($namespace);
    }
}
