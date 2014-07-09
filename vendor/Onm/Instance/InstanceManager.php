<?php
/*
 * This file is part of the onm package.
 * (c) 2009-2011 OpenHost S.L. <contact@openhost.es>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace Onm\Instance;

use FilesManager as fm;
use Onm\Settings as s;
use Onm\Instance\Instance;

use Repository\BaseManager;
use Onm\Database\DbalWrapper;
use Onm\Cache\CacheInterface;

/**
 * Class for manage ONM instances.
 *
 * @package    Onm
 * @subpackage Instance
 */
class InstanceManager extends BaseManager
{
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
     * Initializes the InstanceManager
     *
     * @param DbalWrapper    $dbConn The custom DBAL wrapper.
     * @param CacheInterface $cache  The cache instance.
     */
    public function __construct(DbalWrapper $conn, CacheInterface $cache)
    {
        $this->conn  = $conn;
        $this->cache = $cache;
    }

    /**
     * Backup assets data of a particular instance.
     *
     * @param  string $mediaPath  Assets directory
     * @param  string $backupPath Backups directory
     * @return boolean            True if the backup was successful. Otherwise,
     *                            returns false.
     *
     * @throws DeleteRegisteredInstanceException In case of error.
     */
    public function backupAssets($mediaPath, $backupPath)
    {
        if (!fm::createDirectory($backupPath)) {
            return false;
        }

        $tgzFile = $backupPath . DS . "media.tar.gz";
        if (!fm::compressTgz($mediaPath, $tgzFile)) {
            return false;
        }

        return true;
    }

    /**
     * Backup database of a particular instance.
     *
     * @param  string  $database Database name.
     * @param  string  $path     Path where place the backup.
     * @return boolean           True if the backup was successful. Otherwise,
     *                           returns false.
     *
     * @throws DeleteRegisteredInstanceException In case of error.
     */
    public function backupDatabase($database, $path)
    {
        if (!fm::createDirectory($path)) {
            return false;
        }

        $dump = "mysqldump -u" . $this->conn->connectionParams['user']
            . " -p" . $this->conn->connectionParams['password'] . " --databases "
            . "'" . $database . "'" . " > " . $path . DS . "database.sql";

        exec($dump, $output, $result);

        if ($result != 0) {
            return false;
        }

        return true;
    }

    /**
     * Backup data of a particular instance from the instances table.
     *
     * @param  integer $id   The id of the instance.
     * @param  string  $path Backups directory
     * @return boolean       True if the backup was successful. Otherwise,
     *                       returns false.
     *
     * @throws DeleteRegisteredInstanceException In case of error.
     */
    public function backupInstanceReference($id, $path)
    {
        if (!fm::createDirectory($path)) {
            return false;
        }

        $dump = "mysqldump -u" . $this->conn->connectionParams['user']
            . " -p" . $this->conn->connectionParams['password']
            . " --no-create-info --where 'id=" . $id . "' "
            . $this->conn->connectionParams['dbname']
            . " instances > " . $path . DS . "instanceReference.sql";

        exec($dump, $output, $return_var);

        if ($return_var != 0) {
            fm::deleteDirectoryRecursively($path);
            return false;
        }

        return true;
    }

    /**
     * Check for repeated internal name and returns it, corrected if necessary.
     *
     * @param  string $name The internal name to check.
     * @return string       The checked and corrected internal name.
     */
    public function checkInternalName($name)
    {
        $this->conn->selectDatabase('onm-instances');

        // Check if the generated InternalShortName already exists
        $sql = "SELECT count(*) as internal_exists FROM instances "
             . "WHERE `settings` REGEXP '" . $name . "[0-9]*'";
        $rs = $this->conn->fetchAssoc($sql);

        if ($rs && $rs['internal_exists'] > 0) {
            $name = $name . $rs['internal_exists'];
        }

        return $name;
    }

    /**
     * Copies the default assets for the new instance given its internal name.
     *
     * @param  string $name The name of the instance.
     * @return mixed        True if the assets where copied successfully.
     *
     * @throws DefaultAssetsForInstanceNotCopiedException If copy fails.
     */
    public function copyDefaultAssets($name)
    {
        $mediaPath   = SITE_PATH . DS . 'media' . DS . $name;
        $defaultPath = SITE_PATH . DS . 'media' . DS . 'default';

        if (!file_exists($mediaPath)) {
            if (!fm::recursiveCopy($defaultPath, $mediaPath)) {
                throw new DefaultAssetsForInstanceNotCopiedException(
                    "Could not copy default assets for the instance"
                );
            } else {
                return true;
            }
        } else {
            //TODO: return codes for handling this errors
            return "The media folder {$name} already exists.";
        }
    }

    /**
     * Creates one instance.
     *
     * @param  array $data The instance data.
     * @return mixed       True if the instance was created successfully.
     *                     Otherwise, return an array of errors.
     */
    public function create($data)
    {
        $errors = array();

        try {
            $data = $this->createInstanceReference($data);

            $this->createDatabase($data);

            $this->copyDefaultAssets($data['internal_name']);

        } catch (InstanceNotRegisteredException $e) {
            $errors []= $e->getMessage();
        } catch (DatabaseForInstanceNotCreatedException $e) {
            $errors []= $e->getMessage();
            $this->deleteDatabaseForInstance($data['settings']['BD_DATABASE']);
            $this->deleteInstanceUserFromDatabaseManager($data['settings']['BD_USER']);
            $this->deleteInstanceReferenceInManager($data['id']);
        } catch (DefaultAssetsForInstanceNotCopiedException $e) {
            $errors []= $e->getMessage();

            $this->deleteDefaultAssets($data['internal_name']);
            $this->deleteDatabaseForInstance($data['settings']['BD_DATABASE']);
        }

        if (count($errors) > 0) {
            return $errors;
        }

        return true;
    }

    /**
     * Creates and imports default database for the new instance.
     *
     * @param  array   $data Information to create the instance.
     * @return boolean       True if the database is created successfully.
     *
     * @throws DatabaseForInstanceNotCreatedException If creation fails.
     */
    public function createDatabase($data)
    {
        // Create instance database
        $sql = "CREATE DATABASE `{$data['settings']['BD_DATABASE']}`";
        $rs = $this->conn->executeQuery($sql);

        if (!$rs) {
            throw new DatabaseForInstanceNotCreatedException(
                'Could not create the default database for the instance'
            );
        }

        // Import default instance database
        $target = $data['settings']['BD_DATABASE'];
        $source = realpath(
            APPLICATION_PATH . DS . 'db' . DS . 'instance-default.sql'
        );

        if (!$this->restoreDatabase($source, $target)) {
            throw new DatabaseForInstanceNotCreatedException(
                'Could not create the default database for the instance'
            );
        }

        $this->conn->selectDatabase($target);

        if (isset($data['user_name'])
            && isset ($data['token'])
            && isset ($data['user_mail'])
            && isset ($data['user_password'])
        ) {
            // Insert user into instance database
            $sql = "INSERT INTO users (`username`, `token`, `sessionexpire`,
                `email`, `password`, `name`, `fk_user_group`)
                VALUES (?,?,?,?,?,?,?)";

            $values = array(
                $data['user_name'], $data['token'], 60, $data['user_mail'],
                md5($data['user_password']), $data['user_name'], 5
            );

            if (!$this->conn->executeQuery($sql, $values)) {
                throw new DatabaseForInstanceNotCreatedException(
                    'Could not create the default database for the instance -creating user'
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
                throw new DatabaseForInstanceNotCreatedException(
                    'Could not create the default database for the instance - privileges'
                );
            }

            // Update instance settings
            if (!s::set('contact_mail', $data['user_mail'])) {
                throw new DatabaseForInstanceNotCreatedException(
                    'Could not create the default database for the instance - user_mail'
                );
            }

            if (!s::set('contact_name', $data['user_name'])) {
                throw new DatabaseForInstanceNotCreatedException(
                    'Could not create the default database for the instance - user_name'
                );
            }

            if (!s::set('contact_IP', $data['contact_IP'])) {
                throw new DatabaseForInstanceNotCreatedException(
                    'Could not create the default database for the instance - content_IP'
                );
            }
        }

        //Change and insert some data with instance information
        if (!s::set('site_name', $data['name'])) {
            throw new DatabaseForInstanceNotCreatedException(
                'Could not create the default database for the instance - site_name'
            );
        }

        if (!s::set('site_created', $data['site_created'])) {
            throw new DatabaseForInstanceNotCreatedException(
                'Could not create the default database for the instance site_created'
            );
        }

        s::invalidate('site_title');
        $title = $data['name'] . ' - ' . s::get('site_title');
        if (!s::set('site_title', $title)) {
            throw new DatabaseForInstanceNotCreatedException(
                'Could not create the default database for the instance - site_title'
            );
        }

        s::invalidate('site_description');
        $description = $data['name'].' - '.s::get('site_description');
        if (!s::set('site_description', $description)) {
            throw new DatabaseForInstanceNotCreatedException(
                'Could not create the default database for the instance - site_description'
            );
        }

        s::invalidate('site_keywords');
        $keywords = $data['name'].' - '.s::get('site_keywords');
        if (!s::set('site_keywords', $keywords)) {
            throw new DatabaseForInstanceNotCreatedException(
                'Could not create the default database for the instance site_keywords'
            );
        }

        if (!s::set('site_agency', $data['internal_name'].'.opennemas.com')) {
            throw new DatabaseForInstanceNotCreatedException(
                'Could not create the default database for the instance - site_keywords'
            );
        }

        if (isset ($data['timezone'])) {
            if (!s::set('time_zone', $data['timezone'])) {
                throw new DatabaseForInstanceNotCreatedException(
                    'Could not create the default database for the instance - timezone'
                );
            }
        }

        return true;
    }

    /**
     * Inserts the instance reference in the instances table.
     *
     * @param  array $data The instance data.
     * @return mixed       True, if the instance is created successfully. If it
     *                     wasn't and it comes from Opennemas.com, return an
     *                     error message.
     *
     * @throws InstanceNotRegisteredException If some data is blank or anything
     *                                       fails.
     */
    public function createInstanceReference($data)
    {
        if (empty($data['name'])
            || empty($data['internal_name'])
            || empty($data['domains'])
            || !isset($data['activated'])
            || empty($data['user_mail'])
        ) {
            throw new InstanceNotRegisteredException(
                _("Instance data could not be blank.")
            );
        }

        // Check if the instance already exists
        $instanceExists = $this->instanceExists($data['internal_name']);

        // Check if the email already exists
        $emailExists = $this->emailExists($data['user_mail']);

        // If doesn´t exist the instance in the database and doesn't exist contact mail proceed
        if (!$instanceExists && !$emailExists) {
            $sql = "INSERT INTO instances (name, internal_name, domains,"
                . "activated, settings, contact_mail) VALUES (?,?,?,?,?,?)";

            $values = array(
                $data['name'], $data['internal_name'],
                $data['domains'], $data['activated'],
                serialize($data['settings']), $data['user_mail']
            );

            if (!$this->conn->executeQuery($sql, $values)) {
                throw new InstanceNotRegisteredException(
                    "Could not create the instance reference into the instance "
                );
            }

            $data['id'] = $this->conn->lastInsertId();
            if (!$this->update($data)) {
                $sql = "DELETE FROM instances WHERE id=?";
                $rs = $this->conn->executeQuery($sql, array($data['id']));
                if (!$rs) {
                    return false;
                }
                throw new InstanceNotRegisteredException(
                    "Could not create the instance reference into the instance "
                    ."table: {$this->connection->ErrorMsg()}"
                );
            }

            $data['settings']['BD_DATABASE'] = $data['id'];

            if (!$this->update($data)) {
                throw new InstanceNotRegisteredException(
                    "Could not create the instance reference into the instance table:"
                    .$this->connection->ErrorMsg()
                );
            }

            return $data;

        } elseif (isset ($_POST['timezone'])) {
            // If instance name or contact mail already
            // exists and comes from openhost form
            if ($instanceExists) {
                echo 'instance_exists';
            } elseif ($emailExists) {
                echo 'mail_exists';
            }
        } else {
            //If instance name or contact mail already exists and comes from manager
            if ($instanceExists) {
                throw new InstanceNotRegisteredException(
                    _("Instance internal name is already in use.")
                );
            } elseif ($emailExists) {
                throw new InstanceNotRegisteredException(
                    _("Instance contact mail is already in use.")
                );
            }
        }

        return false;
    }

    /**
     * Deletes one instance given its id.
     *
     * @param  integer $id The instance id.
     * @return boolean     True, if the instance is deleted successfully.
     *                     Otherwise, returns false.
     */
    public function delete($id)
    {
        $instance = $this->find($id);

        if (!$instance) {
            return false;
        }

        $assetFolder = realpath(
            SITE_PATH . DS . 'media' . DS . $instance->internal_name
        );
        $backupPath = BACKUP_PATH . DS . $instance->id . "-"
            . $instance->internal_name . DS . "DELETED-" . date("YmdHi");
        $errors = array();

        try {
            $this->backupInstanceReference($id, $backupPath);
            $this->remove($instance);

            $this->backupAssets($assetFolder, $backupPath);
            $this->deleteDefaultAssets($instance->internal_name);

            $database = $instance->settings['BD_DATABASE'];
            $this->backupDatabase($database, $backupPath);
            $this->deleteDatabaseForInstance($database);
        } catch (DeleteRegisteredInstanceException $e) {
            $errors []= $e->getMessage();
        } catch (DefaultAssetsForInstanceNotDeletedException $e) {
            $errors []= $e->getMessage();
            $this->restoreInstanceReferenceInManager($backupPath);
        } catch (DatabaseForInstanceNotDeletedException $e) {
            $errors []= $e->getMessage();
            $this->restoreInstanceReferenceInManager($backupPath);
            $this->restoreAssetsForInstance($backupPath);
            $this->restoreDatabaseForInstance($backupPath);
        }

        if (count($errors) > 0) {
            return $errors;
        }

        $this->deleteCacheForInstancedomains($instance->domains);

        return true;
    }

    /**
     * undocumented function
     *
     * @return void
     * @author
     **/
    public function deleteCacheForInstancedomains($instanceDomains)
    {
        $domains = explode(',', $instanceDomains);

        foreach ($domains as $domain) {
            $domain = trim($domain);
            $this->cache->delete($domain, 'instance');
        }
    }

    /**
     * Deletes the database given its name.
     *
     * @param  string  $database The database name.
     * @return boolean           True, if the database is deleted successfully.
     *
     * @throws DatabaseForInstanceNotDeletedException If the database couldn't
     *                                                be deleted.
     */
    public function deleteDatabaseForInstance($database)
    {
        $sql = "DROP DATABASE `$database`";

        if (!$this->conn->executeQuery($sql)) {
            throw new DatabaseForInstanceNotDeletedException(
                "Could not drop the database"
            );
        }

        return true;
    }

    /*
     * Deletes the default assets for the instance given its internal name.
     *
     * @param  string  $name The instance internal name.
     * @return boolean       True it assets were deleted successfully.
     *                       Otherwise, returns false.
     */
    public function deleteDefaultAssets($name)
    {
        $target = SITE_PATH . DS . 'media' . DS . $name;
        if (!is_dir($target)) {
            return false;
        }

        if (!fm::deleteDirectoryRecursively($target)) {
            return false;
        }

        return true;
    }

    /**
     * Check if a contact email is already in use.
     *
     * @param  string  $mail The email to check.
     * @return boolean       True if the email is already in use. Otherwise,
     *                       returns false.
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
     * @param  array   $criteria The criteria used to search.
     * @return integer           The number of found instances.
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
     * @param  integer  $id Instance id.
     * @return Instance
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
     * @param  array|string $criteria        The criteria used to search.
     * @param  array        $order           The order applied in the search.
     * @param  integer      $elementsPerPage The max number of elements.
     * @param  integer      $page            The current page.
     * @param  integer      $offset          The offset to start with.
     * @return array                         The matched elements.
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
     * @param  array $data Array of instance ids.
     * @return array       Array of contents.
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
     * Count total contents in for an instance.
     *
     * @param array $settings The instance settings.
     */
    public function getDBInformation(Instance &$instance)
    {
        $database = $instance->getDatabaseName();

        $cacheId = 'instance' . $this->cacheSeparator . $instance->id
            . $this->cacheSeparator . 'information';

        $totals = $this->cache->fetch($cacheId);

        // If was not fetched from APC now is turn of DB
        if (!$totals) {
            $sql = 'SELECT content_type_name as type, count(*) as total '
                 .'FROM contents GROUP BY `fk_content_type`';

            $this->conn->selectDatabase($database);
            $rs = $this->conn->fetchAll($sql);


            if ($rs !== false) {
                foreach ($rs as $value) {
                    $totals[$value['type']] = $value['total'];
                }
            }

            $this->cache->save($cacheId, $totals, 300);
        }

        $this->conn->selectDatabase($database);
        $sql = "SELECT * FROM `settings`";

        $rs = $this->conn->executeQuery($sql);

        $settings = array();
        if ($rs !== false) {
            foreach ($rs as $value) {
                $settings[$value['name'] ] = unserialize($value['value']);
            }
        }

        return array($totals, $settings);
    }

    /**
     * Check if an instance already exists.
     *
     * @param  string  $name The instance internal name.
     * @return boolean       True if instance exists. Otherwise, returns false.
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
     * Restores the assets for an instance.
     *
     * @param  string  $path The path where extract the assets.
     * @return boolean       True, if assets were extracted successfully.
     *
     * @throws DefaultAssetsForInstanceNotDeletedException If assets not found.
     */
    public function restoreAssetsForInstance($path)
    {
        $tgzFile = $path . DS . "media.tar.gz";
        if (!fm::decompressTgz($tgzFile, "/")) {
            throw new DefaultAssetsForInstanceNotDeletedException(
                "Could not compress assets directory."
            );
        }

        return true;
    }

    /**
     * Restores an instance database from a source.
     *
     * @param  string  $source The path to the source.
     * @param  string  $target The target database.
     * @return boolean         True if the command was executed successfully.
     *                         Otherwise, returns false.
     */
    public function restoreDatabase($source, $target = null)
    {
        $cmd = "mysql -u{$this->conn->connectionParams['user']}"
            . " -p{$this->conn->connectionParams['password']}"
            . " -h{$this->conn->connectionParams['host']}"
            . ($target ? " $target"  : '')
            . " < $source";

        exec($cmd, $output, $result);

        if ($result != 0) {
            return false;
        }

        return true;
    }

    /**
     * Restores instance reference data to the instances table.
     *
     * @param  string  $path Backup directory.
     * @return boolean       True, if instance was restored successfully.
     *                       Otherwise, returns false.
     */
    public function restoreInstanceReferenceInManager($path)
    {
        $dump = "mysql -u". $this->conn->connectionParams['user'] .
                " -p" . $this->conn->connectionParams['password'] .
                " " . $this->conn->connectionParams['dbname'] .
                " < " . $path . DS . "instanceReference.sql";

        exec($dump, $output, $return_var);

        if ($return_var!=0) {
            return false;
        }

        return true;
    }

    /**
     * Updates the instance data.
     *
     * @param  array   $data The instance data.
     * @return boolean       True, if instance was restored successfully.
     *                       Otherwise, returns false.
     */
    public function update($data)
    {
        $instance = $im->findBy(
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
                $values[$key] = $value;
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

            $sql = rtrim($sql, ',') . 'WHERE id = ' . $instance->id;
        }

        $this->conn->selectDatabase('onm-instances');
        $this->conn->executeQuery($sql);

        if (is_null($instance->id)) {
            $instance->id = $this->conn->lastInsertId();
        }

        $this->cache->delete('instance' . $this->cacheSeparator . $instance->id);
        $this->cache->setNamespace($previousNamespace);
    }

    /**
     * Reload the instance properties from database.
     *
     * @param Instance $instance The instance object.
     */
    public function refresh(Instance &$instance)
    {
        if (!$instance->id) {
            throw new InstanceNotRegisteredException();
        }

        $sql = 'SELECT * FROM instances WHERE id = ' . $instance->id;

        $this->conn->selectDatabase('onm-instances');
        $rs = $this->conn->fetchAssoc($sql);

        $instance = new Instance();
        foreach ($rs as $key => $value) {
            if (is_array($instance->{$key})) {
                if ($key == 'domains') {
                    $instance->{$key} = explode(',', $value);
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
        $rs = $this->conn->executeQuery($sql, array($id));

        if (!$rs) {
            throw new DeleteRegisteredInstanceException(
                "Could not delete instance reference."
            );
        }

        $this->cache->delete('instance' . $this->cacheSeparator . $instance->id);
    }
}
