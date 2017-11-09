<?php
/**
 * This file is part of the Onm package.
 *
 * (c) Openhost, S.L. <onm-devs@openhost.es>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace Framework\Component\Routing;

use Repository\EntityManager;

class ContentUrlMatcher
{
    /**
     * The entity manager.
     *
     * @var EntityManager
     */
    protected $em;

    /**
     * Initializes the ContentUrlMatcher
     *
     * @param EntityManager $entityManager The entity manager.
     */
    public function __construct(EntityManager $em)
    {
        $this->em = $em;
    }

    /**
     * Clean id and search if exist in content table.
     *
     * @param string $type     The content type name.
     * @param string $dirtyId  The content id with date.
     * @param string $slug     The content slug.
     * @param string $category The content category name.
     *
     * @return int id in table content or forward to 404
     *
     */
    public function matchContentUrl($type, $dirtyId, $slug = null, $category = null)
    {
        if (empty($dirtyId)) {
            return null;
        }

        // Check for valid Id
        preg_match("@(?P<date>\d{14})(?P<id>\d{6,})@", $dirtyId, $matches);

        // Get real content id and date from url
        $id = $date = 0;
        if (!array_key_exists('id', $matches)
            || !array_key_exists('date', $matches)
            || !((int) $matches['id'] > 0)
        ) {
            return null;
        }

        $id   = (int) $matches['id'];
        $date = \DateTime::createFromFormat('YmdHis', $matches['date'])->format('Y-m-d H:i:s');

        $content = $this->em->find(\classify($type), $id);

        // Check if the content matches the info provided and is ready for publish.
        if (is_object($content)
            && $content->pk_content === $id
            && $content->created === $date
            && $content->content_type_name === $type
            && (is_null($slug) || $slug === $content->slug)
            && (is_null($category) || $category === $content->category_name)
            && $content->isReadyForPublish()
        ) {
            return $content;
        }

        return null;
    }
}
