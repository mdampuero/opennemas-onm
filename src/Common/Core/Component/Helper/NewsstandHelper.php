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

class NewsstandHelper extends FileHelper
{
    /**
     * Returns the path where the file should be moved.
     *
     * @param \SplFileInfo     $file The file to generate path to.
     * @param DateTime $date The date to generate the path from.
     *
     * @return string The path where the file should be moved.
     */
    public function generatePath(\SplFileInfo $file, \DateTime $date) : string
    {
        return preg_replace('/\/+/', '/', sprintf(
            '%s/%s/%s/%s%s.%s',
            $this->publicDir,
            $this->getPathForFile(),
            $date->format('Y/m/d'),
            $date->format('YmdHis'),
            str_pad(substr(gettimeofday()['usec'], 0, 5), 5, '0'),
            'pdf'
        ));
    }

    /**
     * {@inheritdoc}
     */
    protected function getPathForFile()
    {
        return $this->loader->getInstance()->getNewsstandShortPath();
    }
}
