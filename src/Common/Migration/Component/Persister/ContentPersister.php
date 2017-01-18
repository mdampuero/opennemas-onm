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
 * The ContentPersister class provides methods to save a Content from an
 * external data source.
 */
class ContentPersister extends Persister
{
    /**
     * {@inheritdoc}
     */
    public function persist($data)
    {
        $converter = $this->em->getConverter('Content');
        $data      = $converter->objectify($data);

        unset($data['pk_content']);

        try {
            $content = $this->find($data);
        } catch (\Exception $e) {
            $content = new Content($data);
            $this->em->persist($content);
        }

        return $content->pk_content;
    }

    /**
     * Search for contents in target data source with the same information ready
     * to import.
     *
     * @param array $data The content information.
     *
     * @return Article The content in target data source.
     */
    protected function find($data)
    {
        $oql = sprintf(
            'title = "%s" and content_type_name = "%s"',
            $data['title'],
            'static_page'
        );

        return $this->em->getRepository('Content')->findOneBy($oql);
    }
}
