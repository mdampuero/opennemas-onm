<?php
/**
 * This file is part of the onm package.
 * (c) Openhost, S.L. <developers@openhost.es>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace Common\NewsAgency\Component\Server\Ftp;

use Common\NewsAgency\Component\Server\Server;

/**
 * Synchronize local folders with an external FTP folder.
 */
class Ftp extends Server
{
    /**
     * {@inheritdoc}
     */
    public function checkConnection() : bool
    {
        try {
            $this->connect();

            return true;
        } catch (\Exception $e) {
            return false;
        }
    }

    /**
     * {@inheritdoc}
     */
    public function checkParameters() : bool
    {
        if (array_key_exists('url', $this->params)
            && preg_match('@ftp://@', $this->params['url'])
        ) {
            return true;
        }

        return false;
    }

    /**
     * {@inheritdoc}
     */
    public function downloadFiles(string $path, ?array $files = null) : Server
    {
        if (empty($files)) {
            $files = $this->remoteFiles;
        }

        if (!is_writable($path)) {
            throw new \Exception(
                sprintf(_('Directory %s is not writable.'), $path)
            );
        }

        foreach ($this->remoteFiles as $file) {
            $localFile = $path . '/' . basename($file['filename']);

            if (!preg_match('/index\.xml$/', $localFile) &&
                !file_exists($localFile)
            ) {
                @ftp_pasv($this->conn, true);
                @ftp_get($this->conn, $localFile, $file['filename'], FTP_BINARY);

                if (preg_match('/\.xml$/', $localFile)) {
                    $this->localFiles[] = $localFile;
                }

                $this->downloaded++;
            }
        }

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function getRemoteFiles() : Server
    {
        $this->connect();

        @ftp_pasv($this->conn, true);

        $files = @ftp_rawlist($this->conn, ftp_pwd($this->conn), true);

        if (!$files || $files === false) {
            throw new \Exception(sprintf(
                _('Can\'t get the file list from server %s'),
                $this->params['name']
            ));
        }

        $this->remoteFiles = $this->filterOldFiles(
            $this->formatFtpFileList($files),
            $this->params['sync_from']
        );

        return $this;
    }

    /**
     * Converts a byte based file size to a human readable string.
     *
     * @param int $bytes The filesize in bytes.
     *
     * @return string The human readable filesize.
     */
    protected function byteConvert(int $bytes) : string
    {
        $symbol = ['B', 'KB', 'MB', 'GB', 'TB', 'PB', 'EB', 'ZB', 'YB'];

        if ($bytes > 0) {
            $exp = floor(log($bytes) / log(1024));
        } else {
            $exp = 0;
        }

        return sprintf('%.2f ' . $symbol[$exp], $bytes / pow(1024, floor($exp)));
    }

    /**
     * Converts a chmod string to a numeric based file permissions.
     *
     * @param string $chmod The chmod string-based file permissions.
     *
     * @return int The numeric based file permissions.
     */
    protected function chmodNum(string $chmod) : int
    {
        $trans = ['-' => '0', 'r' => '4', 'w' => '2', 'x' => '1'];
        $chmod = substr(strtr($chmod, $trans), 1);
        $array = str_split($chmod, 3);

        return array_sum(str_split($array[0]))
            . array_sum(str_split($array[1]))
            . array_sum(str_split($array[2]));
    }

    /**
     * Opens a new FTP connection
     *
     * @return Server The current server.
     *
     * @throws \Exception If the server parameters are not valid.
     */
    protected function connect() : Server
    {
        $url = parse_url($this->params['url']);

        $this->conn = @ftp_connect($url['host']);

        // Test FTP conn
        if (!$this->conn) {
            throw new \Exception(sprintf(_(
                'Can\'t connect to server %s. Please check your conn details.'
            ), $this->params['name']));
        }

        ftp_pasv($this->conn, true);

        // if there is a ftp login configuration use it
        if (array_key_exists('username', $this->params)) {
            $logged = @ftp_login(
                $this->conn,
                $this->params['username'],
                $this->params['password']
            );

            if (!$logged) {
                throw new \Exception(sprintf(
                    _('Can\'t login into server %s'),
                    $this->params['name']
                ));
            }

            if (isset($url['path']) && !@ftp_chdir($this->conn, $url['path'])) {
                throw new \Exception(sprintf(_(
                    "Directory '%s' in the server '%s' does not exist or "
                    . "you don't have enough permissions to access it"
                ), $url['path'], $url['host']));
            }
        }

        return $this;
    }

    /**
     * Filters files by its creation time.
     *
     * @param array  $files  The list of files to filter.
     * @param string $maxAge The timestamp of the max age allowed.
     *
     * @return array The list of files.
     */
    protected function filterOldFiles(array $files, string $maxAge) : array
    {
        if (empty($maxAge) || $maxAge == 'no_limits') {
            return $files;
        }

        $files = array_filter(
            $files,
            function ($item) use ($maxAge) {
                if (!($item['date'] instanceof \DateTime) ||
                    $item['filename'] == '..' ||
                    $item['filename'] == '.'
                ) {
                    return false;
                }

                return (time() - $maxAge) < $item['date']->getTimestamp();
            }
        );

        return $files;
    }

    /**
     * Converts a raw file list from a FTP conn to a formatted array list.
     *
     * @param array $raw The list of files to format.
     *
     * @return array list of files with its properties.
     */
    protected function formatFtpFileList(array $raw) : array
    {
        if (!is_array($raw) || empty($raw)) {
            return [];
        }

        // here the magic begins!
        $structure    = [];
        $arraypointer = &$structure;

        $systype = @ftp_systype($this->conn);

        foreach ($raw as $rawfile) {
            if ($rawfile[0] == '/') {
                $paths        = array_slice(explode('/', str_replace(':', '', $rawfile)), 1);
                $arraypointer = &$structure;
                foreach ($paths as $path) {
                    foreach ($arraypointer as $i => $file) {
                        if ($file['text'] == $path) {
                            $arraypointer = &$arraypointer[ $i ]['children'];
                            break;
                        }
                    }
                }
            } elseif (!empty($rawfile)) {
                // Process the line according to the FTP server SO
                if ($systype == 'Windows_NT') {
                    $fileInfo = $this->getFileInfoWindows($rawfile);
                } else {
                    $fileInfo = $this->getFileInfoLinux($rawfile);
                }

                $arraypointer[] = $fileInfo;
            }
        }

        return $structure;
    }

    /**
     * Returns an array of information extracted from the Windows FTP Server
     * raw list element.
     *
     * @param string $rawfile the FTP rawfile info.
     *
     * @return array The properties extracted for the element.
     */
    protected function getFileInfoWindows(string $rawfile) : array
    {
        $lineRegexp = "@([0-9]{2})-([0-9]{2})-([0-9]{2}) "
            . "+([0-9]{2}):([0-9]{2})(AM|PM) +([0-9]+|<DIR>) +(.+)@";

        $matched = preg_match($lineRegexp, $rawfile, $matches);

        $date = $matches[3] . '-' . $matches[1] . '-' . $matches[2] . ' '
            . $matches[4] . ':' . $matches[5];

        return [
            'filename' => $matches[8],
            'isDir'    => ($matches[7] == '<DIR>') == 'd',
            'size'     => $this->byteConvert($matches[4]),
            'chmod'    => 0,
            'date'     => \DateTime::createFromFormat('y-m-d g:i', $date),
            'raw'      => $matches,
            'raw2'     => $rawfile,
        ];
    }

    /**
     * Returns an array of information extracted from the Linux FTP servers
     * raw list element.
     *
     * @param string $rawfile the FTP rawfile info.
     *
     * @return array the properties extracted for the element
     */
    protected function getFileInfoLinux(string $rawfile) : array
    {
        $info = preg_split("/[\s]+/", $rawfile, 9);

        return [
            'filename' => $info[8],
            'isDir'    => $info[0]{0} == 'd',
            'size'     => $this->byteConvert($info[4]),
            'chmod'    => $this->chmodNum($info[0]),
            'date'     => \DateTime::createFromFormat(
                'd M H:i',
                $info[6] . ' ' . $info[5] . ' ' . $info[7]
            ),
            'raw'      => $info,
            'raw2'     => $rawfile,
        ];
    }
}
