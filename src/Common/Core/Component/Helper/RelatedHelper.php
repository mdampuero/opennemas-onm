<?php

namespace Common\Core\Component\Helper;

use Common\Core\Component\Helper\ContentHelper;
use Common\Core\Component\Helper\SubscriptionHelper;
use Common\Core\Component\Template\Template;

class RelatedHelper
{
    /**
     * The content helper.
     *
     * @var ContentHelper
     */
    protected $contentHelper;

    /**
     * The subscription helper.
     *
     * @var SubscriptionHelper
     */
    protected $subscriptionHelper;

    /**
     * The frontend template.
     *
     * @var Template
     */
    protected $template;

    /**
     * Initializes the related helper.
     *
     * @param ContentHelper      The content helper.
     * @param SubscriptionHelper The subscription helper.
     * @param Template           The frontend template.
     */
    public function __construct(
        ContentHelper $contentHelper,
        SubscriptionHelper $subscriptionHelper,
        Template $template
    ) {
            $this->contentHelper      = $contentHelper;
            $this->subscriptionHelper = $subscriptionHelper;
            $this->template           = $template;
    }

    /**
     * Returns the list of related contents for the provided item based on the
     * relation type.
     *
     * @param Content $item The item to get related contents for.
     * @param string  $type The relation type.
     *
     * @return array The list of related contents.
     */
    public function getRelated($item, string $type) : array
    {
        if (empty($item->related_contents)) {
            return [];
        }

        $items = array_filter($item->related_contents, function ($a) use ($type) {
            return $a['type'] === $type;
        });

        usort($items, function ($a, $b) {
            return $a['position'] <=> $b['position'];
        });

        if ($item->external) {
            $related = $this->template->getValue('related');

            return array_filter(array_map(function ($a) use ($related) {
                return $related[$a['target_id']];
            }, $items));
        }

        return array_filter(array_map(function ($a) {
            $content = $this->contentHelper->getContent($a['target_id'], $a['content_type_name']);

            return empty($content)
                ? null
                : [
                    'item' => $content,
                    'caption' => $a['caption'],
                    'position' => $a['position']
                ];
        }, $items));
    }

    /**
     * Alias to get_related function to use only for 'related_' types.
     *
     * @param Content $item The item to get related contents for.
     * @param string  $type The type of the related contents (frontpage|inner).
     *
     * @return array The list of related contents.
     */
    public function getRelatedContents($item, string $type) : array
    {
        return $this->getRelated($item, 'related_' . $type);
    }

    /**
     * Checks if the item has related contents in the specified relation.
     *
     * @param Content $item The item to check.
     * @param string  $type The relation type.
     *
     * @return bool True if the content has related contents. False otherwise.
     */
    public function hasRelatedContents($item, string $type) : bool
    {
        $token = $this->template->getValue('o_token');

        return !empty($this->getRelatedContents($item, $type))
            && !$this->subscriptionHelper->isHidden($token, 'related_contents');
    }
}
