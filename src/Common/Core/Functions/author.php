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
    return getService('core.helper.author')->getAuthor($item);
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
    return getService('core.helper.author')->getAuthorAvatar($item);
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
    return getService('core.helper.author')->getAuthorBioBody($item);
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
    return getService('core.helper.author')->getAuthorBioSummary($item);
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
    return getService('core.helper.author')->getAuthorId($item);
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
    return getService('core.helper.author')->getAuthorName($item);
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
    return getService('core.helper.author')->getAuthorRssUrl($item);
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
    return getService('core.helper.author')->getAuthorSlug($item);
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
    return getService('core.helper.author')->getAuthorSocialFacebookUrl($item);
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
    return getService('core.helper.author')->getAuthorSocialTwitterUrl($item);
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
    return getService('core.helper.author')->getAuthorUrl($item);
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
    return getService('core.helper.author')->hasAuthor($item);
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
    return getService('core.helper.author')->hasAuthorAvatar($item);
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
    return getService('core.helper.author')->hasAuthorBioBody($item);
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
    return getService('core.helper.author')->hasAuthorBioSummary($item);
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
    return getService('core.helper.author')->hasAuthorRssUrl($item);
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
    return getService('core.helper.author')->hasAuthorSlug($item);
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
    return getService('core.helper.author')->hasAuthorSocialFacebookUrl($item);
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
    return getService('core.helper.author')->hasAuthorSocialTwitterUrl($item);
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
    return getService('core.helper.author')->hasAuthorUrl($item);
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
    return getService('core.helper.author')->isBlog($item);
}
