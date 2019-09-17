<?php
/**
 * This file is part of the Onm package.
 *
 * (c) Openhost, S.L. <developers@opennemas.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace Common\Core\Component\Helper;

use Symfony\Component\HttpFoundation\File\File;

class NewsstandHelper extends FileHelper
{
    /**
     * Returns the path where the file should be moved.
     *
     * @param File   $file The file to generate path to.
     * @param string $date The date to generate the path from.
     *
     * @return string The path where the file should be moved.
     */
    public function generatePath(File $file, ?string $date = null) : string
    {
        $date = new \Datetime($date);

        return preg_replace('/\/+/', '/', sprintf(
            '%s/%s/%s/%s%s.%s',
            $this->publicDir,
            $this->getPathForFile(),
            $date->format('Y/m/d'),
            $date->format('YmdHis'),
            substr(gettimeofday()['usec'], 0, 5),
            'pdf'
        ));
    }

    /**
     * {@inheritdoc}
     */
    protected function getPathForFile()
    {
        return $this->instance->getNewsstandShortPath();
    }
}
