<?php

namespace Common\Core\Component\Helper;

use Common\Core\Component\Template\Template;
use Common\Model\Entity\Content;
use Opennemas\Data\Filter\FilterManager;

/**
 * Helper class to retrieve video data.
 */
class VideoHelper
{
    /**
     * The content helper.
     *
     * @var ContentHelper
     */
    protected $contentHelper;

    /**
     * The related helper.
     *
     * @var RelatedHelper
     */
    protected $relatedHelper;

    /**
     * The data filter.
     *
     * @var FilterManager
     */
    protected $filter;

    /**
     * The admin template.
     *
     * @var Template
     */
    protected $template;

    /**
     * Initializes the video helper
     *
     * @param ContentHelper $contentHelper The content helper.
     * @param RelatedHelper $relatedHelper The related helper.
     * @param Template      $template      The admin template.
     * @param FilterManager $filter        The data filter.
     */
    public function __construct(
        ContentHelper $contentHelper,
        RelatedHelper $relatedHelper,
        Template $template,
        FilterManager $filter
    ) {
        $this->contentHelper = $contentHelper;
        $this->relatedHelper = $relatedHelper;
        $this->filter        = $filter;
        $this->template      = $template;
    }

    /**
     * Returns the video embed html.
     *
     * @param Content $item The item to get embed html for.
     *
     * @return string The embed html.
     */
    public function getVideoEmbedHtml($item)
    {
        $information = $this->contentHelper->getProperty($item, 'information');

        return !empty($information['embedHTML']) ? $information['embedHTML'] : null;
    }

    /**
     * Returns the video embed url.
     *
     * @param Content $item The item to get embed url for.
     *
     * @return string The embed url.
     */
    public function getVideoEmbedUrl($item)
    {
        $information = $this->contentHelper->getProperty($item, 'information');

        return !empty($information['embedUrl']) ? $information['embedUrl'] : null;
    }

    /**
     * Returns the video html.
     *
     * @param Content $item The item to get html for.
     *
     * @return string The html code.
     */
    public function getVideoHtml($item, $width = null, $height = null, $amp = false)
    {
        $width  = $width ?? '560';
        $height = $height ?? '320';
        $output = '';

        if ($item->type === 'script') {
            $output = sprintf('<div>%s</div>', $item->body);
        } else {
            $tpl = !empty($item->information['source'])
                ? 'video/render/external.tpl'
                : 'video/render/web-source.tpl';

            $output = $this->template->fetch($tpl, [
                'info' => $item->information,
                'height' => $height,
                'width' => $width
            ]);
        }

        if ($amp) {
            $output = $this->filter->set($output)->filter('amp')->get();
        }

        return $output;
    }

    /**
     * Returns the type for the provided item.
     *
     * @param Content $item The item to get type for.
     *
     * @return string The content type.
     */
    public function getVideoType($item)
    {
        $value = $this->contentHelper->getProperty($item, 'type');

        return !empty($value) ? $value : null;
    }

    /**
     * Returns the path for the provided item.
     *
     * @param Content $item The item to get path for.
     *
     * @return string The content path.
     */
    public function getVideoPath($item)
    {
        $value = $this->contentHelper->getProperty($item, 'path');

        return !empty($value) ? $value : null;
    }

    /**
     * Returns the thumbnail for the provided item.
     *
     * @param Content $item The item to get thumbnail from.
     *
     * @return mixed The thumbnail id or the thumbnail string.
     */
    public function getVideoThumbnail($item)
    {
        $related = $this->relatedHelper->getRelated($item, 'featured_frontpage');

        if (!empty($related)) {
            $thumbnail = array_shift($related);
            return $this->contentHelper->getContent($thumbnail);
        }

        if (empty($item->information)
            || !array_key_exists('thumbnail', $item->information)
        ) {
            return null;
        }

        return new Content([
            'content_status'    => 1,
            'content_type_name' => 'photo',
            'description'       => $item->title,
            'external_uri'      => $item->information['thumbnail']
        ]);
    }

    /**
     * Returns if the video has embed html or not.
     *
     * @param Content $item The item to check if has embed html or not.
     *
     * @return boolean true if has embed html.
     */
    public function hasVideoEmbedHtml($item)
    {
        $value = $this->getVideoEmbedHtml($item);

        return !empty($value);
    }

    /**
     * Returns if the video has embed url or not.
     *
     * @param Content $item The item to check if has embed url or not.
     *
     * @return boolean true if has embed url.
     */
    public function hasVideoEmbedUrl($item)
    {
        $value = $this->getVideoEmbedUrl($item);

        return !empty($value);
    }

    /**
     * Returns if the video has path or not.
     *
     * @param Content $item The item to check if has path or not.
     *
     * @return boolean true if has path.
     */
    public function hasVideoPath($item)
    {
        $value = $this->getVideoPath($item);

        return !empty($value);
    }
}
