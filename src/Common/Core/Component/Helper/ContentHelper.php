<?php

/**
 * This file is part of the Onm package.
 *
 * (c)  OpenHost S.L. <developers@openhost.es>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Common\Core\Component\Helper;

use Api\Exception\GetItemException;
use Symfony\Component\DependencyInjection\Container;

/**
* Perform searches in Database related with one content
*/
class ContentHelper
{
    /**
     * The services container.
     *
     * @var Container
     */
    protected $container;

    /**
     * The content service.
     *
     * @var ContentService
     */
    protected $service;

    /**
     * The frontend template.
     *
     * @var Template
     */
    protected $template;

    /**
     * The entity repository.
     *
     * @var EntityManager
     */
    protected $entityManager;

    /**
     * The cache service.
     *
     * @var Cache
     */
    protected $cache;

    /**
     * The tags service.
     *
     * @var TagService
     */
    protected $tagService;

    /**
     * The subscriptions helper.
     *
     * @var SubscriptionHelper
     */
    protected $subscriptionHelper;

    /**
     * Initializes the ContentHelper.
     *
     * @param Container $container The service container.
     */
    public function __construct($container)
    {
        $this->container          = $container;
        $this->service            = $this->container->get('api.service.content');
        $this->template           = $this->container->get('core.template.frontend');
        $this->entityManager      = $this->container->get('entity_repository');
        $this->cache              = $this->container->get('cache.connection.instance');
        $this->tagService         = $this->container->get('api.service.tag');
        $this->subscriptionHelper = $this->container->get('core.helper.subscription');
        $this->locale             = $this->container->get('core.locale');
    }

    /**
     * Returns the body for the provided item.
     *
     * @param Content $item The item to get property from.
     *
     * @return string The content body.
     */
    public function getBody($item = null) : ?string
    {
        $map = [
            'album' => 'description',
            'poll'  => 'description',
            'video' => 'description'
        ];

        $value = array_key_exists($this->getType($item), $map)
            ? $this->getProperty($item, $map[$this->getType($item)])
            : $this->getProperty($item, 'body');

        return !empty($value) ? $value : null;
    }

    /**
     * Get the proper cache expire date for scheduled contents.
     *
     * @return mixed The expire cache datetime in "Y-m-d H:i:s" format or null.
     */
    public function getCacheExpireDate()
    {
        $oqlStart = sprintf(
            'content_status = 1 and in_litter != 1 and'
            . ' content_type_name != "advertisement" and'
            . ' (starttime !is null and starttime > "%s")'
            . ' order by starttime asc limit 1',
            date('Y-m-d H:i:s')
        );

        $oqlEnd = sprintf(
            'content_status = 1 and in_litter != 1 and'
            . ' content_type_name != "advertisement" and'
            . ' (endtime !is null and endtime > "%s")'
            . ' order by endtime desc limit 1',
            date('Y-m-d H:i:s')
        );

        try {
            $start = $this->service->getItemBy($oqlStart);
        } catch (\Exception $e) {
            $start = null;
        }

        try {
            $end = $this->service->getItemBy($oqlEnd);
        } catch (\Exception $e) {
            $end = null;
        }

        if (empty($start) && empty($end)) {
            return null;
        }

        // Get valid date formated or null
        $starttime = !empty($start) && $start->starttime
            ? $start->starttime->format('Y-m-d H:i:s') : null;
        $endtime   = !empty($end) && $end->endtime
            ? $end->endtime->format('Y-m-d H:i:s') : null;

        return min(array_filter([ $starttime, $endtime ]));
    }

    public function getFirstContentCreatedDate($contentTypeName = 'article')
    {
        $oql = sprintf(
            'content_type_name = "%s" and created !is null and created >="2000-01" and in_litter = 0'
            . ' order by created asc limit 1',
            $contentTypeName
        );

        try {
            $item = $this->service->getItemBy($oql);
            return $item->created;
        } catch (\Exception $e) {
            $logger = $this->container->get('application.log');
            $logger->info('ERROR getFirstContentCreatedDate() ' . $e->getMessage());
            var_dump($e);
            var_dump($e->getMessage());
            die();
        }
    }

    /**
     * Returns the caption for an item.
     *
     * @param mixed $item The item to get caption from.
     *
     * @return string The item caption when the photo is provided as an array (with
     *                the object, the position in the list of related contents of
     *                the same type and the caption).
     */
    public function getCaption($item = null) : ?string
    {
        if (!is_array($item)) {
            return null;
        }

        return array_key_exists('caption', $item)
            ? htmlentities($item['caption'])
            : null;
    }

    /**
     * Returns the content of specified type for the provided item.
     *
     * @param mixed  $item        The item to return or the id of the item to return. If
     *                            not provided, the function will try to search the item in
     *                            the template.
     * @param string $type        Content type used to find the content when an id
     *                            provided as first parameter.
     * @param bool   $unpublished Flag to indicate if the content to get the property from can be unpublished.
     *
     * @return Content The content.
     */
    public function getContent($item = null, $type = null, bool $unpublished = false)
    {
        $item = $item ?? $this->template->getValue('item');

        // Item as a related content (array with item + caption + position)
        if (is_array($item) && array_key_exists('item', $item)) {
            $item = $item['item'];
        }

        if (!is_object($item) && is_numeric($item) && !empty($type)) {
            try {
                $item = $this->entityManager->find($type, $item);
            } catch (GetItemException $e) {
                return null;
            }
        }

        if (!$item instanceof \Common\Model\Entity\Content
            && !$item instanceof \Content
        ) {
            return null;
        }

        if ($unpublished) {
            return $item;
        }

        return $this->isReadyForPublish($item) ? $item : null;
    }

    /**
     * Returns the creation date for the provided item.
     *
     * @param Content $item The item to get property from.
     *
     * @return string The content creation date.
     */
    public function getCreationDate($item = null) : ?\Datetime
    {
        $value = $this->getProperty($item, 'created');

        return is_object($value) ? $value : new \Datetime($value);
    }

    /**
     * Returns the description for the provided item.
     *
     * @param Content $item The item to get property from.
     *
     * @return string The content description.
     */
    public function getDescription($item = null) : ?string
    {
        $value = $this->getProperty($item, 'description');

        return !empty($value) ? htmlentities($value) : null;
    }

    /**
     * Returns the id of an item.
     *
     * @param Content $content The content to get id from.
     *
     * @return int The content id.
     */
    public function getId($item) : ?int
    {
        $item = $this->getContent($item);

        return empty($item) ? null : $item->pk_content;
    }

    /**
     * Returns the pretitle for the provided item.
     *
     * @param Content $item The item to get property from.
     *
     * @return string The content pretitle.
     */
    public function getPretitle($item = null) : ?string
    {
        $value = $this->getProperty($item, 'pretitle');

        return !empty($value) ? htmlentities($value) : null;
    }

    /**
     * Returns a property for the provided item.
     *
     * @param Content $item        The item to get property from.
     * @param string  $name        The property name.
     *
     * @param bool    $unpublished Flag to indicate if the content to get the property from can be unpublished.
     * @return mixed The property value.
     */
    public function getProperty($item, string $name, bool $unpublished = false)
    {
        $item = $this->getContent($item, null, $unpublished);

        if (empty($item)) {
            return null;
        }

        return !empty($item->{$name}) ? $item->{$name} : null;
    }

    /**
     * Returns the publication date for the provided item.
     *
     * @param Content $item The item to get property from.
     *
     * @return string The content publication date.
     */
    public function getPublicationDate($item = null) : ?\Datetime
    {
        $value = $this->getProperty($item, 'starttime') ?? $this->getProperty($item, 'created');

        return is_object($value) ? $value : new \Datetime($value);
    }

    /**
     * Returns the scheduling state.
     *
     * @return string The scheduling state.
     */
    public function getSchedulingState($item)
    {
        if ($this->isScheduled($item)) {
            if ($this->isInTime($item)) {
                return \Content::IN_TIME;
            } elseif ($this->isDued($item)) {
                return \Content::DUED;
            } elseif ($this->isPostponed($item)) {
                return \Content::POSTPONED;
            }
        }

        return \Content::NOT_SCHEDULED;
    }

    /**
     * Returns a list of contents related with a content type and category.
     *
     * @param string $contentTypeName  Content types required.
     * @param string $filter           Advanced SQL filter for contents.
     * @param int    $numberOfElements Number of results.
     *
     * @return array Array with the content properties of each content.
     */
    public function getSuggested($contentTypeName, $categoryId = null, $contentId = null)
    {
        $epp = $this->container->get('core.theme')->getSuggestedEpp();

        if (empty($epp)) {
            return [];
        }

        $cacheId = sprintf('suggested_contents_%s_%d', $contentTypeName, $categoryId);

        if (!empty($contentId)) {
            $cacheId .= '_' . $contentId;
        }

        $items = $this->cache->get($cacheId);

        if (!empty($items)) {
            return $items;
        }

        $criteria = [
            'content_status'    => [ [ 'value' => 1 ] ],
            'in_litter'         => [ [ 'value' => 0 ] ],
            'content_type_name' => [ [ 'value' => $contentTypeName ] ],
            'starttime'         => [
                'union' => 'OR',
                [ 'value' => null, 'operator' => 'IS', 'field' => true ],
                [ 'value' => gmdate('Y-m-d H:i:s'), 'operator' => '<=' ],
            ],
            'endtime'           => [
                'union' => 'OR',
                [ 'value' => null, 'operator' => 'IS', 'field' => true ],
                [ 'value' => gmdate('Y-m-d H:i:s'), 'operator' => '>' ],
            ]
        ];

        if (!empty($contentId)) {
            $criteria['pk_content'] = [
                [ 'value' => [ $contentId ], 'operator' => 'NOT IN' ]
            ];
        }

        if (!empty($categoryId)) {
            $criteria['category_id'] = [ [ 'value' => $categoryId ] ];
        }

        try {
            $items = $this->entityManager->findBy($criteria, [
                'starttime' => 'desc'
            ], $epp, 1);

            $this->cache->set($cacheId, $items, 900);
        } catch (\Exception $e) {
            return [];
        }

        return $items;
    }

    /**
     * Returns the summary for the provided item.
     *
     * @param Content $item The item to get property from.
     *
     * @return string The content summary.
     */
    public function getSummary($item = null) : ?string
    {
        if (in_array(
            $item->content_type_name,
            [ 'article', 'company', 'obituary', 'opinion', 'video', 'poll', 'event' ]
        )) {
            return $this->getProperty($item, 'description');
        }

        $value = $this->getProperty($item, 'summary');

        //TODO: Recover use of htmlentities when possible
        return !empty($value) ? $value : null;
    }

    /**
     * Returns the title for the provided item.
     *
     * @param Content $item The item to get property from.
     *
     * @return string The content title.
     */
    public function getTitle($item = null) : ?string
    {
        $value = $this->getProperty($item, 'title');

        return !empty($value) ? htmlentities($value) : null;
    }

    /**
     * Returns the list of tags for the provided item.
     *
     * @param Content $item The item to get tags from.
     *
     * @return array The list of tags.
     */
    public function getTags($item = null) : array
    {
        $value = $this->getProperty($item, 'tags');

        if (empty($value)) {
            return [];
        }

        try {
            return $this->tagService->getListByIds($value)['items'];
        } catch (\Exception $e) {
            return [];
        }
    }

    /**
     * Returns the internal type or human-readable type for the provided item.
     *
     * @param Content $item        The item to get content type for.
     * @param bool    $readable    True if the instance and item have comments enabled. False
     *                             otherwise.
     * @param bool    $unpublished Flag to indicate if the content to get the property from can be unpublished.
     * @param string The internal or human-readable type.
     */
    public function getType($item = null, bool $readable = false, bool $unpublished = false) : ?string
    {
        $value = $this->getProperty($item, 'content_type_name', $unpublished);

        return !empty($value)
            ? !$readable ? $value : _(ucfirst(implode(' ', explode('_', $value))))
            : null;
    }

    /**
     * Check if the content has a body.
     *
     * @param Content $item The item to check body for.
     *
     * @return bool True if the content has a body. False otherwise.
     */
    public function hasBody($item = null) : bool
    {
        $token = $this->template->getValue('o_token');

        return !empty($this->getBody($item))
            && !$this->subscriptionHelper->isHidden($token, 'body');
    }

    /**
     * Checks if the item has a caption.
     *
     * @param mixed $item The item to get caption from.
     *
     * @return bool True if the item is provided as an array (with the object, the
     *              position in the list of related contents of the same type and
     *              the caption) and the caption is not empty.
     */
    public function hasCaption($item = null) : bool
    {
        return !empty($this->getCaption($item));
    }

    /**
     * Checks if the content has comments enabled or not.
     *
     * @param Content $item The item to get if comments are enabled.
     *
     * @return bool True if enabled, false otherwise.
     */
    public function hasCommentsEnabled($item = null) : bool
    {
        return !empty($this->getProperty($item, 'with_comment'));
    }

    /**
     * Check if the content has a description.
     *
     * @param Content $item The item to check description for.
     *
     * @return bool True if the content has a description. False otherwise.
     */
    public function hasDescription($item) : bool
    {
        return !empty($this->getDescription($item));
    }

    /**
     * Check if the content has a pretitle.
     *
     * @param Content $item The item to check pretitle for.
     *
     * @return bool True if the content has a pretitle. False otherwise.
     */
    public function hasPretitle($item) : bool
    {
        $token = $this->template->getValue('o_token');

        return !empty($this->getPretitle($item))
            && !$this->subscriptionHelper->isHidden($token, 'pretitle');
    }

    /**
     * Checks if the content has a summary.
     *
     * @param Content $item The item to check summary for.
     *
     * @return bool True if the content has a summary. False otherwise.
     */
    public function hasSummary($item) : bool
    {
        $token = $this->template->getValue('o_token');

        return !empty($this->getSummary($item))
            && !$this->subscriptionHelper->isHidden($token, 'summary');
    }

    /**
     * Checks if the content has tags.
     *
     * @param Content $item The item to check tags for.
     *
     * @return bool True if the content has tags. False otherwise.
     */
    public function hasTags($item = null) : bool
    {
        return !empty($this->getTags($item));
    }

    /**
     * Checks if the content has a title.
     *
     * @param Content $item The item to check title for.
     *
     * @return bool True if the content has a title. False otherwise.
     */
    public function hasTitle($item) : bool
    {
        $token = $this->template->getValue('o_token');

        return !empty($this->getTitle($item))
            && !$this->subscriptionHelper->isHidden($token, 'title');
    }

    /**
     * Check if a content is in time for publishing
     *
     * @return boolean
     */
    public function isInTime($item)
    {
        $timezone  = $this->locale->getTimeZone();
        $now       = new \DateTime(null, $timezone);
        $starttime = !$item->starttime instanceof \DateTime ?
            new \DateTime($item->starttime, $timezone) :
            $item->starttime;
        $endtime   = !$item->endtime instanceof \DateTime ?
            new \DateTime($item->endtime, $timezone) :
            $item->endtime;

        $dued = (
            !empty($item->endtime)
            && $now->getTimestamp() > $endtime->getTimestamp()
        );

        $postponed = (
            !empty($item->starttime)
            && $now->getTimestamp() < $starttime->getTimestamp()
        );

        return (!$dued && !$postponed);
    }

    /**
     * Returns true if a match time constraints, is available and is not in trash
     *
     * @return boolean true if is ready
     */
    public function isReadyForPublish($item)
    {
        return ($this->isInTime($item)
            && $item->content_status == 1
            && $item->in_litter == 0);
    }

    /**
     * Returns true if the content is suggested
     *
     * @return boolean true if the content is suggested
     */
    public function isSuggested($item)
    {
        return ($item->frontpage == 1);
    }

    /**
     * Check if this content is dued
     *       End      Now
     * -------]--------|-----------
     *
     * @return bool
     */
    protected function isDued($item)
    {
        if (empty($item->endtime)) {
            return false;
        }

        $timezone = $this->locale->getTimeZone();

        $end = !$item->endtime instanceof \DateTime ?
            new \DateTime($item->endtime, $timezone) :
            $item->endtime;
        $now = new \DateTime(null, $timezone);

        return $now->getTimeStamp() > $end->getTimeStamp();
    }

    /**
     * Check if this content is postponed
     *
     *       Now     Start
     * -------|--------[-----------
     *
     * @return bool
     */
    protected function isPostponed($item)
    {
        if (empty($item->starttime)) {
            return false;
        }

        $timezone = $this->locale->getTimeZone();

        $start = !$item->starttime instanceof \DateTime ?
            new \DateTime($item->starttime, $timezone) :
            $item->starttime;
        $now   = new \DateTime(null, $timezone);

        return $now->getTimeStamp() < $start->getTimeStamp();
    }

    /**
     * Check if this content is scheduled or, in others words, if this
     * content has a starttime and/or endtime defined.
     *
     * @return bool
     */
    protected function isScheduled($item)
    {
        if (empty($item->starttime)) {
            return false;
        }

        $start = !$item->starttime instanceof \Datetime ?
            new \DateTime($item->starttime) :
            $item->starttime;
        $end   = !$item->endtime instanceof \DateTime ?
            new \DateTime($item->endtime) :
            $item->endtime;

        if ($start->getTimeStamp() - $end->getTimeStamp() == 0) {
            return false;
        }

        return true;
    }

     /**
     * Check if this content have live blog flag enabled
     *
     * @return bool
     */
    public function isLiveBlog($item)
    {
        return !empty($item->live_blog_posting) &&
            !empty($item->coverage_start_time) &&
            !empty($item->coverage_end_time) &&
            !empty($item->live_blog_updates);
    }

     /**
     * Check if this content have live blog updates
     *
     * @return bool
     */
    public function hasLiveUpdates($item)
    {
        return !empty($item->live_blog_updates);
    }

     /**
     * Return last live update date if live blog article
     *
     * @return string
     */
    public function getLastLiveUpdate($item)
    {
        if (!$this->isLiveBlog($item) || empty($item->live_blog_updates)) {
            return null;
        }

        return $item->live_blog_updates[0]['modified'];
    }

    /**
     * Check if this content is live or, in others words, if this
     * content is between coverage start time and end time
     *
     * @return bool
     */
    public function isLive($item)
    {
        if (empty($item->coverage_start_time || $item->coverage_end_time)) {
            return false;
        }

        $timezone  = $this->locale->getTimeZone();
        $startTime = (gettype($item->coverage_start_time) == 'object') ?
            $item->coverage_start_time :
            new \DateTime($item->coverage_start_time);

        $endTime = (gettype($item->coverage_end_time) == 'object') ?
            $item->coverage_end_time :
            new \DateTime($item->coverage_end_time);

        $now = new \DateTime(null, $timezone);

        return $now->getTimeStamp() >= $startTime->getTimeStamp() && $now->getTimeStamp() <= $endTime->getTimeStamp();
    }
}
