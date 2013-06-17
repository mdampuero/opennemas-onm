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
use Onm\Message as m;
use Onm\Settings as s;
use Onm\Instance\Instance;

/**
 * Class for manage ONM instances.
 *
 * @package    Onm
 * @subpackage Instance
 * @author     Fran Dieguez <fran@openhost.es>
 * @version    Git: $Id: Settings.php Mér Xul 13 01:06:01 2011 frandieguez $
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
     * Initializes the Request object
     *
     */
    public function __construct()
    {
        $this->connection = self::getConnection();

        return $this;
    }

    /**
     * Retrieve singleton instance
     *
     * @return Zend_Loader_Autoloader
     */
    public static function getInstance()
    {
        if (null === self::$instance) {
            self::$instance = new self();
        }

        return self::$instance;
    }

    /**
     * Fetches one onm instance from DB given a server name
     *
     *
     * @param string $serverName the domain name for one instance
     *
     * @return stdClass dummy object with properties for the loaded instance
     * @return false    if the instance doesn't exists
     */
    public function load($serverName)
    {
        // global $sc;
        // $cache = $sc->get('cache');

        $instance = false;
        if (preg_match("@\/manager@", $_SERVER["REQUEST_URI"])) {
            // $instance = $cache->fetch('manager_instance_'.$serverName);
            if (!$instance) {
                global $onmInstancesConnection;

                $instance = new Instance();
                $instance->internal_name = 'onm_manager';
                $instance->activated = true;

                $instance->settings = array(
                    'INSTANCE_UNIQUE_NAME' => $instance->internal_name,
                    'MEDIA_URL'            => '',
                    'TEMPLATE_USER'        => '',
                    'BD_HOST'              => $onmInstancesConnection['BD_HOST'],
                    'BD_USER'              => $onmInstancesConnection['BD_USER'],
                    'BD_PASS'              => $onmInstancesConnection['BD_PASS'],
                    'BD_DATABASE'          => $onmInstancesConnection['BD_DATABASE'],
                    'BD_TYPE'              => $onmInstancesConnection['BD_TYPE'],
                );

                // $cache->save('manager_instance_'.$serverName, $instance);
            }

            $instance->boot();

            return $instance;
        }

        // $instance = $cache->fetch('instance_'.$serverName);
        if (!$instance) {
            //TODO: improve search for allowing subdomains with wildcards
            $sql = "SELECT SQL_CACHE * FROM instances"
                ." WHERE domains LIKE '%{$serverName}%' LIMIT 1";
            $rs = $this->connection->Execute($sql);

            if (!$rs) {
                $this->connection->ErrorMsg();
                return false;
            }

            //If found matching instance initialize its contants and return it
            if ($rs->fields) {

                $instance = new Instance();
                foreach ($rs->fields as $key => $value) {
                    $instance->{$key} = $value;
                }
                define('INSTANCE_UNIQUE_NAME', $instance->internal_name);

                // $cache->save('instance_'.$serverName, $instance);

                $instance->boot();

                // If this instance is not activated throw an exception
                if ($instance->activated != '1') {
                    $message =_('Instance not activated');
                    throw new \Onm\Instance\NotActivatedException($message);
                }

            } else {
                // If this instance doesn't exist check if the request is from manager
                // in that case return a dummie instance.
                throw new \Onm\Instance\NotFoundException(_('Instance not found'));
            }
        }

        return $instance;
    }

    /*
     * Gets one Database connection
     *
     * @param $arg
     */
    public static function getConnection($connectionData = null)
    {
        // Database
        global $onmInstancesConnection;
        if (!is_null($connectionData)
            && is_array($connectionData)
        ) {
            $onmInstancesConnection = $connectionData;
        }
        $conn = \ADONewConnection($onmInstancesConnection['BD_TYPE']);
        $conn->Connect(
            $onmInstancesConnection['BD_HOST'],
            $onmInstancesConnection['BD_USER'],
            $onmInstancesConnection['BD_PASS'],
            $onmInstancesConnection['BD_DATABASE']
        );

        // Check if adodb is log enabled
        $conn->LogSQL();

        return $conn;
    }

    /*
     * Gets a list of instances
     *
     */
    public function findAll($params = array())
    {
        $instances = array();

        if ($params['name'] != '*') {
            $sql = "SELECT * FROM instances "
                 ."WHERE name LIKE '%".$params['name']."%' ORDER BY id DESC";
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
     * Gets one instances
     *
     */
    public function read($id)
    {
        $instances = array();

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

     /*
     * count total contents in one instance
     *
     * @param string id the id for this instance
     */
    public function getDBInformation($settings)
    {
        global $sc;
        $cache = $sc->get('cache');

        // Fetch caches if exist
        $key = CACHE_PREFIX."getDBInformation_totals_".$settings['BD_DATABASE'];
        $totals = $cache->fetch($key);
        $key = CACHE_PREFIX."getDBInformation_infor_".$settings['BD_DATABASE'];
        $information = $cache->fetch($key);


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

            $cache->save(
                CACHE_PREFIX . "getDBInformation_totals_".$settings['BD_DATABASE'],
                $totals,
                300
            );
        }

        if (!$information) {
            if (!isset($dbConection) || empty($dbConection)) {
                $dbConection = self::getConnection($settings);
            }

            $sql = 'SELECT * FROM settings';

            $rs = $dbConection->Execute($sql);

            if ($rs !== false) {
                while (!$rs->EOF) {
                    $information[ $rs->fields['name'] ] =
                        unserialize($rs->fields['value']);
                    $rs->MoveNext();
                }
            }

            $cache->save(
                CACHE_PREFIX . "getDBInformation_infor_".$settings['BD_DATABASE'],
                $information,
                300
            );
        }

        return array($totals, $information);
    }

    /*
     * Change activated flag for one instance given its id
     *
     * @param $id
     */
    public function changeActivated($id, $flag)
    {
        $sql = "UPDATE instances SET activated = ? WHERE id = ?";
        $rs = $this->connection->Execute($sql, array($flag, $id));

        if (!$rs) {
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
        $sql = "UPDATE instances SET name=?, internal_name=?, "
             . "domains=?, activated=?, settings=? WHERE id=?";
        $values = array(
            $data['name'],
            $data['internal_name'],
            $data['domains'],
            $data['activated'],
            serialize($data['settings']),
            $data['id']
        );

        $rs = $this->connection->Execute($sql, $values);
        if (!$rs) {
            return false;
        }

        return true;
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

            // $this->backupApacheConfAndReloadConfiguration($instance->internal_name, $backupPath);
            // $this->deleteApacheConfAndReloadConfiguration($instance->internal_name);
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
        // catch (ApacheConfigurationNotDeletedException $e) {
        //     $errors []= $e->getMessage();
        //     $this->restoreInstanceReferenceInManager($backupPath);
        //     $this->restoreAssetsForInstance($backupPath);
        //     $this->restoreDatabaseForInstance($backupPath);
        //     $this->restoreInstanceUserFromDatabaseManager($backupPath);
        //     $this->restoreApacheConfAndReloadConfiguration($backupPath);
        // }

        if (count($errors) > 0) {
            return $errors;
        }


        return true;
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

            //$this->copyApacheAndReloadConfiguration($data);

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
        // catch (ApacheConfigurationNotCreatedException $e) {
        //     $errors []= $e->getMessage();
        // }

        if (count($errors) > 0) {
            return $errors;
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
                    "Could not create the instance reference into the instance "
                    ."table: {$this->connection->ErrorMsg()}"
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

            die();
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

        if (!$rs || $this->connection->Affected_Rows()==0) {
            throw new DeleteRegisteredInstanceException(
                "Could not delete instance reference."
            );
        }
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

        global $onmInstancesConnection;

        $dump = "mysqldump -u".$onmInstancesConnection['BD_USER'].
                " -p".$onmInstancesConnection['BD_PASS'].
                " --no-create-info --where 'id=".$id."' ".
                $onmInstancesConnection['BD_DATABASE'].
                " instances > ".$backupPath.DS."instanceReference.sql";

        exec($dump, $output, $return_var);

        if ($return_var!=0) {
            fm::deleteDirectoryRecursively($backupPath);
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
        global $onmInstancesConnection;

        $dump = "mysql -u".$onmInstancesConnection['BD_USER'].
                " -p".$onmInstancesConnection['BD_PASS'].
                " ".$onmInstancesConnection['BD_DATABASE'].
                " < ".$backupPath.DS."instanceReference.sql";

        exec($dump, $output, $return_var);

        if ($return_var!=0) {
            return false;
        }

        return true;
    }

    /**
     * Creates apache configuration for this instance and reload Apache.
     *
     * @param  array the configuration for create the configuration file
     *
     * @return boolean true if all went well
     **/
    public function copyApacheAndReloadConfiguration($data)
    {
        $configPath = realpath(APPLICATION_PATH.DS.'config');

        // If default file exists proceed
        if (!empty($configPath)) {
            $apacheConfFile = $configPath.DS.'vhost.conf';
            if (!file_exists($apacheConfFile)) {
                throw new ApacheConfigurationNotCreatedException(
                    "Could not create the Apache vhost config for the instance"
                );
            }

            $apacheConfString = file_get_contents($apacheConfFile);

            // Replace wildcards with the proper settings.
            $replacements = array(
                '@{IP}@'           => $_SERVER['SERVER_ADDR'],
                '@{SITE_DOMAINS}@' => implode(' ', explode(',', $data['domains'])),
                '@{SITE_PATH}@'    => SITE_PATH,
                '@{TMP_PATH}@'     => SYS_LOG_PATH,
                '@{ID}@'           => $data['internal_name'],
            );
            $apacheConfString = preg_replace(
                array_keys($replacements),
                array_values($replacements),
                $apacheConfString
            );

            $instanceConfigPath =
                $configPath.DS.'vhosts.d'.DS.$data['internal_name'];

            // If we can create the instance configuration file reload apache
            if (file_put_contents($instanceConfigPath, $apacheConfString)) {
                //exec("sudo apachectl graceful", $output, $exitCode);
                $apacheCtl = "/usr/sbin/apache2ctl";
                echo exec("sudo $apacheCtl graceful", $output, $exitCode);
                if ($exitCode > 0) {
                    throw new ApacheConfigurationNotCreatedException(
                        "Could not create the Apache vhost config for the instance,".
                        " problems restarting Apache."
                    );
                }
            } else {

                throw new ApacheConfigurationNotCreatedException(
                    "Could not create the Apache vhost config for the instance",
                    1
                );
            }
        }
    }

    /**
     * Deletes the vhost Apache configuration and forces to reload Apache conf
     *
     * @return boolean false if something went wrong
     **/
    public function deleteApacheConfAndReloadConfiguration($name)
    {
        $configPath = realpath(APPLICATION_PATH.DS.'config');
        $instanceConfigPath = $configPath.DS.'vhosts.d'.DS.$name;

        if (file_exists($instanceConfigPath)) {
            return  unlink($instanceConfigPath);
        }

        return false;
    }

    /**
     * Backup the vhost Apache configuration
     *
     * @param String $internalName instance
     * @param String $backupPath Backups directory
     *
     * @author
     **/
    public function backupApacheConfAndReloadConfiguration($internalName, $backupPath)
    {
        if (!fm::createDirectory($backupPath)) {
            return false;
        }

        $configPath = realpath(APPLICATION_PATH.DS.'config'.DS."vhosts.d");
        $vhostFile = $configPath.DS.$internalName;
        if (!file_exists($vhostFile) || !is_writable($backupPath)) {
            return false;
        }

        $backupFile = $backupPath.DS.$internalName.".vhost";
        if (!copy($vhostFile, $backupFile)) {
            return false;
        }

        return true;

    }

    /**
     * Restore the vhost Apache configuration
     *
     * @param String $backupPath Backups directory
     *
     * @return boolean false if something went wrong
     *
     * @author
     **/
    public function restoreApacheConfAndReloadConfiguration($backupPath)
    {
        $vhostPattern = $backupPath.DS."*.vhost";
        $vhostFile = glob($vhostPattern);
        if (!is_array($vhostFile) && count($vhostFile) != 1) {
            return false;
        }
        $configPath = realpath(APPLICATION_PATH.DS.'config'.DS."vhosts.d");
        $vhostFile = basename($vhostFile[0]);
        $newvHost = substr($vhostFile, 0, -6);
        if (!copy($backupPath.DS.$vhostFile, $configPath.DS.$newvHost)) {
            return false;
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
        // Gets global database connection and creates the requested database
        global $onmInstancesConnection;
        $conn = \ADONewConnection($onmInstancesConnection['BD_TYPE']);
        $conn->Connect(
            $onmInstancesConnection['BD_HOST'],
            $onmInstancesConnection['BD_USER'],
            $onmInstancesConnection['BD_PASS']
        );
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
                'Could not create the default database for the instance'
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
        $execLine = "mysql -h {$onmInstancesConnection['BD_HOST']} "
            ."-u{$onmInstancesConnection['BD_USER']}"
            ." -p{$onmInstancesConnection['BD_PASS']} "
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
        $im = $this->getInstance();
        $GLOBALS['application']->conn =
            $im->getConnection($data['settings']);

        if (isset($data['user_name'])
            && isset ($data['user_pass'])
            && isset ($data['user_mail'])
        ) {
            $sql = "INSERT INTO users (`username`, `password`, `sessionexpire`,
                                       `email`, `name`, `fk_user_group`)
                    VALUES (?,?,?,?,?,?)";

            $values = array($data['user_name'], md5($data['user_pass']),  60,
                            $data['user_mail'], $data['user_name'],5);

            if (!$GLOBALS['application']->conn->Execute($sql, $values)) {
                throw new DatabaseForInstanceNotCreatedException(
                    'Could not create the default database for the instance'
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
                    'Could not create the default database for the instance'
                );
            }
            if (!s::set('contact_mail', $data['user_mail'])) {
                throw new DatabaseForInstanceNotCreatedException(
                    'Could not create the default database for the instance'
                );
            }
            if (!s::set('contact_name', $data['user_name'])) {
                throw new DatabaseForInstanceNotCreatedException(
                    'Could not create the default database for the instance'
                );
            }
            if (!s::set('contact_IP', $data['contact_IP'])) {
                throw new DatabaseForInstanceNotCreatedException(
                    'Could not create the default database for the instance'
                );
            }
        }

        //Change and insert some data with instance information
        if (!s::set('site_name', $data['name'])) {
            throw new DatabaseForInstanceNotCreatedException(
                'Could not create the default database for the instance'
            );
        }
        if (!s::set('site_created', $data['site_created'])) {
            throw new DatabaseForInstanceNotCreatedException(
                'Could not create the default database for the instance'
            );
        }
        if (!s::set(
            'site_title',
            $data['name'].' - '.s::get('site_title')
        )) {
            throw new DatabaseForInstanceNotCreatedException(
                'Could not create the default database for the instance'
            );
        }
        if (!s::set(
            'site_description',
            $data['name'].' - '.s::get('site_description')
        )) {
            throw new DatabaseForInstanceNotCreatedException(
                'Could not create the default database for the instance'
            );
        }
        if (!s::set(
            'site_keywords',
            $data['name'].' - '.s::get('site_keywords')
        )) {
            throw new DatabaseForInstanceNotCreatedException(
                'Could not create the default database for the instance'
            );
        }
        if (!s::set('site_agency', $data['internal_name'].'.opennemas.com')) {
            throw new DatabaseForInstanceNotCreatedException(
                'Could not create the default database for the instance'
            );
        }
        if (isset ($data['timezone'])) {
            if (!s::set('time_zone', $data['timezone'])) {
                throw new DatabaseForInstanceNotCreatedException(
                    'Could not create the default database for the instance'
                );
            }
        }

        return true;

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

        global $onmInstancesConnection;

        $dump = "mysqldump -u".$onmInstancesConnection['BD_USER'].
                " -p".$onmInstancesConnection['BD_PASS']." --databases ".
                "'".$database."'".
                " > ".$backupPath.DS."database.sql";

        exec($dump, $output, $return_var);

        if ($return_var!=0) {
            return false;
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
        global $onmInstancesConnection;

        $dump = "mysql -u".$onmInstancesConnection['BD_USER'].
                " -p".$onmInstancesConnection['BD_PASS'].
                " < ".$backupPath.DS."database.sql";

        exec($dump, $output, $return_var);

        if ($return_var!=0) {
            return false;
        }

        return true;
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
        $sql = "DROP USER `{$user}`@'localhost'";

        if (!$this->connection->Execute($sql)) {
            throw new DatabaseForInstanceNotDeletedException(
                "Could not drop the database user"
            );
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
        foreach ($rs as $userInfo) {
            fwrite($handle, $userInfo[0].";\n");
        }
        fclose($handle);

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
        global $onmInstancesConnection;

        $dump = "mysql -u".$onmInstancesConnection['BD_USER'].
                " -p".$onmInstancesConnection['BD_PASS'].
                " < ".$backupPath.DS."user.sql";

        exec($dump, $output, $return_var);

        if ($return_var!=0) {
            return false;
        }

        return true;
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
            throw new DefaultAssetsForInstanceNotDeletedException(
                "Could not delete assets of the instance"
            );
        }

        if (!fm::deleteDirectoryRecursively($mediaPath)) {
            throw new DefaultAssetsForInstanceNotDeletedException(
                "Could not delete assets directory."
            );
        }

        return true;
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
}

/**
 * Exceptions for handling unsuccessfull instance creation
 **/
class InstanceNotRegisteredException extends \Exception
{
}

class DatabaseForInstanceNotCreatedException extends \Exception
{
}

class DefaultAssetsForInstanceNotCopiedException extends \Exception
{
}

class ApacheConfigurationNotCreatedException extends \Exception
{
}

/**
 * Exceptions for handling unsuccessfull instance deletion
 **/
class DeleteRegisteredInstanceException extends \Exception
{
}

class DatabaseForInstanceNotDeletedException extends \Exception
{
}

class DefaultAssetsForInstanceNotDeletedException extends \Exception
{
}

class ApacheConfigurationNotDeletedException extends \Exception
{
}
