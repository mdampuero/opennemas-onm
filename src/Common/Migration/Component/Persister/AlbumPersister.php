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

use Common\ORM\Entity\Album;

/**
 * The AlbumPersister class provides methods to save a Album from an extenal
 * data source.
 */
class AlbumPersister extends Persister
{
    /**
     * {@inheritdoc}
     */
    public function persist($data)
    {
        unset($data['pk_album']);

        try {
            $album = $this->find($data);
        } catch (\Exception $e) {
            $album = new \Album();
            $album->create($data);
        }

        return $album->pk_content;
    }

    /**
     * Search for categories in target data source with the same information
     * ready to import.
     *
     * @param array $data The album information.
     *
     * @return Album The album in target data source.
     */
    protected function find($data)
    {
        $oql = sprintf(
            'title = "%s" or description = "%s" and content_type_name = "%s"',
            $data['title'],
            $data['description'],
            'album'
        );

        return $this->em->getRepository('Content')->findOneBy($oql);
    }
}
