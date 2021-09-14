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
        $data['created'] = new \DateTime();

        $data = $this->assignUser($data, [ 'fk_user_last_editor', 'fk_publisher' ]);

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

        $data = $this->assignUser($data, [ 'fk_user_last_editor' ]);

        parent::patchItem($id, $data);
    }

    /**
     * {@inheritdoc}
     */
    public function patchList($ids, $data)
    {
        $data['changed'] = new \DateTime();

        $data = $this->assignUser($data, [ 'fk_user_last_editor' ]);

        return parent::patchList($ids, $data);
    }

    /**
     * {@inheritdoc}
     */
    public function updateItem($id, $data)
    {
        $data['changed'] = new \DateTime();

        $data = $this->assignUser($data, [ 'fk_user_last_editor' ]);

        parent::updateItem($id, $data);
    }

    /**
     * {@inheritdoc}
     */
    public function responsify($item)
    {
        $item = \Onm\StringUtils::convertToUtf8($item);

        return parent::responsify($item);
    }

    /**
     * Assign the user data for content.
     *
     * @param array $data       The content data.
     * @param array $userFields The user data fields to update.
     *
     * @return array The data with the current user on user fields.
     */
    protected function assignUser($data, $userFields = [])
    {
        if ($this->container->get('core.security')->hasPermission('MASTER')) {
            return $data;
        }

        $currentUserId = $this->container->get('core.user')->id ?? null;

        return array_merge($data, array_fill_keys($userFields, $currentUserId));
    }

    /**
     * {@inheritdoc}
     */
    protected function localizeItem($item)
    {
        $keys = [
            'related_contents' => [ 'caption' ]
        ];

        $item = parent::localizeItem($item);

        foreach ($keys as $key => $value) {
            if (!empty($item->{$key})) {
                $item->{$key} = $this->container->get('data.manager.filter')
                    ->set($item->{$key})
                    ->filter('localize', [ 'keys' => $value ])
                    ->get();
            }
        }

        return $item;
    }
}
