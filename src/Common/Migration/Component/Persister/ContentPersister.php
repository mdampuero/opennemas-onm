<?php
/**
 * This file is part of the Onm package.
 *
 * (c) Openhost, S.L. <developers@opennemas.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace Common\Migration\Component\Persister;

use Common\ORM\Entity\Content;

/**
 * The ContentPersister class provides methods to save a Content from an extenal
 * data source.
 */
class ContentPersister extends Persister
{
    /**
     * {@inheritdoc}
     */
    public function save($data)
    {
        $converter = $this->em->getConverter('Content');
        $content   = new Content($converter->objectify($data));

        $this->em->persist($content);

        return $content->pk_content;
    }
}
