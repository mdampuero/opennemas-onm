<?php

namespace Common\Core\Component\Helper;

/**
 * Helper class to retrieve tag data.
 */
class TagHelper
{
    /**
     * Returns the name of the tag for the provided item, prioritizing the header if specified.
     *
     * @param Content $item The item for which to retrieve the tag name. If not provided,
     *                      the function will attempt to find the item in the template.
     * @param bool $displayHeader1 If true, the method will return the tag's header_1
     *                             if it exists; otherwise, it returns the tag's title.
     *
     * @return ?string The tag's header_1 or title if available, or null if the tag is not found.
     */
    public function getTagName($item = null, $displayHeader1 = false): ?string
    {
        return !empty($item)
            ? ($displayHeader1 && !empty($item->header_1) ? $item->header_1 : $item->name)
            : null;
    }
}
