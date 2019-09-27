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

class AttachmentHelper extends FileHelper
{
    /**
     * {@inheritdoc}
     */
    protected function getPathForFile()
    {
        return $this->instance->getFilesShortPath();
    }
}
