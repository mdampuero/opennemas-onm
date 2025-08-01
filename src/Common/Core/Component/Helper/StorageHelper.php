<?php

namespace Common\Core\Component\Helper;

use League\Flysystem\Filesystem;
use Psr\Log\LoggerInterface;

class StorageHelper
{
    private $filesystem;
    private $logApp;
    private $logError;

    public function __construct(Filesystem $filesystem, LoggerInterface $logApp, LoggerInterface $logError)
    {
        $this->filesystem = $filesystem;
        $this->logApp     = $logApp;
        $this->logError   = $logError;
    }

    /**
     * Uploads content to a specific path in the storage system.
     *
     * @param string $path     The destination path for the file.
     * @param string $contents The file contents to upload.
     * @param array  $params   Optional parameters such as visibility.
     * @return bool True on success, false on failure.
     */
    public function upload(string $path, string $contents, array $params = []): bool
    {
        try {
            $result = $this->filesystem->put($path, $contents, $params);
            $this->logApp->info("File uploaded to {$path} successfully.");
            return $result;
        } catch (\Throwable $e) {
            $this->logError->error("Error uploading file to {$path}: " . $e->getMessage());
            return false;
        }
    }

    /**
     * Deletes a file at the given path.
     *
     * @param string $path The path of the file to delete.
     * @return bool True if deletion was successful, false otherwise.
     */
    public function delete(string $path): bool
    {
        try {
            $result = $this->filesystem->delete($path);
            $this->logApp->info("File deleted {$path} successfully.");
            return $result;
        } catch (\Throwable $e) {
            $this->logError->error("Error deleting file {$path}: " . $e->getMessage());
            return false;
        }
    }

    /**
     * Checks if a file exists at the given path.
     *
     * @param string $path The path of the file to check.
     * @return bool True if the file exists, false otherwise.
     */
    public function exists(string $path): bool
    {
        try {
            if (method_exists($this->filesystem, 'fileExists')) {
                $result = $this->filesystem->fileExists($path);
                $this->logApp->info("File exists {$path} successfully.");
                return $result;
            }
            $result = $this->filesystem->has($path);
            $this->logApp->info("File exists {$path} successfully.");
            return $result;
        } catch (\Throwable $e) {
            $this->logError->error("Error checking existence of file {$path}: " . $e->getMessage());
            return false;
        }
    }

    /**
     * Reads the contents of a file at the given path.
     *
     * @param string $path The path of the file to read.
     * @return string|null The file contents, or null on failure.
     */
    public function read(string $path): ?string
    {
        try {
            $result = $this->filesystem->read($path);
            $this->logApp->info("File readed {$path} successfully.");
            return $result;
        } catch (\Throwable $e) {
            $this->logError->error("Error reading file {$path}: " . $e->getMessage());
            return null;
        }
    }

    /**
     * Replaces an existing file with new contents.
     * Deletes the existing file if it exists before writing the new contents.
     *
     * @param string $path     The path of the file to replace.
     * @param string $contents The new contents to write.
     * @return bool True on success, false on failure.
     */
    public function replace(string $path, string $contents): bool
    {
        try {
            if ($this->exists($path)) {
                $this->filesystem->delete($path);
                $this->logApp->info("File {$path} deleted successfully.");
            }

            $result = $this->filesystem->put($path, $contents);
            $this->logApp->info("File {$path} put successfully.");
            return $result;
        } catch (\Throwable $e) {
            $this->logError->error("Error replacing file {$path}: " . $e->getMessage());
            return false;
        }
    }

    /**
     * Lists the contents of a directory.
     *
     * @param string $directory The directory path to list.
     * @param bool   $recursive Whether to list contents recursively.
     * @return array An array of directory contents.
     */
    public function listContents(string $directory = '', bool $recursive = false): array
    {
        try {
            if (method_exists($this->filesystem, 'listContents')) {
                return iterator_to_array($this->filesystem->listContents($directory, $recursive));
            }

            // Flysystem v1 fallback (no recursion)
            return $this->filesystem->listContents($directory);
        } catch (\Throwable $e) {
            $this->logError->error("Error listing contents of directory {$directory}: " . $e->getMessage());
            return [];
        }
    }
}
