<?php
/**
 * Returns the author for the provided item.
 *
 * @param mixed $item The item to get author for or directly an author. If not
 *                    provided, the function will try to search the item in the
 *                    template.
 *
 * @return \Common\Model\Entity\User The author if present. Null otherwise.
 */
function get_author($item = null) : ?\Common\Model\Entity\User
{
    $item = $item ?? getService('core.template.frontend')->getValue('item');

    if (empty($item)) {
        return null;
    }

    if (($item instanceof \Content
        || $item instanceof \Common\Model\Entity\Content)
        && !empty($item->fk_author)
    ) {
        try {
            return getService('api.service.author')
                ->getItem($item->fk_author);
        } catch (\Exception $e) {
            return null;
        }
    }

    return $item instanceof \Common\Model\Entity\User
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
 * Returns the author id.
 *
 * @param mixed $item The item to get author. If not
 *                    provided, the function will try to search the item in the
 *                    template.
 *
 * @return int The author avatar id.
 */
function get_author_id($item = null) : ?int
{
    $author = get_author($item);

    return !empty($author) ? $author->id : null;
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
        || $item instanceof \Common\Model\Entity\Content
    ) {
        if (!empty($item->agency)) {
            return $item->agency;
        }

        if (!empty($item->author_name)
            && !$item instanceof \Video
        ) {
            return $item->author_name;
        }
    }

    return null;
}

/**
 * Returns the relative URL to the RSS page of the author for the
 * provided item.
 *
 * @param Content $item The item to get URL for. If not provided, the
 *                      function will try to search the item in the template.
 *
 * @return string The rss URL page to the automatic frontpage of the author.
 */
function get_author_rss_url($item = null) : ?string
{
    $author = get_author($item);

    if (!empty($author)) {
        $routeName   = 'frontend_rss_author';
        $routeParams = [
            'author_slug' => $author->slug,
        ];

        if ($author->inrss) {
            $url = getService('router')->generate($routeName, $routeParams);

            return !empty($url) ? $url : null;
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

    return empty($author) ? null : $author->slug;
}

/**
 * Returns the author twitter url for the provided item.
 *
 * @param Content $item The item to get author facebook url for. If not provided, the
 *                      function will try to search the item in the template.
 *
 * @return string The facebook url if author is present. Null otherwise.
 */
function get_author_social_facebook_url($item = null) : ?string
{
    $author = get_author($item);

    return !empty($author->facebook) ? ("https://www.facebook.com/" . $author->facebook) : null;
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

    if (isset($item->content_type_name) && $item->content_type_name === 'opinion') {
        return !empty($author)
            ? getService('core.helper.url_generator')->generate($author)
            : null;
    }

    if (has_author_slug($author)) {
        $routeName   = 'frontend_author_frontpage';
        $routeParams = [
            'slug' => get_author_slug($author),
        ];

        return getService('router')->generate($routeName, $routeParams);
    }

    return null;
}

/**
 * Checks if there is an author based on a content or author provided as
 * parameter.
 *
 * @param mixed $item The item to check author for or the author. If
 *                    not provided, the function will try to search the item in
 *                    the template.
 *
 * @return bool True if the author exists. False otherwise.
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
 * @param mixed $item The item to check author's long bio for or the author. If
 *                    not provided, the function will try to search the item in
 *                    the template.
 *
 * @return bool True if the author has a long biography defined. False otherwise.
 */
function has_author_bio_body($item = null) : bool
{
    return !empty(get_author_bio_body($item));
}

/**
 * Checks if the author has a bio defined as
 * parameter.
 *
 * @param mixed $item The item to check author's bio for or the author. If
 *                    not provided, the function will try to search the item in
 *                    the template.
 *
 * @return bool True if the author has a short biography defined. False otherwise.
 */
function has_author_bio_summary($item = null) : bool
{
    return !empty(get_author_bio_summary($item));
}

/**
 * Checks if if the author has a rss url defined based on a content or author
 * provided as parameter
 *
 * @param mixed $item The item to get author rss url property. If not
 *                    provided, the function will try to search the item in the
 *                    template.
 *
 * @return int The author avatar id.
 */
function has_author_rss_url($item = null) : bool
{
    $url = get_author_rss_url($item);

    return !empty($url);
}

/**
 * Checks if if the author has a slug defined based on a content or author
 * provided as parameter
 *
 * @param mixed $item The item to get author slug property. If not
 *                    provided, the function will try to search the item in the
 *                    template.
 *
 * @return int The author avatar id.
 */
function has_author_slug($item = null) : bool
{
    $slug = get_author_slug($item);

    return !empty($slug);
}

/**
 * Checks if the author has a facebook account provided as
 * parameter.
 *
 * @param mixed $item The item to check author's facebook account for or the author. If
 *                    not provided, the function will try to search the item in
 *                    the template.
 *
 * @return bool True if the author has a facebook account defined. False otherwise.
 */
function has_author_social_facebook_url($item = null) : bool
{
    return !empty(get_author_social_facebook_url($item));
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

/**
 * Checks if the author has an url based on a content or author provided as
 * parameter.
 *
 * @param mixed $item The item to check author for or the author. If
 *                    not provided, the function will try to search the item in
 *                    the template.
 *
 * @return bool True if the author exists. False otherwise.
 */
function has_author_url($item = null) : bool
{
    return !empty(get_author_url($item));
}

/**
 * Checks if if the author is configured as blogger based on a content or author
 * provided as parameter
 *
 * @param mixed $item The item to get author is_blog property. If not
 *                    provided, the function will try to search the item in the
 *                    template.
 *
 * @return int The author avatar id.
 */
function is_blog($item = null) : bool
{
    $author = get_author($item);

    return !empty($author->is_blog) ? $author->is_blog : false;
}
