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

use FilesManager as fm;
use Onm\Database\DbalWrapper;
use Onm\Exception\AssetsNotCopiedException;
use Onm\Exception\AssetsNotDeletedException;
use Onm\Exception\AssetsNotRestoredException;
use Onm\Exception\BackupException;
use Onm\Exception\DatabaseNotCreatedException;
use Onm\Exception\DatabaseNotDeletedException;
use Onm\Exception\DatabaseNotRestoredException;
use Onm\Exception\InstanceNotRestoredException;

/**
 * Implements actions to run when creating instances.
 */
class InstanceCreator
{
    /**
     * The database connection.
     *
     * @var DbalWrapper
     */
    private $conn;

    /**
     * Initializes the database connection.
     *
     * @param DbalWrapper $conn The database connection.
     */
    public function __construct(DbalWrapper $conn)
    {
        $this->conn = $conn;
    }

    /**
     * Backup assets data of a particular instance.
     *
     * @param  string $mediaPath  Assets directory
     * @param  string $backupPath Backups directory
     * @return boolean            True if the backup was successful. Otherwise,
     *                            returns false.
     *
     * @throws BackupException In case of error.
     */
    public function backupAssets($mediaPath, $backupPath)
    {
        $tgzFile = $backupPath . DS . "media.tar.gz";

        if (!fm::createDirectory($backupPath)) {
            throw new BackupException(
                "The directory for backup could not be created"
            );
        }

        if (!fm::compressTgz($tgzFile, $mediaPath)) {
            throw new BackupException(
                "Could not create a backup of the directory"
            );
        }
    }

    /**
     * Backup database of a particular instance.
     *
     * @param  string  $user     Database user.
     * @param  string  $password Database password.
     * @param  string  $database Database name.
     * @param  string  $path     Path where place the backup.
     * @return boolean           True if the backup was successful. Otherwise,
     *                           returns false.
     *
     * @throws BackupException In case of error.
     */
    public function backupDatabase($database, $path)
    {
        if (!fm::createDirectory($path)) {
            throw new BackupException(
                "The directory for backup could not be created"
            );
        }

        $dump = "mysqldump -u" . $this->conn->connectionParams['user']
            . " -p" . $this->conn->connectionParams['password']
            . " --databases '$database'  > " . $path . DS . "database.sql";

        exec($dump, $output, $result);

        if ($result != 0) {
            throw new BackupException($dump);
        }
    }

    /**
     * Backup data of a particular instance from the instances table.
     *
     * @param  integer $user     The database user.
     * @param  integer $password The database password.
     * @param  integer $database The database name.
     * @param  integer $id       The id of the instance.
     * @param  string  $path     Backups directory
     *
     * @throws BackupException In case of error.
     */
    public function backupInstance($database, $id, $path)
    {
        if (!fm::createDirectory($path)) {
            throw new BackupException(
                "The directory for backup could not be created"
            );
        }

        $dump = "mysqldump -u" . $this->conn->connectionParams['user']
            . " -p" . $this->conn->connectionParams['password']
            . " --no-create-info --where 'id=" . $id . "' "
            . " onm-instances instances > " . $path . DS . "instance.sql";

        exec($dump, $output, $result);

        if ($result != 0) {
            throw new BackupException($output);
        }
    }

    /**
     * Copies the default assets for the new instance given its internal name.
     *
     * @param  string $name The instance internal name.
     * @return mixed        True if the assets where copied successfully.
     *
     * @throws AssetsNotCopiedException If copy fails.
     */
    public function copyDefaultAssets($name)
    {
        $mediaPath   = SITE_PATH . DS . 'media' . DS . $name;
        $defaultPath = SITE_PATH . DS . 'media' . DS . 'default';

        if (file_exists($mediaPath)) {
            throw new AssetsNotCopiedException(
                "The media folder {$name} already exists."
            );
        }

        if (!fm::recursiveCopy($defaultPath, $mediaPath)) {
            throw new AssetsNotCopiedException(
                "Could not copy default assets for the instance"
            );
        }
    }

    /**
     * Creates and imports default database for the new instance.
     *
     * @param  array   $database The database name.
     * @return boolean           True if the database is created successfully.
     *
     * @throws DatabaseForInstanceNotCreatedException If creation fails.
     */
    public function createDatabase($database)
    {
        // Create instance database
        $sql = "CREATE DATABASE `$database`";
        $rs = $this->conn->executeQuery($sql);

        if (!$rs) {
            throw new DatabaseNotCreatedException(
                'Could not create the default database for the instance'
            );
        }

        // Import default instance database
        $source = realpath(
            APPLICATION_PATH . DS . 'db' . DS . 'instance-default.sql'
        );


        $this->restoreDatabase($source, $database);
    }

    /*
     * Deletes the default assets for the instance given its internal name.
     *
     * @param  string  $name The instance internal name.
     * @return boolean       True it assets were deleted successfully.
     *                       Otherwise, returns false.
     */
    public function deleteAssets($name)
    {
        $target = SITE_PATH . DS . 'media' . DS . $name;
        if (!is_dir($target)) {
            throw new AssetsNotDeletedException(
                "The assets directory for $name doesn't exist"
            );

        }

        if (!fm::deleteDirectoryRecursively($target)) {
            throw new AssetsNotDeletedException(
                "Could not delete assets for instance $name"
            );
        }
    }

    /**
     * Deletes the backup directory.
     *
     * @param string $path The path to the directory
     */
    public function deleteBackup($path)
    {
        fm::deleteDirectoryRecursively($path);
    }

    /**
     * Deletes the database given its name.
     *
     * @param  string  $database The database name.
     * @return boolean           True, if the database is deleted successfully.
     *
     * @throws DatabaseNotDeletedException If the database couldn't be deleted.
     */
    public function deleteDatabase($database)
    {
        $sql = "DROP DATABASE `$database`";

        if (!$this->conn->executeQuery($sql)) {
            throw new DatabaseNotDeletedException(
                "Could not drop the database"
            );
        }
    }

    /**
     * Restores the assets for an instance.
     *
     * @param  string  $path The path where extract the assets.
     * @return boolean       True, if assets were extracted successfully.
     *
     * @throws DefaultAssetsForInstanceNotDeletedException If assets not found.
     */
    public function restoreAssets($path)
    {
        $tgzFile = $path . DS . "media.tar.gz";
        if (!fm::decompressTgz($tgzFile, "/")) {
            throw new AssetsNotRestoredException(
                "Could not restore the assets directory."
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
            throw new DatabaseNotRestoredException(
                'Could not import the default database for the instance'
            );
        }
    }

    /**
     * Restores instance reference data to the instances table.
     *
     * @param  string  $path Backup directory.
     * @return boolean       True, if instance was restored successfully.
     *                       Otherwise, returns false.
     */
    public function restoreInstance($path)
    {
        $dump = "mysql -u". $this->conn->connectionParams['user'] .
                " -p" . $this->conn->connectionParams['password'] .
                " " . $this->conn->connectionParams['dbname'] .
                " < " . $path . DS . "instance.sql";

        exec($dump, $output, $result);

        if ($result!=0) {
            throw new InstanceNotRestoredException(
                "The instance could not be restored"
            );

        }
    }
}
