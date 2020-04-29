<?php
/**
 * This file is part of the Onm package.
 *
 * (c) Openhost, S.L. <developers@opennemas.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace Api\Service\V1;

class ContentService extends OrmService
{
    /**
     * {@inheritdoc}
     */
    public function createItem($data)
    {
        $data['changed'] = new \DateTime();

        return parent::createItem($data);
    }

    /**
     * Returns a content basing on a slug.
     *
     * @param string $slug The category slug.
     *
     * @return Content The content.
     */
    public function getItemBySlug($slug)
    {
        $oql = sprintf('slug regexp "(.+\"|^)%s(\".+|$)"', $slug);

        return $this->getItemBy($oql);
    }

    /**
     * Returns a content basing on a slug and content type.
     *
     * @param string $slug        The category slug.
     * @param string $contentType The id of the content type.
     *
     * @return Content The content.
     */
    public function getItemBySlugAndContentType($slug, $contentType)
    {
        $oql = sprintf('slug regexp "(.+\"|^)%s(\".+|$)" and fk_content_type=%d', $slug, $contentType);

        return $this->getItemBy($oql);
    }

    /**
     * {@inheritdoc}
     */
    public function patchItem($id, $data)
    {
        $data['changed'] = new \DateTime();

        parent::patchItem($id, $data);
    }

    /**
     * {@inheritdoc}
     */
    public function patchList($ids, $data)
    {
        $data['changed'] = new \DateTime();

        return parent::patchList($ids, $data);
    }

    /**
     * {@inheritdoc}
     */
    public function updateItem($id, $data)
    {
        $data['changed'] = new \DateTime();

        parent::updateItem($id, $data);
    }
}
