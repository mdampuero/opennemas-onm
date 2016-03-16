<?php
/**
 * This file is part of the onm package.
 * (c) Openhost, S.L. <developers@openhost.es>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace Framework\Import\Server\Ftp;

use Framework\Import\Server\Server;

/**
 * Synchronize local folders with an external FTP folder.
 */
class Ftp extends Server
{
    /**
     * Initializes a new FTP server and opens a conn.
     *
     * @param array $params The server parameters.
     *
     * @throws \Exception If the server parameters are not valid.
     */
    public function __construct($params)
    {
        parent::__construct($params);

        $url = parse_url($this->params['url']);

        $this->conn = @ftp_connect($url['host']);

        // Test FTP conn
        if (!$this->conn) {
            throw new \Exception(
                sprintf(
                    _(
                        'Can\'t connect to server %s. Please check your'
                        .' conn details.'
                    ),
                    $this->params['name']
                )
            );
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
                throw new \Exception(
                    sprintf(
                        _('Can\'t login into server %s'),
                        $this->params['server']
                    )
                );
            }

            if (isset($url['path'])) {
                if (!@ftp_chdir($this->conn, $url['path'])) {
                    throw new \Exception(
                        sprintf(
                            _(
                                "Directory '%s' in the server '%s' doesn't exists or "
                                ."you don't have enought permissions to access it"
                            ),
                            $url['path'],
                            $url['host']
                        )
                    );
                }
            }
        }
    }

    /**
     * {@inheritdoc}
     */
    public function checkParameters($params)
    {
        if (array_key_exists('url', $params)
            && preg_match('@ftp://@', $params['url'])
        ) {
            return true;
        }

        return false;
    }

    /**
     * {@inheritdoc}
     */
    public function downloadFiles($files = null)
    {
        if (empty($files)) {
            $files = $this->getRemoteFiles();
        }

        if (!is_writable($this->params['path'])) {
            throw new \Exception(
                sprintf(
                    _('Directory %s is not writable.'),
                    $this->params['path']
                )
            );
        }

        foreach ($this->remoteFiles as $file) {
            $localFile = $this->params['path'] . DS
                . strtolower(basename($file['filename']));

            if (!file_exists($localFile)) {
                ftp_pasv($this->conn, true);
                @ftp_get($this->conn, $localFile, $file['filename'], FTP_BINARY);

                if (preg_match('/\.xml$/', $localFile)) {
                    $this->localFiles[] = $localFile;
                }

                $this->downloaded++;
            }
        }
    }

    /**
     * Gets and returns the list of remote files.
     *
     * @return array The list of remote files.
     */
    public function getRemoteFiles()
    {
        ftp_pasv($this->conn, true);

        $files = ftp_rawlist($this->conn, ftp_pwd($this->conn), true);

        $this->remoteFiles = $this->filterOldFiles(
            $this->formatFtpFileList($files),
            $this->params['sync_from']
        );

        return $this->remoteFiles;
    }

    /**
     * Converts a byte based file size to a human readable string.
     *
     * @param  integer $bytes The filesize in bytes.
     *
     * @return string The human readable filesize.
     */
    protected function byteConvert($bytes)
    {
        $symbol = array('B', 'KB', 'MB', 'GB', 'TB', 'PB', 'EB', 'ZB', 'YB');
        if ($bytes > 0) {
            $exp    = floor(log($bytes)/log(1024));
        } else {
            $exp = 0;
        }

        return sprintf('%.2f ' . $symbol[$exp], $bytes / pow(1024, floor($exp)));
    }

    /**
     * Converts a chmod string to a numeric based file permissions.
     *
     * @param  string  $chmod The chmod string-based file permissions.
     *
     * @return integer The numeric based file permissions.
     */
    protected function chmodNum($chmod)
    {
        $trans = array('-' => '0', 'r' => '4', 'w' => '2', 'x' => '1');
        $chmod = substr(strtr($chmod, $trans), 1);
        $array = str_split($chmod, 3);

        return array_sum(str_split($array[0]))
            . array_sum(str_split($array[1]))
            . array_sum(str_split($array[2]));
    }

    /**
     * Converts a raw file list from a FTP conn to a formatted array list
     *
     * @return array list of files with its properties
     */
    protected function formatFtpFileList($raw = [])
    {
        if (!is_array($raw) || empty($raw)) {
            return [];
        }

        // here the magic begins!
        $structure = array();
        $arraypointer = &$structure;

        foreach ($raw as $rawfile) {
            if ($rawfile[0] == '/') {
                $paths =
                    array_slice(explode('/', str_replace(':', '', $rawfile)), 1);
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
                $info = preg_split("/[\s]+/", $rawfile, 9);
                $arraypointer[] = [
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

        return $structure;
    }
}
