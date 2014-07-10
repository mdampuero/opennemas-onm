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
    /**
     * The database connection.
     *
     * @var DbalWrapper
     */
    private $conn = null;

    /**
     * The cache object.
     *
     * @var CacheInterface
     */
    private $cache = null;

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

    /*
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
     * Change activated flag for one instance given its id.
     *
     * @param  integer $id   The instance id.
     * @param  integer $flag The activated flag.
     * @return boolean       True if action was successful. Otherwise, returns
     *                       false.
     */
    public function changeActivated($id, $flag)
    {
        $instance = $this->read($id);

        $sql = "UPDATE instances SET activated = ? WHERE id = ?";
        $rs = $this->conn->executeQuery($sql, array($flag, $id));

        if (!$rs) {
            return false;
        }

        $this->deleteCacheForInstancedomains($instance->domains);

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
        $instance = $this->read($id);

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
            $this->deleteInstanceReferenceInManager($id);

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
        // die();
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
     * Delete one instance reference from the instances table.
     *
     * @param integer $id The instance id.
     *
     * @throws DeleteRegisteredInstanceException If reference couldn't be
     *                                           deleted.
     */
    public function deleteInstanceReferenceInManager($id)
    {
        $this->conn->selectDatabase('onm-instances');

        $sql = "DELETE FROM instances WHERE id=?";
        $rs = $this->conn->executeQuery($sql, array($id));

        if (!$rs) {
            throw new DeleteRegisteredInstanceException(
                "Could not delete instance reference."
            );
        }
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
        $this->conn->selectDatabase('onm-instances');

        $sql = "SELECT count(*) as email_exists FROM instances "
              . "WHERE `contact_mail` = ?";
        $rs = $this->conn->fetchAssoc($sql, array($email));

        if (!$rs) {
            throw new Exception(
                'Error in sql execution:'
                .' EXEC_LINE: {$execLine} \n OUTPUT: {$output}'
            );
        }

        if ($rs['email_exists'] > 0) {
            return true;
        }

        return false;
    }

    /**
     * Builds the instance object given a server name.
     *
     * @param  string   $serverName The instance server name.
     * @return Instance             The instance object.
     */
    public function fetchInstance($serverName)
    {
        $previousNamespace = $this->cache->getNamespace();

        $this->cache->setNamespace('instance');
        $instancesMatched = $this->cache->fetch($serverName);

        if (!is_array($instancesMatched)) {
            $this->conn->selectDatabase('onm-instances');

            //TODO: improve search for allowing subdomains with wildcards
            $sql = "SELECT * FROM instances WHERE domains REGEXP "
                . "'^$serverName|,$serverName|,$serverName$'";
            $rs = $this->conn->fetchAll($sql);

            if (!$rs) {
                return false;
            }

            $instancesMatched = $rs;
            $this->cache->save($serverName, $instancesMatched);
        }

        if (!(is_array($instancesMatched) && count($instancesMatched) > 0)) {
            return false;
        }

        $matchedInstance = null;
        foreach ($instancesMatched as $element) {
            $domains = explode(',', $element['domains']);
            $domains = array_map(
                function ($instanceDataElem) {
                    return trim(strtolower($instanceDataElem));
                },
                $domains
            );

            if (in_array($serverName, $domains)) {
                $matchedInstance = $element;
                break;
            }
        }

        $instance = $this->loadInstanceProperties($matchedInstance);

        $this->cache->setNamespace($previousNamespace);

        return $instance;
    }

    /**
     * Builds the instance object given an internal name.
     *
     * @param  string   $internalName The instance internal name.
     * @return Instance               The instance object.
     */
    public function fetchInstanceFromInternalName($internalName)
    {
        $this->conn->selectDatabase('onm-instances');

        $sql = "SELECT SQL_CACHE * FROM instances WHERE internal_name = ?";
        $rs = $this->conn->fetchAssoc($sql, array($internalName));

        if (!$rs) {
            return false;
        }

        $instance = $this->loadInstanceProperties($rs);

        return $instance;
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
        $sql = "SELECT * FROM `instances` "
            ."WHERE $filterSQL ORDER BY $orderBySQL $limitSQL";

        $this->conn->selectDatabase('onm-instances');
        $rs = $this->conn->executeQuery($sql);

        if (!$rs) {
            return false;
        }

        foreach ($rs as $value) {
            $instance                = new \stdClass();
            $instance->id            = $value["id"];
            $instance->internal_name = $value["internal_name"];
            $instance->name          = $value["name"];
            $instance->activated     = $value["activated"];
            $instance->domains       = $value["domains"];
            $instance->settings      = unserialize($value['settings']);
            $instances[]             = $instance;
        }

        return $instances;
    }

    /**
     * Counts the instances for content given a criteria
     *
     * @param  array $criteria The criteria used to search.
     */
    public function countBy($criteria)
    {
        $instances = array();

        // Building the SQL filter
        $filterSQL  = $this->getFilterSQL($criteria);

        // Executing the SQL
        $sql = "SELECT COUNT(*) FROM `instances` "
            ."WHERE $filterSQL";

        $this->conn->selectDatabase('onm-instances');
        $rs = $this->conn->fetchArray($sql);

        if (!$rs) {
            return false;
        }

        return $rs[0];
    }

    /*
     * Gets one Database connection
     *
     * @param array $connectionData the parameters to build the connection
     *
     * @return Onm\DatabaseConnection the database connection object instance
     */
    public function getConnection($connectionData = null)
    {
        // Database
        // $conn = getService('db_conn_manager');
        // if (!is_null($connectionData)
        //     && is_array($connectionData)
        // ) {
        //     $conn = getService('db_conn');
        //     $conn = $conn->selectDatabase($connectionData['BD_DATABASE']);
        // }

        // // Check if adodb is log enabled
        // $conn->LogSQL();

        return $this->conn;
    }

    /**
     * Count total contents in for an instance.
     *
     * @param array $settings The instance settings.
     */
    public function getDBInformation($settings)
    {

        // Fetch caches if exist
        $key = CACHE_PREFIX."getDBInformation_totals_".$settings['BD_DATABASE'];
        $totals = $this->cache->fetch($key);

        // If was not fetched from APC now is turn of DB
        if (!$totals) {
            $sql = 'SELECT count(*) as total, fk_content_type as type '
                 .'FROM contents GROUP BY `fk_content_type`';

            $this->conn->selectDatabase($settings['BD_DATABASE']);
            $rs = $this->conn->executeQuery($sql);

            if ($rs !== false) {
                foreach ($rs as $value) {
                    $totals[$value['type']] = $value['total'];
                }
            }

            $this->cache->save(
                CACHE_PREFIX . "getDBInformation_totals_".$settings['BD_DATABASE'],
                $totals,
                300
            );
        }

        $this->conn->selectDatabase($settings['BD_DATABASE']);
        $sql = "SELECT * FROM `settings`";

        $rs = $this->conn->executeQuery($sql);

        $information = array();
        if ($rs !== false) {
            foreach ($rs as $value) {
                $information[$value['name'] ] =
                    @unserialize($value['value']);
            }
        }

        return array($totals, $information);
    }

    /**
     * Check if an instance already exists.
     *
     * @param  string  $name The instance internal name.
     * @return boolean       True if instance exists. Otherwise, returns false.
     */
    public function instanceExists($name)
    {
        $this->conn->selectDatabase('onm-instances');

        $sql = "SELECT count(*) as instance_exists FROM instances "
             . "WHERE `internal_name` = ?";
        $rs = $this->conn->fetchAssoc($sql, array($name));

        if (!$rs) {
            throw new Exception(
                'Error in sql execution:'
                .' EXEC_LINE: {$execLine} \n OUTPUT: {$output}'
            );
        }

        if ($rs['instance_exists'] > 0) {
            return true;
        }

        return false;
    }

    /**
     * Fetches one instance from DB given a server name.
     *
     * @param  string   $serverName The instance domain name.
     * @return Instance             The instance.
     */
    public function load($serverName)
    {
        $instance = false;
        if (preg_match("@\/manager@", $_SERVER["REQUEST_URI"])) {
            $instance = new Instance();
            $instance->internal_name = 'onm_manager';
            $instance->activated = true;

            $instance->settings = array(
                'INSTANCE_UNIQUE_NAME' => $instance->internal_name,
                'MEDIA_URL'            => '',
                'TEMPLATE_USER'        => '',
                'BD_DATABASE'        => 'onm-instances',
            );

            $this->instance = $instance;
            $instance->boot();

            return $instance;
        }

        $instance = $this->fetchInstance($serverName);

        //If found matching instance initialize its contants and return it
        if (is_object($instance)) {
            define('INSTANCE_UNIQUE_NAME', $instance->internal_name);

            $this->instance = $instance;
            $instance->boot();

            // If this instance is not activated throw an exception
            if ($instance->activated != '1') {
                $message =_('Instance not activated');
                throw new \Onm\Instance\NotActivatedException($message);
            }
        } else {
            throw new \Onm\Instance\NotFoundException(_('Instance not found'));
        }

        return $instance;
    }

    /**
     * Fetches one instance from DB given its internal name
     *
     * @param  string   $internalName The instance internal name.
     * @return Instance               The instance object.
     */
    public function loadFromInternalName($internalName)
    {
        $instance = $this->fetchInstanceFromInternalName($internalName);

        //If found matching instance initialize its contants and return it
        if (is_object($instance)) {
            define('INSTANCE_UNIQUE_NAME', $instance->internal_name);

            $instance->boot();

            // If this instance is not activated throw an exception
            if ($instance->activated != '1') {
                $message =_('Instance not activated');
                throw new \Onm\Instance\NotActivatedException($message);
            }
        } else {
            throw new \Onm\Instance\NotFoundException(_('Instance not found'));
        }

        return $instance;
    }

    /**
     * Builds an Instance object from an array of instance properties.
     *
     * @param  array    $matchedInstance Array of properties to load.
     * @return Instance                  The instance object.
     */
    public function loadInstanceProperties($matchedInstance)
    {
        $instance = new Instance();
        foreach ($matchedInstance as $key => $value) {
            $instance->{$key} = $value;
        }

        return $instance;
    }

    /**
     * Gets one instance.
     *
     * @param  integer  $id The instance id.
     * @return Instance     The instance object.
     */
    public function read($id)
    {
        $this->conn->selectDatabase('onm-instances');

        $sql = "SELECT SQL_CACHE * FROM instances WHERE id = ?";
        $rs = $this->conn->fetchAssoc($sql, array($id));
        if (!$rs) {
            return false;
        }

        $instance = new \stdClass();
        foreach ($rs as $key => $value) {
            $instance->{$key} = $value;
        }
        $instance->settings = unserialize($instance->settings);

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
        $instance = $this->fetchInstanceFromInternalName($data['internal_name']);

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
}
