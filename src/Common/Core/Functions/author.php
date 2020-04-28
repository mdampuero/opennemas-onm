<?php
/**
 * Returns the author for the provided item.
 *
 * @param mixed $item The item to get author for or directly an author. If not
 *                    provided, the function will try to search the item in the
 *                    template.
 *
 * @return \Common\ORM\Entity\User The author if present. Null otherwise.
 */
function get_author($item = null) : ?\Common\ORM\Entity\User
{
    $item = $item ?? getService('core.template.frontend')->getValue('item');

    if (empty($item)) {
        return null;
    }

    if (($item instanceof \Content
        || $item instanceof \Common\ORM\Entity\Content)
        && !empty($item->fk_author)
    ) {
        try {
            return getService('api.service.author')
                ->getItem($item->fk_author);
        } catch (\Exception $e) {
            return null;
        }
    }

    return $item instanceof \Common\ORM\Entity\User
        ? $item
        : null;
}

/**
 * Returns the id for the author avatar.
 *
 * @param mixed $item The item to get author avatar for or an author. If not
 *                    provided, the function will try to search the item in the
 *                    template.
 *
 * @return int The author avatar id.
 */
function get_author_avatar($item = null) : ?int
{
    $author = get_author($item);

    return !empty($author) ? $author->avatar_img_id : null;
}

/**
 * Returns the author title for the provided item.
 *
 * @param Content $item The item to get author name for. If not provided, the
 *                      function will try to search the item in the template.
 *
 * @return string The author name if author present or item agency if item is a
 *                content. Null otherwise.
 */
function get_author_name($item = null) : ?string
{
    $author = get_author($item);

    if (!empty($author)) {
        return $author->name;
    }

    $item = $item ?? getService('core.template.frontend')->getValue('item');

    if ($item instanceof \Content
        || $item instanceof \Common\ORM\Entity\Content
    ) {
        if (!empty($item->agency)) {
            return $item->agency;
        }

        if (!empty($item->author_name)) {
            return $item->author_name;
        }
    }

    return null;
}

/**
 * Returns the author slug for the provided item.
 *
 * @param Content $item The item to get author slug for. If not provided, the
 *                      function will try to search the item in the template.
 *
 * @return string The author slug if author is present. Null otherwise.
 */
function get_author_slug($item = null) : ?string
{
    $author = get_author($item);

    return empty($author) ? null : getService('data.manager.filter')
        ->set($author->name)
        ->filter('slug')
        ->get();
}

/**
 * Returns the short author bio for the provided item.
 *
 * @param Content $item The item to get author slug for. If not provided, the
 *                      function will try to search the item in the template.
 *
 * @return string The author bio if author is present. Null otherwise.
 */
function get_author_bio_summary($item = null) : ?string
{
    $author = get_author($item);

    return !empty($author->bio) ? $author->bio : null;
}

/**
 * Returns the long author biography for the provided item.
 *
 * @param Content $item The item to get author biography for. If not provided, the
 *                      function will try to search the item in the template.
 *
 * @return string The author bio if author is present. Null otherwise.
 */
function get_author_bio_body($item = null) : ?string
{
    $author = get_author($item);

    return !empty($author->bio_description) ? $author->bio_description : null;
}

/**
 * Returns the author twitter url for the provided item.
 *
 * @param Content $item The item to get author twitter url for. If not provided, the
 *                      function will try to search the item in the template.
 *
 * @return string The twitter url if author is present. Null otherwise.
 */
function get_author_social_twitter_url($item = null) : ?string
{
    $author = get_author($item);

    return !empty($author->twitter) ? ("https://www.twitter.com/" . $author->twitter) : null;
}

/**
 * Returns the relative URL to the automatic frontpage of the author for the
 * provided item.
 *
 * @param Content $item The item to get URL for. If not provided, the
 *                      function will try to search the item in the template.
 *
 * @return string The relative URL to the automatic frontpage of the author.
 */
function get_author_url($item = null) : ?string
{
    $author = get_author($item);

    return !empty($author)
        ? getService('core.helper.url_generator')->generate($author)
        : null;
}

/**
 * Checks if there is an author based on a content or author provided as
 * parameter.
 *
 * @param mixed $item The item to check author for or the author. If
 *                    not provided, the function will try to search the item in
 *                    the template.
 *
 * @return bool True if the author has an avatar. False otherwise.
 */
function has_author($item = null) : bool
{
    return !empty(get_author_name($item));
}

/**
 * Checks if the author has an avatar based on a content or author provided as
 * parameter.
 *
 * @param mixed $item The item to check author's avatar for or the author. If
 *                    not provided, the function will try to search the item in
 *                    the template.
 *
 * @return bool True if the author has an avatar. False otherwise.
 */
function has_author_avatar($item = null) : bool
{
    return !empty(get_author_avatar($item));
}


/**
 * Checks if the author has a bio defined as
 * parameter.
 *
 * @param mixed $item The item to check author's bio for or the author. If
 *                    not provided, the function will try to search the item in
 *                    the template.
 *
 * @return bool True if the author has a. False otherwise.
 */
function has_author_bio_summary($item = null) : bool
{
    return !empty(get_author_bio_summary($item));
}


/**
 * Checks if the author has a twitter account provided as
 * parameter.
 *
 * @param mixed $item The item to check author's twitter account for or the author. If
 *                    not provided, the function will try to search the item in
 *                    the template.
 *
 * @return bool True if the author has a twitter account defined. False otherwise.
 */
function has_author_social_twitter_url($item = null) : bool
{
    return !empty(get_author_social_twitter_url($item));
}
