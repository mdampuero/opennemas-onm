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

/**
 * Class for manage ONM instances.
 *
 * @package    Onm
 * @subpackage Instance
 */
class InstanceManager
{
    /**
     * The connection to the database
     **/
    private $connection = null;

    /**
     * @var Onm\Request Singleton instance
     */
    protected static $instance;

    /*
     * Get available templates
     *
     */
    public static function getAvailableTemplates()
    {
        // Change this to get dinamically templates from folder
        foreach (glob(SITE_PATH.DS.'themes'.DS.'*') as $value) {
            $parts             = preg_split("@/@", $value);
            $name              = $parts[count($parts)-1];
            $templates [$name] = ucfirst($name);
        }
        unset($templates['admin']);
        unset($templates['manager']);

        return $templates;
    }

    /*
     * Gets one Database connection
     *
     * @param array $connectionData the parameters to build the connection
     *
     * @return Onm\DatabaseConnection the database connection object instance
     */
    public static function getConnection($connectionData = null)
    {
        // Database
        $conn = getService('db_conn_manager');
        if (!is_null($connectionData)
            && is_array($connectionData)
        ) {
            $conn = getService('db_conn');
            $conn = $conn->selectDatabase($connectionData['BD_DATABASE']);
        }

        // Check if adodb is log enabled
        $conn->LogSQL();

        return $conn;
    }

    /*
     * Initializes the Request object
     *
     */
    public function __construct($databaseConnection, $cache)
    {
        $this->connection = $databaseConnection;
        $this->cache      = $cache;

        return $this;
    }

    /**
     * Backup assets data of a particular instance.
     * In case of error, throw a DeleteRegisteredInstanceException.
     *
     * @param String $mediaPath Assets directory
     * @param String $backupPath Backups directory
     *
     * @author
     **/
    public function backupAssetsForInstance($mediaPath, $backupPath)
    {
        if (!fm::createDirectory($backupPath)) {
            return false;
        }

        $tgzFile = $backupPath.DS."media.tar.gz";
        if (!fm::compressTgz($mediaPath, $tgzFile)) {
            return false;
        }

        return true;

    }

    /**
     * Backup database of a particular instance.
     * In case of error, throw a DeleteRegisteredInstanceException.
     *
     *
     * @author
     **/
    public function backupDatabaseForInstance($database, $backupPath)
    {
        if (!fm::createDirectory($backupPath)) {
            return false;
        }

        $conn = getService('db_conn_manager');

        $dump = "mysqldump -u".$conn->connectionParams['user'].
                " -p".$conn->connectionParams['password']." --databases ".
                "'".$database."'".
                " > ".$backupPath.DS."database.sql";

        exec($dump, $output, $return_var);

        if ($return_var!=0) {
            return false;
        }

        return true;
    }

    /**
     * Backup data of a particular instance from the instances table.
     * In case of error, throw a DeleteRegisteredInstanceException.
     *
     * @param int $id the id of the instance we need to dump
     * @param String $backupPath Backups directory
     *
     * @author
     **/
    public function backupInstanceReferenceInManager($id, $backupPath)
    {
        if (!fm::createDirectory($backupPath)) {
            return false;
        }

        $conn = getService('db_conn_manager');

        $dump = "mysqldump -u".$conn->connectionParams['user'].
                " -p".$conn->connectionParams['password'].
                " --no-create-info --where 'id=".$id."' ".
                $conn->connectionParams['dbname'].
                " instances > ".$backupPath.DS."instanceReference.sql";

        exec($dump, $output, $return_var);

        if ($return_var != 0) {
            fm::deleteDirectoryRecursively($backupPath);
            return false;
        }

        return true;
    }

        /**
     * Backup instance database user.
     * In case of error, throw a DeleteRegisteredInstanceException.
     *
     *
     * @author
     **/
    public function backupInstanceUserFromDatabaseManager($user, $backupPath)
    {
        if (!fm::createDirectory($backupPath)) {
            return false;
        }

        $sql = "show grants for `{$user}`@'localhost'";
        $rs = $this->connection->Execute($sql);

        if (!$rs || $rs->fields === false) {
            return false;
        }

        if (!is_writable($backupPath)) {
            return false;
        }
        $filePath = $backupPath.DS."user.sql";
        $handle = fopen($filePath, "w");
        foreach ($rs->fields as $userInfo) {
            fwrite($handle, $userInfo.";\n");
        }
        fclose($handle);

        return true;
    }

    /*
     * Change activated flag for one instance given its id
     *
     * @param $id
     */
    public function changeActivated($id, $flag)
    {
        $instance = $this->read($id);

        $sql = "UPDATE instances SET activated = ? WHERE id = ?";
        $rs = $this->connection->Execute($sql, array($flag, $id));

        if (!$rs) {
            return false;
        }

        $this->deleteCacheForInstancedomains($instance->domains);

        return true;
    }

    /*
     * Check for repeated internalNameShort
     *
     */
    public function checkInstanceExists($internalName)
    {
        $sql = "SELECT count(*) as instance_exists FROM instances "
             . "WHERE `internal_name` = ?";
        $rs = $this->connection->Execute($sql, array($internalName));

        if ($rs && $rs->fields['instance_exists'] > 0) {
            return true;
        } elseif (!$rs) {
            throw new Exception(
                'Error in sql execution:'
                .' EXEC_LINE: {$execLine} \n OUTPUT: {$output}'
            );
        }

        return false;
    }

    /*
     * Check for repeated internalNameShort
     *
     */
    public function checkInternalShortName($data)
    {
        // Generate internalnameShort
        $internalNameShort = $data['settings']['BD_DATABASE'];

        // Check if the generated InternalShortName already exists
        $sql = "SELECT count(*) as internalShort_exists FROM instances "
             . "WHERE `settings` REGEXP '".$internalNameShort."[0-9]*'";

        $rs = $this->connection->Execute($sql);


        if ($rs && $rs->fields['internalShort_exists'] > 0) {
            $num = $rs->fields['internalShort_exists'];
            $data['settings']['BD_USER'] = $internalNameShort.$num;
            $data['settings']['BD_DATABASE'] = $internalNameShort.$num;
        }

        return $data;
    }

    /*
     * Check for repeated internalNameShort
     *
     */
    public function checkMailExists($mail)
    {
        // Check if the email already exists
        $sql = "SELECT count(*) as email_exists FROM instances "
              . "WHERE `contact_mail` = ?";
        $rs = $this->connection->Execute($sql, array($mail));

        if ($rs && $rs->fields['email_exists'] > 0) {
            return true;
        } elseif (!$rs) {
            throw new Exception(
                'Error in sql execution:'
                .' EXEC_LINE: {$execLine} \n OUTPUT: {$output}'
            );
        }

        return false;
    }

    /*
     * Copies the default assets for the new instance given its internal name
     *
     * @param $name the name of the instance
     */
    public function copyDefaultAssetsForInstance($name)
    {
        $mediaPath = SITE_PATH.DS.'media'.DS.$name;
        if (!file_exists($mediaPath)) {
            if (!fm::recursiveCopy(
                SITE_PATH.DS.'media'.DS.'default',
                $mediaPath
            )) {
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
     * Creates one instance given some data
     *
     * @param  array the configuration for create the configuration file
     *
     * @return boolean true if all went well
     **/
    public function create($data)
    {
        $errors = array();

        try {

            $data = $this->createInstanceReferenceInManager($data);

            $this->createDatabaseForInstance($data);

            $this->copyDefaultAssetsForInstance($data['internal_name']);

        } catch (InstanceNotRegisteredException $e) {
            $errors []= $e->getMessage();
        } catch (DatabaseForInstanceNotCreatedException $e) {
            $errors []= $e->getMessage();
            $this->deleteDatabaseForInstance($data['settings']['BD_DATABASE']);
            $this->deleteInstanceUserFromDatabaseManager($data['settings']['BD_USER']);
            $this->deleteInstanceReferenceInManager($data['id']);
        } catch (DefaultAssetsForInstanceNotCopiedException $e) {
            $errors []= $e->getMessage();
            $assetFolder = SITE_PATH.DS.'media'.DS.$data['internal_name'];
            $this->deleteDefaultAssetsForInstance($assetFolder);
            $this->deleteDatabaseForInstance($data['settings']['BD_DATABASE']);
        }

        if (count($errors) > 0) {
            return $errors;
        }

        return true;
    }

    /**
     * Creates and imports default database for the new instance
     *
     * @param $arg
     **/
    public function createDatabaseForInstance($data)
    {
        // Get manager database connection and creates the requested database
        $conn = getService('db_conn_manager');

        $sql = "CREATE DATABASE `{$data['settings']['BD_DATABASE']}`";
        $rs = $conn->Execute($sql);

        //Create new mysql user for this instance and grant usage and privileges
        $sql2 = "CREATE USER `{$data['settings']['BD_USER']}`@'localhost' "
              . "IDENTIFIED BY '{$data['settings']['BD_PASS']}' ";
        $sql3 = "GRANT USAGE ON `{$data['settings']['BD_DATABASE']}`.* "
              . "TO `{$data['settings']['BD_USER']}`@'localhost' ";
        $sql4 = "GRANT ALL PRIVILEGES ON `{$data['settings']['BD_DATABASE']}`.*"
              . " TO '{$data['settings']['BD_USER']}'@'localhost'";
        $rs2 = $conn->Execute($sql2);
        $rs3 = $conn->Execute($sql3);
        $rs4 = $conn->Execute($sql4);

        if (!$rs) {

            throw new DatabaseForInstanceNotCreatedException(
                'Could not create the default database for the instance '.
                $conn->ErrorMsg()
            );
        }
        if (!$rs2) {
            throw new DatabaseForInstanceNotCreatedException(
                'Could not create the default user for the instance'
            );
        }

        if (!$rs3) {
            throw new DatabaseForInstanceNotCreatedException(
                'Could not grant usage to the default user for the instance database'
            );
        }
        if (!$rs4) {
            throw new DatabaseForInstanceNotCreatedException(
                'Could not grant all privileges to the default user for the instance database'
            );
        }

        $exampleDatabasePath = realpath(APPLICATION_PATH.DS.'db'.DS.'instance-default.sql');
        $execLine = "mysql -h {$conn->connectionParams['host']} "
            ."-u{$conn->connectionParams['user']}"
            ." -p{$conn->connectionParams['password']} "
            ."{$data['settings']['BD_DATABASE']} < {$exampleDatabasePath}";
        exec($execLine, $output, $exitCode);
        if ($exitCode > 0) {
            throw new DatabaseForInstanceNotCreatedException(
                'Could not create the default database for the instance:'
                .' EXEC_LINE: {'.$execLine.'} \n OUTPUT: {'.$output.'}'
            );
        }

        // Insert user with data from the openhost form
        //TODO: PROVISIONAL WHILE DONT DELETE $GLOBALS['application']->conn
        //// is used in settings set
        $im = getService('instance_manager');
        $GLOBALS['application']->conn =
            $im->getConnection($data['settings']);

        if (isset($data['user_name'])
            && isset ($data['token'])
            && isset ($data['user_mail'])
            && isset ($data['user_password'])
        ) {
            $sql = "INSERT INTO users (`username`, `token`, `sessionexpire`,
                                       `email`, `password`, `name`, `fk_user_group`)
                    VALUES (?,?,?,?,?,?,?)";

            $values = array($data['user_name'], $data['token'], 60,
                            $data['user_mail'], md5($data['user_password']),
                            $data['user_name'], 5
            );

            if (!$GLOBALS['application']->conn->Execute($sql, $values)) {

                throw new DatabaseForInstanceNotCreatedException(
                    'Could not create the default database for the instance -creating user'
                );
            }

            $idNewUser = $GLOBALS['application']->conn->Insert_ID();
            $userPrivSql = "INSERT INTO `users_content_categories` "
                    ."(`pk_fk_user`, `pk_fk_content_category`) "
                    ."VALUES ({$idNewUser}, 0), ({$idNewUser}, 22), ({$idNewUser}, 23), ({$idNewUser}, 24), "
                    ."       ({$idNewUser}, 25), ({$idNewUser}, 26), ({$idNewUser}, 27), "
                    ."       ({$idNewUser}, 28), ({$idNewUser}, 29), ({$idNewUser}, 30), ({$idNewUser}, 31)";

            if (!$GLOBALS['application']->conn->Execute($userPrivSql)) {
                throw new DatabaseForInstanceNotCreatedException(
                    'Could not create the default database for the instance - privileges'
                );
            }
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

        if (!s::set(
            'site_title',
            $data['name'].' - '.s::get('site_title')
        )) {
            throw new DatabaseForInstanceNotCreatedException(
                'Could not create the default database for the instance - site_title'
            );
        }

        s::invalidate('site_description');

        if (!s::set(
            'site_description',
            $data['name'].' - '.s::get('site_description')
        )) {
            throw new DatabaseForInstanceNotCreatedException(
                'Could not create the default database for the instance - site_description'
            );
        }

        s::invalidate('site_keywords');

        if (!s::set(
            'site_keywords',
            $data['name'].' - '.s::get('site_keywords')
        )) {
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
     * @param  array the configuration for creating the configuration file
     *
     * @return boolean/string true if all went well, string if not.
     **/
    public function createInstanceReferenceInManager($data)
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
        $instanceExists = $this->checkInstanceExists($data['internal_name']);

        // Check if the email already exists
        $emailExists = $this->checkMailExists($data['user_mail']);

        // If doesn´t exist the instance in the database and doesn't exist contact mail proceed
        if (!$instanceExists && !$emailExists) {
            $createIntanceSql = "INSERT INTO instances "
                  . "(name, internal_name, domains, "
                  . "activated, settings, contact_mail) "
                  . "VALUES (?, ?, ?, ?, ?, ?)";
            $values = array(
                $data['name'],
                $data['internal_name'],
                $data['domains'],
                $data['activated'],
                serialize($data['settings']),
                $data['user_mail'],
            );

            $createIntanceRs = $this->connection->Execute($createIntanceSql, $values);
            if (!$createIntanceRs) {
                throw new InstanceNotRegisteredException(
                    "Could not create the instance reference into the instance "
                    ."table: {$this->connection->ErrorMsg()}"
                );
            }

            $data['id'] = $this->connection->Insert_ID();
            if (!$this->update($data)) {
                $delSql = "DELETE FROM instances WHERE id=?";
                $rs = $this->connection->Execute($delSql, array($data['id']));
                if (!$rs) {
                    return false;
                }
                throw new InstanceNotRegisteredException(
                    "Could not create the instance reference into the instance "
                    ."table: {$this->connection->ErrorMsg()}"
                );
            }

            $data['settings']['BD_USER'] = $data['id'];
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

    /*
     * Deletes one instance given its id
     *
     * @param string id the id for this instance
     *
     * @return boolean
     */
    public function delete($id)
    {
        $instance = $this->read($id);

        if (!$instance) {
            return false;
        }

        $errors = array();
        $backupPath = BACKUP_PATH.DS.$instance->id."-".$instance->internal_name.
                          DS."DELETED-".date("YmdHi");
        try {
            $this->backupInstanceReferenceInManager($id, $backupPath);
            $this->deleteInstanceReferenceInManager($id);

            $assetFolder = realpath(SITE_PATH.DS.'media'.DS.$instance->internal_name);
            $this->backupAssetsForInstance($assetFolder, $backupPath);
            $this->deleteDefaultAssetsForInstance($assetFolder);

            $database = $instance->settings['BD_DATABASE'];
            $this->backupDatabaseForInstance($database, $backupPath);
            $this->deleteDatabaseForInstance($database);

            $user = $instance->settings['BD_USER'];
            $this->backupInstanceUserFromDatabaseManager($user, $backupPath);
            $this->deleteInstanceUserFromDatabaseManager($user);
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
            $this->restoreInstanceUserFromDatabaseManager($backupPath);
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
            getService('cache')->delete($domain, 'instance');
        }
        // die();
    }

    /**
     * Deletes the database given an intance internal_name
     *
     * @return void
     * @author
     **/
    public function deleteDatabaseForInstance($database)
    {
        $sql = "DROP DATABASE `$database`";

        if (!$this->connection->Execute($sql)) {
            throw new DatabaseForInstanceNotDeletedException(
                "Could not drop the database"
            );
        }

        return true;
    }

    /*
     * Copies the default assets for the new instance given its internal name
     *
     * @param $name the name of the instance
     * @param String $backupPath Backups directory
     *
     */
    public function deleteDefaultAssetsForInstance($mediaPath)
    {
        if (!is_dir($mediaPath)) {
            return false;
        }

        if (!fm::deleteDirectoryRecursively($mediaPath)) {
            return false;
        }

        return true;
    }

    /**
     * Delete one instance reference from the instances table.
     * In case of error, throw a DeleteRegisteredInstanceException.
     *
     * @param int $id the id of the instance we need to delete
     * @param String $backupPath Backups directory
     *
     **/
    public function deleteInstanceReferenceInManager($id)
    {
        $sql = "DELETE FROM instances WHERE id=?";
        $rs = $this->connection->Execute($sql, array($id));

        if (!$rs || $this->connection->Affected_Rows() == 0) {
            throw new DeleteRegisteredInstanceException(
                "Could not delete instance reference."
            );
        }
    }

    /**
     * Deletes the user of this instance from the database manager
     * given an database user name
     *
     * @return void
     * @author
     **/
    public function deleteInstanceUserFromDatabaseManager($user)
    {
        if ($user == 'root') {
            return true;
        }

        $sql = "DROP USER `{$user}`@'localhost'";

        if (!$this->connection->Execute($sql)) {
            throw new DatabaseForInstanceNotDeletedException(
                "Could not drop the database user"
            );
        }

        return true;
    }

    /**
     * Delete one instance reference from the instances table.
     *
     * @param string $internalName the internal name of
     *                             the instance we need to delete
     *
     * @return boolean false if something went wrong
     * @author
     **/
    public function deleteInstanceWithInternalName($internalName)
    {
        $sql = "DELETE FROM `instances` WHERE  `internal_name` = ?";
        $values = array($internalName);

        if (!$this->connection->Execute($sql, $values)
            || $this->connection->Affected_Rows()==0) {
            return false;
        }

        return true;
    }

    /**
     * Builds the instance object given a serverName
     *
     * @param string $serverName the server name of the instance to fetch
     *
     * @return \Onm\Instance|null the instance object
     **/
    public function fetchInstance($serverName)
    {
        $previousNamespace = $this->cache->getNamespace();

        $this->cache->setNamespace('instance');
        $instancesMatched = $this->cache->fetch($serverName);

        if (!is_array($instancesMatched)) {
            //TODO: improve search for allowing subdomains with wildcards
            $sql = "SELECT SQL_CACHE * FROM instances WHERE domains LIKE '%{$serverName}%'";
            $this->connection->SetFetchMode(ADODB_FETCH_ASSOC);
            $rs = $this->connection->Execute($sql);

            if (!$rs) {
                return false;
            }

            $instancesMatched = $rs->GetArray();
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
     * Builds the instance object given an internal name
     *
     * @return \Onm\Instance|null the instance object
     **/
    public function fetchInstanceFromInternalName($internalName)
    {
        $sql = "SELECT SQL_CACHE * FROM instances WHERE internal_name = ?";
        $this->connection->SetFetchMode(ADODB_FETCH_ASSOC);
        $rs = $this->connection->Execute($sql, array($internalName));

        if (!$rs) {
            return false;
        }

        $matchedInstance = $rs->GetArray();
        $instance = $this->loadInstanceProperties($matchedInstance[0]);

        return $instance;
    }

    /**
     * Gets a list of instances
     *
     * @param array $params the list of filters to use when searching instances
     *
     * @return array list of instances that match the search criteria
     **/
    public function findAll($params = array())
    {
        $instances = array();

        if (!empty($params['name']) && !empty($params['email'])) {
            $sql = "SELECT * FROM instances "
                 ."WHERE (name LIKE '%".$params['name']."%' OR "
                 ."domains LIKE '%".$params['name']."%') AND "
                 ."contact_mail LIKE '%".$params['email']."%' ORDER BY id DESC";
        } elseif (!empty($params['name'])) {
            $sql = "SELECT * FROM instances "
                 ."WHERE name LIKE '%".$params['name']."%' OR "
                 ."domains LIKE '%".$params['name']."%' ORDER BY id DESC";
        } elseif (!empty($params['email'])) {
            $sql = "SELECT * FROM instances "
                 ."WHERE contact_mail LIKE '%".$params['email']."%' ORDER BY id DESC";
        } else {
            $sql = "SELECT * FROM instances ORDER BY id DESC";
        }

        $rs = $this->connection->Execute($sql);

        if (!$rs) {

            return false;
        }

        foreach ($rs as $key) {
            $instance = new \stdClass();
            $instance->id        = $key["id"];
            $instance->name      = $key["name"];
            $instance->activated = $rs->fields["activated"];
            $instance->domains   = $key["domains"];
            $instance->settings  = unserialize($key['settings']);
            $instances           []= $instance;
        }

        return $instances;
    }

     /*
     * count total contents in one instance
     *
     * @param string id the id for this instance
     */
    public function getDBInformation($settings)
    {
        // Fetch caches if exist
        $key = CACHE_PREFIX."getDBInformation_totals_".$settings['BD_DATABASE'];
        $totals = $this->cache->fetch($key);

        // If was not fetched from APC now is turn of DB
        if (!$totals) {
            $dbConection = self::getConnection($settings);

            $sql = 'SELECT count(*) as total, fk_content_type as type '
                 .'FROM contents GROUP BY `fk_content_type`';

            $rs = $dbConection->Execute($sql);

            if ($rs !== false) {
                while (!$rs->EOF) {

                    $totals[ $rs->fields['type'] ] = $rs->fields['total'];
                    $rs->MoveNext();
                }
            }

            $this->cache->save(
                CACHE_PREFIX . "getDBInformation_totals_".$settings['BD_DATABASE'],
                $totals,
                300
            );
        }

        if (!isset($dbConection) || empty($dbConection)) {
            $dbConection = self::getConnection($settings);
        }

        $sql = 'SELECT * FROM settings';

        $rs = $dbConection->Execute($sql);

        $information = array();
        if ($rs !== false) {
            while (!$rs->EOF) {
                $information[ $rs->fields['name'] ] =
                    @unserialize($rs->fields['value']);
                $rs->MoveNext();
            }
        }

        return array($totals, $information);
    }

    /**
     * Fetches one onm instance from DB given a server name
     *
     *
     * @param string $serverName the domain name for one instance
     *
     * @return \Onm\Instance dummy object with properties for the loaded instance
     * @return false    if the instance doesn't exists
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

            $instance->boot();

            return $instance;
        }

        $instance = $this->fetchInstance($serverName);

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
     * Fetches one onm instance from DB given its internal name
     *
     *
     * @param string $internalName the internal name for one instance
     *
     * @return Instance dummy object with properties for the loaded instance
     * @return false    if the instance doesn't exists
     **/
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
     * Builds an \Onm\Instance object from an array of instance properties
     *
     * @param  array $matchedInstance the list of properties to assign the new
     *                                Instance object.
     *
     * @return \Onm\Instance the Instance object
     **/
    public function loadInstanceProperties($matchedInstance)
    {
        $instance = new Instance();
        foreach ($matchedInstance as $key => $value) {
            $instance->{$key} = $value;
        }

        return $instance;
    }

    /*
     * Gets one instances
     *
     */
    public function read($id)
    {
        $sql = "SELECT SQL_CACHE * FROM instances WHERE id = ?";
        $rs = $this->connection->Execute($sql, array($id));
        if (!$rs) {
            return false;
        }

        if ($rs->fields === false) {
            return false;
        }

        $instance = new \stdClass();
        foreach ($rs->fields as $key => $value) {
            $instance->{$key} = $value;
        }
        $instance->settings = unserialize($instance->settings);

        return $instance;
    }

    /**
     * Restore instance reference data to the instances table.
     *
     * @param int $id the id of the instance we need to dump
     *
     * @return boolean false if something went wrong
     *
     * @author
     **/
    public function restoreAssetsForInstance($backupPath)
    {
        $tgzFile = $backupPath.DS."media.tar.gz";
        if (!fm::decompressTgz($tgzFile, "/")) {
            throw new DefaultAssetsForInstanceNotDeletedException(
                "Could not compress assets directory."
            );
        }

        return true;
    }

    /**
     * Restore instance database.
     *
     *
     * @return boolean false if something went wrong
     *
     * @author
     **/
    public function restoreDatabaseForInstance($backupPath)
    {
        $conn = getService('db_conn_manager');

        $dump = "mysql -u".$conn->connectionParams['user'].
                " -p".$conn->connectionParams['password'].
                " < ".$backupPath.DS."database.sql";

        exec($dump, $output, $return_var);

        if ($return_var!=0) {
            return false;
        }

        return true;
    }

    /**
     * Restore instance reference data to the instances table.
     *
     * @param String $backupPath Backups directory
     *
     * @return boolean false if something went wrong
     *
     * @author
     **/
    public function restoreInstanceReferenceInManager($backupPath)
    {
        $conn = getService('db_conn_manager');

        $dump = "mysql -u".$conn->connectionParams['user'].
                " -p".$conn->connectionParams['password'].
                " ".$conn->connectionParams['dbname'].
                " < ".$backupPath.DS."instanceReference.sql";

        exec($dump, $output, $return_var);

        if ($return_var!=0) {
            return false;
        }

        return true;
    }

    /**
     * Restore instance database user.
     *
     *
     * @return boolean false if something went wrong
     *
     * @author
     **/
    public function restoreInstanceUserFromDatabaseManager($backupPath)
    {
        $conn = getService('db_conn_manager');

        $dump = "mysql -u".$conn->connectionParams['user'].
                " -p".$conn->connectionParams['password'].
                " < ".$backupPath.DS."user.sql";

        exec($dump, $output, $return_var);

        if ($return_var!=0) {
            return false;
        }

        return true;
    }

    /*
     * update
     *
     * @param $data
     */
    public function update($data)
    {
        $instance = $this->fetchInstanceFromInternalName($data['internal_name']);

        $sql = "UPDATE instances SET name=?, internal_name=?, "
             . "domains=?, activated=?, contact_mail=?, settings=? WHERE id=?";
        $values = array(
            $data['name'],
            $data['internal_name'],
            $data['domains'],
            $data['activated'],
            $data['user_mail'],
            serialize($data['settings']),
            $data['id']
        );

        $rs = $this->connection->Execute($sql, $values);
        if (!$rs) {
            return false;
        }

        $this->deleteCacheForInstancedomains($data['domains']);

        return true;
    }
}
