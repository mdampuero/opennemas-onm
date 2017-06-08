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

use Symfony\Component\Filesystem\Filesystem;

use Onm\Database\DbalWrapper;
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
     * The path to backup directory.
     *
     * @var string
     */
    private $backupPath;

    /**
     * The database connection.
     *
     * @var DbalWrapper
     */
    private $conn;

    /**
     * The filesystem manager.
     *
     * @var Filesystem
     */
    private $fs;

    /**
     * Initializes the database connection.
     *
     * @param DbalWrapper $conn The database connection.
     */
    public function __construct($conn)
    {
        $this->conn = $conn;
        $this->fs   = new Filesystem();
    }

    /**
     * Backup assets data of a particular instance.
     *
     * @param  string $mediaPath  Assets directory
     *
     * @throws BackupException In case of error.
     */
    public function backupAssets($mediaPath)
    {
        $tarFile = $this->getBackupPath() . DS . 'media.tar';

        if (!$this->fs->exists($mediaPath)) {
            return;
        }

        if (!\Onm\Compress\Compress::compressOnlyTar($tarFile, $mediaPath)) {
            throw new BackupException(
                'Could not create a backup of the directory'
            );
        }
    }

    /**
     * Backup database of a particular instance.
     *
     * @param  string $database Database name.
     *
     * @throws BackupException In case of error.
     */
    public function backupDatabase($database)
    {
        $rs = $this->conn->fetchAll("SHOW DATABASES LIKE '$database'");

        if (empty($rs)) {
            return;
        }

        $target = $this->getBackupPath() . '/database.sql';
        $cmd    = "mysqldump -u{$this->conn->user}"
            . " -p{$this->conn->password}"
            . " -h{$this->conn->host}"
            . " --databases $database  > $target";

        exec($cmd, $output, $result);

        if ($result != 0) {
            throw new BackupException("Cannot backup the database $database");
        }
    }

    /**
     * Backup data of a particular instance from the instances table.
     *
     * @param integer $id The id of the instance.
     *
     * @throws BackupException In case of error.
     */
    public function backupInstance($id)
    {
        $target   = $this->getBackupPath() . "instance.sql";
        $database = $this->conn->dbname;

        $cmd = "mysqldump -u{$this->conn->user}"
            . " -p{$this->conn->password}"
            . " -h{$this->conn->host}"
            . ' --no-create-info --where \'id=' . $id . '\' '
            . $database . ' instances > ' . $target;

        exec($cmd, $output, $result);

        if ($result != 0) {
            throw new BackupException("Cannot backup the instance with id $id");
        }
    }

    /**
     * Copies the default assets for the new instance given its internal name.
     *
     * @param string $instance The instance internal name.
     */
    public function copyDefaultAssets($instance)
    {
        $mediaPath   = SITE_PATH . 'media' . DS . $instance;
        $defaultPath = SITE_PATH . 'media' . DS . 'default';

        $this->fs->mirror($defaultPath, $mediaPath);
    }

    /**
     * Creates and imports default database for the new instance.
     *
     * @param array $database The database name.
     *
     * @throws DatabaseNotCreatedException If creation fails.
     */
    public function createDatabase($database)
    {
        // Create instance database
        $sql = "CREATE DATABASE IF NOT EXISTS `$database`";
        $rs  = $this->conn->executeQuery($sql);

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
     * @param string $name The instance internal name.
     */
    public function deleteAssets($name)
    {
        $target = SITE_PATH . DS . 'media' . DS . $name;

        $this->fs->remove($target);
    }

    /**
     * Deletes the backup directory.
     *
     * @param string $path The path to the directory
     */
    public function deleteBackup($path)
    {
        $this->fs->remove($path);
    }

    /**
     * Deletes the database given its name.
     *
     * @param string $database The database name.
     *
     * @throws DatabaseNotDeletedException If the database couldn't be deleted.
     */
    public function deleteDatabase($database)
    {
        if (empty($database)) {
            return;
        }

        $sql = "DROP DATABASE IF EXISTS `$database`";

        if (!$this->conn->executeQuery($sql)) {
            throw new DatabaseNotDeletedException(
                "Could not drop the database $database"
            );
        }
    }

    /**
     * Return the current backup direcotry and creates it if not exists.
     *
     * @return string The backup path.
     */
    public function getBackupPath()
    {
        if (!empty($this->backupPath)
            && !$this->fs->exists($this->backupPath)
        ) {
            $this->fs->mkdir($this->backupPath);
        }

        return $this->backupPath;
    }

    /**
     * Restores the assets for an instance.
     *
     * @param string $path The path where extract the assets.
     *
     * @throws AssetsNotRestoredException If assets not found.
     */
    public function restoreAssets($path)
    {
        $tarFile = $path . DS . "media.tar";

        if (!$this->fs->exists($tarFile)
            || !\Onm\Compress\Compress::decompressOnlyTar($tarFile, "/")
        ) {
            throw new AssetsNotRestoredException(
                "Could not restore the assets directory."
            );
        }
    }

    /**
     * Restores an instance database from a source.
     *
     * @param string $source The path to the source.
     * @param string $target The target database.
     */
    public function restoreDatabase($source, $target = null)
    {
        $cmd = "mysql -u{$this->conn->user}"
            . " -p{$this->conn->password}"
            . " -h{$this->conn->host} "
            . ($target ? " $target"  : '')
            . " < $source";

        exec($cmd, $output, $result);

        if ($result != 0) {
            throw new DatabaseNotRestoredException(
                'Could not import the default database for the instance '
                . print_r($output)
            );
        }
    }

    /**
     * Restores instance reference data to the instances table.
     *
     * @param string $path Backup directory.
     */
    public function restoreInstance($path)
    {
        if (!$this->fs->exists($path)) {
            throw new InstanceNotRestoredException(
                'Could not import the default database for the instance'
            );
        }

        $dump = "mysql -u{$this->conn->user}"
            . " -p{$this->conn->password}"
            . " -h{$this->conn->host}"
            . " {$this->conn->dbname}"
            . " < " . $path . DS . "instance.sql";

        exec($dump, $output, $result);

        if ($result != 0) {
            throw new InstanceNotRestoredException(
                "The instance could not be restored (" . print_r($output) . ")"
            );
        }
    }

    /**
     * Changes the backup path.
     *
     * @param string $path The new backup path.
     */
    public function setBackupPath($path)
    {
        $this->backupPath = rtrim($path, DS) . DS;
    }
}
