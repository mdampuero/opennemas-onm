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
use Onm\Cache\CacheInterface;
use Onm\Instance\Instance;
use Onm\Exception\AssetsNotRestoredException;
use Onm\Exception\BackupException;
use Onm\Exception\DatabaseNotCreatedException;
use Onm\Exception\DatabaseNotDeletedException;
use Onm\Exception\DatabaseNotRestoredException;
use Onm\Exception\InstanceNotConfiguredException;
use Onm\Exception\InstanceNotDeletedException;
use Onm\Exception\InstanceNotFoundException;
use Onm\Exception\InstanceNotRestoredException;
use Onm\Module\ModuleManager;
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
                $themeInfo =  include_once($value.'/init.php');

                $templates['es.openhost.theme.' . $name] = $themeInfo;
            }
        }

        unset($templates['admin']);
        unset($templates['manager']);

        return $templates;
    }

    /**
     * Initializes the InstanceManager.
     *
     * @param Connection     $dbConn The custom DBAL wrapper.
     * @param CacheInterface $cache  The cache instance.
     */
    public function __construct($conn, CacheInterface $cache, SettingManager $sm)
    {
        $this->conn  = $conn;
        $this->cache = $cache;
        $this->sm    = $sm;
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
     * Finds the list of instances created in the current month.
     *
     * @return array Array of instances.
     */
    public function findLastCreatedInstances()
    {
        // Executing the SQL
        $sql = "SELECT id FROM `instances` "
            ."WHERE created > DATE_SUB(NOW(), INTERVAL 1 MONTH)";

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
     * @return array Array of instances.
     */
    public function findMulti($data)
    {
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
        // Unused var $prefix
        unset($prefix);

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

        return $ordered;
    }

    /**
     * Finds the list of instances not used in the current month.
     *
     * @return array Array of instances.
     */
    public function findNotUsedInstances()
    {
        // Executing the SQL
        $sql = "SELECT id FROM `instances` "
            ."WHERE last_login IS NULL OR last_login < DATE_SUB(NOW(), INTERVAL 1 MONTH)";

        $this->conn->selectDatabase('onm-instances');
        $rs = $this->conn->fetchAll($sql);

        $ids = array();
        foreach ($rs as $item) {
            $ids[] = $item['id'];
        }

        return $this->findMulti($ids);
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
        if (empty($instance->getDatabaseName())) {
            return false;
        }

        $cacheId = 'instance' . $this->cacheSeparator . $instance->id
            . $this->cacheSeparator . 'information';

        $instance->external = $this->cache->fetch($cacheId);

        if (!$instance->external) {
            $this->conn->selectDatabase($instance->getDatabaseName());
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
        $instance->internal_name = 'manager';
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

        // Delete metas
        $delete = array_diff(
            !empty($instance->_metas) ? array_keys($instance->_metas) : [],
            !empty($instance->metas) ? array_keys($instance->metas) : []
        );

        if (!empty($delete)) {
            foreach ($delete as &$value) {
                $value = '\'' . $value . '\'';
            }

            $sql = 'DELETE FROM instance_meta WHERE instance_id = '
                . $instance->id
                . ' AND meta_key IN (' . implode(',', $delete) . ')';
            $this->conn->executeQuery($sql);
        }

        // Update instance metas
        if (!empty($instance->metas)) {
            $values = [];
            foreach ($instance->metas as $key => $value) {
                if (is_array($value) || is_object($value)) {
                    $value = str_replace('\\', '\\\\', serialize($value));
                }

                $values[] = '(\'' . $instance->id . '\',\'' . $key . '\',\''
                    . $value . '\')';
            }

            $sql = 'REPLACE INTO instance_meta VALUES ' . implode(',', $values);
            $this->conn->executeUpdate($sql);
        }

        // Delete cache for domains
        foreach ($instance->domains as $domain) {
            $this->cache->delete($domain);
        }

        // Delete instance from cache
        $this->cache->delete('instance' . $this->cacheSeparator . $instance->id);
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
                    $instance->{$key} = @unserialize($value);

                    if (!$instance->{$key}) {
                        $instance->{$key} = array();
                    }
                }
            } else {
                $instance->{$key} = $value;
            }
        }

        $sql = 'SELECT * FROM instance_meta WHERE instance_id = ' . $instance->id;
        $this->conn->selectDatabase('onm-instances');
        $rs = $this->conn->fetchAll($sql);

        $instance->metas = [];
        foreach ($rs as $r) {
            $instance->metas[$r['meta_key']] = $r['meta_value'];

            $data = @unserialize($r['meta_value']);

            if ($data !== false) {
                $instance->metas[$r['meta_key']] = $data;
            }
        }

        $instance->_metas = $instance->metas;

        // Check for changes in modules
        if (is_null($instance->changes_in_modules)) {
            $instance->changes_in_modules = [];
        }

        $this->checkPacksActivated($instance);
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
            $this->cache->delete($domain);
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
            && isset($data['email'])
            && isset($data['password'])
            && isset($data['token'])
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
     * @param Instance $instance The instance to configure.
     */
    public function configureInstance(&$instance)
    {
        $namespace = $this->cache->getNamespace();

        $this->cache->setNamespace($instance->internal_name);
        $this->sm->setConfig([
            'database'     => $instance->getDatabaseName(),
            'cache_prefix' => $instance->internal_name
        ]);

        // Build external parameters
        $instance->external['site_name']    = $instance->name;
        $instance->external['site_created'] = $instance->created;

        $title = $this->sm->get('site_title');
        if (strpos($title, $instance->name) === false) {
            $instance->external['site_title'] = $instance->name . ' - ' . $title;
        }

        $description = $this->sm->get('site_description');
        if (strpos($description, $instance->name) === false) {
            $instance->external['site_description'] = $instance->name . ' - ' . $description;
        }

        $keywords = $this->sm->get('site_keywords');
        if (strpos($keywords, $instance->name) === false) {
            $instance->external['site_keywords'] = $instance->name . ' - ' . $keywords;
        }

        $instance->external['site_agency'] = $instance->internal_name . '.opennemas.com';

        foreach (array_keys($instance->external) as $key) {
            $this->cache->delete($key);
            $this->sm->invalidate($key);

            if (!$this->sm->set($key, $instance->external[$key])) {
                throw new InstanceNotConfiguredException(
                    'The instance could not be configured'
                );
            }
        }

        $this->cache->setNamespace($namespace);
    }

    /**
     * Update settings in instance database.
     *
     * @param Instance The instance to configure.
     */
    public function updateSettings($instance)
    {
        $namespace = $this->cache->getNamespace();

        $this->cache->setNamespace($instance->internal_name);
        $this->sm->setConfig(array('database' => $instance->getDatabaseName()));

        $settings = ['pass_level', 'piwik', 'max_mailing', 'max_users', 'last_invoice'];

        foreach ($settings as $key) {
            $this->cache->delete($key);
            $this->sm->invalidate($key);

            $value = '';
            if (array_key_exists($key, $instance->external)) {
                $value = $instance->external[$key];
            }

            if (!$this->sm->set($key, $value)) {
                throw new InstanceNotConfiguredException(
                    'The instance could not be configured'
                );
            }
        }

        $this->cache->setNamespace($namespace);
    }

    /**
     * Adds the activated packs basing on the activated modules.
     *
     * @param Instance $instance The instance.
     */
    private function checkPacksActivated(&$instance)
    {
        $packs = [ 'BASIC', 'PROFESSIONAL', 'ADVANCED', 'EXPERT' ];

        $instance->activated_modules =
            array_diff($instance->activated_modules, $packs);

        foreach ($packs as $pack) {
            $modules = ModuleManager::getModuleIdsByPack($pack);

            if (empty(array_diff($modules, $instance->activated_modules))
                && !in_array($pack, $instance->activated_modules)
            ) {
                $instance->activated_modules[] = $pack;
            }
        }

        $instance->activated_modules =
            array_values($instance->activated_modules);
    }
}
