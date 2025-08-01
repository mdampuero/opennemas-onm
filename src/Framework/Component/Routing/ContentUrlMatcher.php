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

use Common\Core\Component\Helper\CategoryHelper;
use Common\Core\Component\Helper\ContentHelper;
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
     * The content helper.
     *
     * @var ContentHelper
     */
    protected $contentHelper;

    /**
     * The category helper.
     *
     * @var CategoryHelper
     */
    protected $categoryHelper;

    /**
     * Initializes the ContentUrlMatcher
     *
     * @param EntityManager  $entityManager  The entity manager.
     * @param ContentHelper  $contentHelper  The content helper.
     * @param CategoryHelper $categoryHelper The category helper.
     */
    public function __construct(EntityManager $em, ContentHelper $contentHelper, CategoryHelper $categoryHelper)
    {
        $this->em             = $em;
        $this->contentHelper  = $contentHelper;
        $this->categoryHelper = $categoryHelper;
    }

    /**
     * Clean id and search if exist in content table.
     *
     * @param string $type     The content type name.
     * @param string $dirtyId  The content id with date.
     * @param string $slug     The content slug.
     * @param string $category The content category name.
     *
     * @return null|\Content
     *
     */
    public function matchContentUrl($type, $dirtyId, $slug = null, $category = null)
    {
        if (empty($dirtyId)) {
            return null;
        }

        // Check for valid Id
        preg_match("@(?P<date>\d{14})(?P<id>\d+)@", $dirtyId, $matches);

        // Get real content id and date from url
        if (!array_key_exists('id', $matches)
            || !array_key_exists('date', $matches)
            || !((int) $matches['id'] > 0)
        ) {
            return null;
        }

        $id   = (int) $matches['id'];
        $date = \DateTime::createFromFormat('YmdHis', $matches['date'])->format('Y-m-d H:i:s');

        $content = $this->em->find(\classify($type), $id);

        if (empty($content)) {
            return null;
        }

        $created = $content->created instanceof \DateTime ?
            $content->created->format('Y-m-d H:i:s') :
            $content->created;

        // Check if the content matches the info provided and is ready for publish.
        if (is_object($content)
            && $content->pk_content === $id
            && $created === $date
            && $content->content_type_name === $type
            && (is_null($slug) || (string) $slug === (string) $content->slug)
            && (is_null($category) || $category === $this->categoryHelper->getCategorySlug($content))
            && $this->contentHelper->isReadyForPublish($content)
        ) {
            return $content;
        }

        return null;
    }
}
