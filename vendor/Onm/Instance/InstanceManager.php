<?php
/*
 * This file is part of the onm package.
 * (c) 2009-2011 OpenHost S.L. <contact@openhost.es>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace Onm\Instance;
use \FilesManager as fm,
    \Onm\Message as m,
    \Onm\Settings as s;
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
    private $_connnection = null;

    /**
     * @var Onm\Request Singleton instance
     */
    protected static $_instance;

    /*
     * Initializes the Request object
     *
     */
    public function __construct()
    {
        $this->_connection = self::getConnection();

        return $this;
    }

    /**
     * Retrieve singleton instance
     *
     * @return Zend_Loader_Autoloader
     */
    public static function getInstance()
    {
        if (null === self::$_instance) {
            self::$_instance = new self();
        }

        return self::$_instance;
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
    public function load( $serverName )
    {
        //TODO: improve search for allowing subdomains with wildcards
        $sql = "SELECT SQL_CACHE * FROM instances"
            ." WHERE domains LIKE '%{$serverName}%' LIMIT 1";
        $rs = $this->_connection->Execute($sql);

        if (!$rs) {
            $this->_connection->ErrorMsg();

            return false;
        }

        if (preg_match("@\/manager@", $_SERVER["PHP_SELF"])) {
            $instance = new \stdClass();
            $instance->internalName = 'onm_manager';
            $instance->activated = true;
            $configs = array(
                'INSTANCE_UNIQUE_NAME' => $instance->internalName,
                'MEDIA_URL' => '',
                'TEMPLATE_USER' => '',
            );
            foreach ($configs as $key => $value) {
                define($key, $value);
            }

            return $instance;
        }
        //If found matching instance initialize its contants and return it
        if ($rs->fields) {

            $instance = new \stdClass();
            foreach ($rs->fields as $key => $value) {
                $instance->{$key} = $value;
            }
            define('INSTANCE_UNIQUE_NAME', $instance->internal_name);

            // Transform all the intance settings into application constants.
            $instance->settings = unserialize($instance->settings);
            if (empty($instance->settings['MEDIA_URL'])) {
                $instance->settings['MEDIA_URL'] = implode('',
                    array('http://' ,$_SERVER['HTTP_HOST'], '/media'.'/'));
            }
            foreach ($instance->settings as $key => $value) {
                define($key, $value);
            }

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

        return $instance;
    }

    /*
     * Gets one Database connection
     *
     * @param $arg
     */
    public static function getConnection($connectionData=null)
    {
        // Database
        global $onmInstancesConnection;
        if (
            !is_null($connectionData)
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
        $rs = $this->_connection->Execute($sql);

        if (!$rs) {
            $connection->ErrorMsg();

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
        $rs = $this->_connection->Execute($sql, array($id));

        if (!$rs) {
            $rs->ErrorMsg();

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

        $fetchedFromAPC = false;
        $fetchedFromAPCInfor = false;
        $totals = array();
        $information = array();

        if (extension_loaded('apc')) {
            $key = APC_PREFIX."getDBInformation_totals_".$settings['BD_DATABASE'];
            $totals = apc_fetch($key, $fetchedFromAPC);
            $key = APC_PREFIX."getDBInformation_infor_".$settings['BD_DATABASE'];
            $information = apc_fetch($key, $fetchedFromAPCInfor);
        }

        // If was not fetched from APC now is turn of DB
        if (!$fetchedFromAPC) {
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
            if (extension_loaded('apc')) {
                apc_store(APC_PREFIX . "getDBInformation_totals_"
                    .$settings['BD_DATABASE'], $totals, 300);
            }
        }

        if (!$fetchedFromAPCInfor) {
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
        $rs = $this->_connection->Execute($sql, array($flag, $id));

        if (!$rs) {
            $connection->ErrorMsg();

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

        $rs = $this->_connection->Execute($sql, $values);
        if (!$rs) {
            $connection->ErrorMsg();

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

        $sql = "DELETE FROM instances WHERE id=?";
        $rs = $this->_connection->Execute($sql, array($instance->id));
        if (!$rs) {
            $this->_connection->ErrorMsg();

            return false;
        }

        $assetFolder = SITE_PATH.DS.'media'.DS.$instance->internal_name;
        $this->deleteDefaultAssetsForInstance($assetFolder);
        $this->deleteDatabaseForInstance($instance->settings);
        $this->deleteInstanceUserFromDatabaseManager($instance->settings);
        $this->deleteInstanceWithInternalName($instance->internal_name);
        $this->deleteApacheConfAndReloadConfiguration($instance->internal_name);

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

            $this->createInstanceReferenceInManager($data);

            $this->createDatabaseForInstance($data);

            $this->copyDefaultAssetsForInstance($data['internal_name']);

            $this->copyApacheAndReloadConfiguration($data);

        } catch (InstanceNotRegisteredException $e) {

            $errors []= $e->getMessage();
            $this->deleteInstanceWithInternalName($data['internal_name']);

        } catch (DatabaseForInstanceNotCreatedException $e) {
            $errors []= $e->getMessage();
            $this->deleteDatabaseForInstance($data);
            $this->deleteInstanceUserFromDatabaseManager($data);
            $this->deleteInstanceWithInternalName($data['internal_name']);

        } catch (DefaultAssetsForInstanceNotCopiedException $e) {

            $errors []= $e->getMessage();
            $assetFolder = SITE_PATH.DS.'media'.DS.$data['internal_name'];
            $this->deleteDefaultAssetsForInstance($assetFolder);
            $this->deleteDatabaseForInstance($data);
            $this->deleteInstanceWithInternalName($data['internal_name']);

        } catch (ApacheConfigurationNotCreatedException $e) {

            $errors []= $e->getMessage();
            $assetFolder = SITE_PATH.DS.'media'.DS.$data['internal_name'];
            $this->deleteDefaultAssetsForInstance($assetFolder);
            $this->deleteDatabaseForInstance($data);
            $this->deleteInstanceWithInternalName($data['internal_name']);
            $this->deleteApacheConfAndReloadConfiguration($data['internal_name']);

        }

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
        if (
            empty($data['name'])
            || empty($data['internal_name'])
            || empty($data['domains'])
            || empty($data['activated'])
            || empty($data['user_mail'])
        ) {
            throw new InstanceNotRegisteredException(
                _("Instance data could not be blank.")
            );
        }

        // Check if the instance already exists
        $sql = "SELECT count(*) as instance_exists FROM instances "
             . "WHERE `internal_name` = ?";
        $rs = $this->_connection->Execute($sql, array($data['internal_name']));

        // Check if the email already exists
        $checkMailSql = "SELECT count(*) as email_exists FROM instances "
              . "WHERE `contact_mail` = ?";
        $checkMailRs = $this->_connection->Execute($checkMailSql, array($data['user_mail']));

        // If doesn´t exist the instance in the database and doesn't exist contact mail proceed
        if ($rs
            && !(bool) $rs->fields['instance_exists']
            && $checkMailRs
            && !(bool) $checkMailRs->fields['email_exists']
        ) {
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

            $createIntanceRs = $this->_connection->Execute($createIntanceSql, $values);
            if (!$createIntanceRs) {
                throw new InstanceNotRegisteredException(
                    "Could not create the instance reference into the instance "
                    ."table: {$this->_connection->ErrorMsg()}"
                );
            }

            return true;

        } elseif (isset ($_POST['timezone'])) {
            // If instance name or contact mail already
            // exists and comes from openhost form
            if ($rs && (bool) $rs->fields['instance_exists']) {
                echo 'instance_exists';
            } elseif ($checkMailRs
                && (bool) $checkMailRs->fields['email_exists']
            ) {
                echo 'mail_exists';
            }

            die();
        } else {
            //If instance name or contact mail already exists and comes from manager
            if ($rs && (bool) $rs->fields['instance_exists']) {
                throw new InstanceNotRegisteredException(
                    _("Instance internal name is already in use.")
                );
            } elseif ( $checkMailRs && (bool) $checkMailRs->fields['email_exists']) {
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

        if (!$this->_connection->Execute($sql, $values)) {
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
            $apacheConfString = file_get_contents($configPath.DS.'vhost.conf');

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
                exec("sudo apachectl graceful", $output, $exitCode);
                if ($exitCode > 0) {
                    //"Unable to reload apache configuration
                    //(exit code {$exitCode}): ".$output);

                    return false;
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
     * Creates and imports default database for the new instance
     *
     * @param $arg
     **/
    public function createDatabaseForInstance($data)
    {
        // Gets global database connection and creates the requested database
        global $onmInstancesConnection;
        $conn = \ADONewConnection($onmInstancesConnection['BD_TYPE']);
        $conn->Connect($onmInstancesConnection['BD_HOST'],
                       $onmInstancesConnection['BD_USER'],
                       $onmInstancesConnection['BD_PASS']);
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

        // If the database was created sucessfully now import the default data.
        if ($rs && $rs2 && $rs3 && $rs4) {
            $connection2         = self::getConnection($data['settings']);
            $exampleDatabasePath = realpath(APPLICATION_PATH.DS.'db'.DS.'instance-default.sql');
            $execLine = "mysql -h {$onmInstancesConnection['BD_HOST']} "
                ."-u {$onmInstancesConnection['BD_USER']}"
                ." -p{$onmInstancesConnection['BD_PASS']} "
                ."{$data['settings']['BD_DATABASE']} < {$exampleDatabasePath}";
            exec($execLine, $output, $exitCode);
            if ($exitCode > 0) {
                throw new DatabaseForInstanceNotCreatedException(
                    'Could not create the default database for the instance:'
                    .' EXEC_LINE: {$execLine} \n OUTPUT: {$output}'
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
                $sql = "INSERT INTO users (`login`, `password`, `sessionexpire`,
                                           `email`, `name`, `fk_user_group`)
                        VALUES (?,?,?,?,?,?)";

                $values = array($data['user_name'], md5($data['user_pass']),  60,
                                $data['user_mail'], $data['user_name'],6);

                if (!$connection2->Execute($sql, $values)) {
                    return false;
                }

                $userPrivSql = "INSERT INTO `users_content_categories` "
                        ."(`pk_fk_user`, `pk_fk_content_category`)"
                        ."VALUES (134, 0), (134, 22), (134, 23), (134, 24), "
                        ."       (134, 25), (134, 26), (134, 27), "
                        ."       (134, 28), (134, 29), (134, 30), (134, 31)";

                if (!$connection2->Execute($userPrivSql)) {
                    return false;
                }

                s::set('contact_mail', $data['user_mail']);
                s::set('contact_name', $data['user_name']);
                s::set('contact_IP', $data['contact_IP']);
            }

            //Change and insert some data with instance information
            s::set('site_name', $data['name']);
            s::set('site_created', $data['site_created']);
            s::set('site_title',
                $data['name'].' - OpenNemas - Servicio online para tu periódico'
                .' digital - Online service for digital newspapers');
            s::set('site_description',
                $data['name'].' - OpenNemas - Servicio online para tu periódico'
                .' digital - Online service for digital newspapers');
            s::set('site_keywords',
                $data['internal_name'].', openNemas, servicio, online, '
                .'periódico, digital, service, newspapers');
            s::set('site_agency', $data['internal_name'].'.opennemas.com');
            if (isset ($data['timezone'])) {
                s::set('time_zone', $data['timezone']);
            }

        } else {
            throw new DatabaseForInstanceNotCreatedException(
                'Could not create the default database/user/grant/privileges'
                .' for the instance/user...'
            );

            return false;
        }

        return true;

    }

    /**
     * Deletes the database given an intance internal_name
     *
     * @return void
     * @author
     **/
    public function deleteDatabaseForInstance($settings)
    {
        $sql = "DROP DATABASE `{$settings['BD_DATABASE']}`";

        if (!$this->_connection->Execute($sql)) {
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
    public function deleteInstanceUserFromDatabaseManager($settings)
    {
        $sql = "DROP USER `{$settings['BD_USER']}`@'localhost'";

        if (!$this->_connection->Execute($sql)) {
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
            return fm::recursiveCopy(SITE_PATH.DS.'media'.DS.'default',
                $mediaPath);
        } else {
            //TODO: return codes for handling this errors
            return "The media folder {$name} already exists.";
        }
    }

    /*
     * Copies the default assets for the new instance given its internal name
     *
     * @param $name the name of the instance
     */
    public function deleteDefaultAssetsForInstance($mediaPath)
    {
        if (!is_dirr($mediaPath)) {
            return false;
        }
        $objects = scandir($mediaPath);
        foreach ($objects as $object) {
            if ($object != "." && $object != "..") {
                if (filetype($mediaPath."/".$object) == "dir") {
                    $this->deleteDefaultAssetsForInstance($mediaPath."/".$object);
                } else {
                    unlink($mediaPath."/".$object);
                }
            }
        }
        reset($objects);

        return rmdir($mediaPath);
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
             . "WHERE `domains` LIKE '".$internalNameShort."%'";
        $rs = $this->_connection->Execute($sql);


        if ($rs && $rs->fields['internalShort_exists'] > 0) {
            $num = $rs->fields['internalShort_exists'];
            $data['settings']['BD_USER'] = $internalNameShort.$num;
            $data['settings']['BD_DATABASE'] = $internalNameShort.$num;
        }

        return $data;
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
class ApacheConfigurationNotReloadedException extends \Exception
{

}
