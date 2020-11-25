<?php
/**
 * Returns the category for the provided item.
 *
 * @param Content $item The item to get category for. If not provided, the
 *                      function will try to search the item in the template.
 *
 * @return \Common\Model\Entity\Category The category.
 */
function get_category($item = null) : ?\Common\Model\Entity\Category
{
    $item = $item ?? getService('core.template.frontend')->getValue('item');

    if (empty($item)) {
        return null;
    }

    if (($item instanceof \Content && !empty($item->category_id))
        || ($item instanceof \Common\Model\Entity\Content && !empty($item->categories))) {
        try {
            $category = $item instanceof \Content ?
            getService('api.service.category')
                ->getItem($item->category_id) :
            getService('api.service.category')
                ->getItem($item->categories[0]);

            return $category;
        } catch (\Exception $e) {
            return null;
        }
    }

    return $item instanceof \Common\Model\Entity\Category
        ? $item
        : null;
}

/**
 * Returns the category color for the provided item.
 *
 * @param Content $item The item to get category color for.
 *                      If not provided, the function will try to search
 *                      the item in the template.
 *
 * @return ?string The category color if present. Null otherwise.
 */
function get_category_color($item = null) : ?string
{
    $category = get_category($item);

    return !empty($category) ? $category->color : null;
}


/**
 * Returns the category description for the provided item.
 *
 * @param Content $item The item to get category description for. If not
 *                      provided, the function will try to search the item in
 *                      the template.
 *
 * @return ?string The category id if present. Null otherwise.
 */
function get_category_description($item = null) : ?string
{
    $category = get_category($item);

    return !empty($category) ? $category->description : null;
}

/**
 * Returns the category id for the provided item.
 *
 * @param Content $item The item to get category id for. If not provided, the
 *                      function will try to search the item in the template.
 *
 * @return ?int The category id if present. Null otherwise.
 */
function get_category_id($item = null) : ?int
{
    $category = get_category($item);

    return !empty($category) ? $category->id : null;
}

/**
 * Returns the path to category logo for the provided item.
 *
 * @param Content $item The item to get logo path for. If not provided, the
 *                      function will try to search the item in the template.
 *
 * @return ?string The path to category logo. Null otherwise.
 */
function get_category_logo($item = null) : ?string
{
    $category = get_category($item);

    return empty($category) || empty($category->logo_path)
        ? null
        : getService('core.instance')->getMediaShortPath()
            . '/' . $category->logo_path;
}

/**
 * Returns the category name for the provided item.
 *
 * @param Content $item The item to get category name for. If not provided, the
 *                      function will try to search the item in the template.
 *
 * @return ?string The category name if present. Null otherwise.
 */
function get_category_name($item = null) : ?string
{
    $category = get_category($item);

    return !empty($category) ? $category->title : null;
}

/**
 * Returns the category slug for the provided item.
 *
 * @param Content $item The item to get category slug for. If not provided, the
 *                      function will try to search the item in the template.
 *
 * @return ?string The category slug. Null otherwise.
 */
function get_category_slug($item = null) : ?string
{
    $category = get_category($item);

    return !empty($category) ? $category->name : null;
}

/**
 * Returns the relative URL to the automatic frontpage of the category for the
 * provided item.
 *
 * @param Content $item The item to get URL for. If not provided, the
 *                      function will try to search the item in the template.
 *
 * @return ?string The relative URL to the automatic frontpage of the category
 *                 if the category is present. Null otherwise.
 */
function get_category_url($item = null) : ?string
{
    $category = get_category($item);

    return !empty($category)
        ? getService('core.helper.url_generator')->generate($category)
        : null;
}

/**
 * Checks if the category has a description.
 *
 * @param Content $item The item to check category description for. If not
 *                      provided, the function will try to search the item in
 *                      the template.
 *
 * @return bool True if the category has a logo. False otherwise.
 */
function has_category_description($item = null) : bool
{
    return !empty(get_category_description($item));
}

/**
 * Checks if the category has a logo.
 *
 * @param Content $item The item to check category logo for. If not provided,
 *                      the function will try to search the item in the
 *                      template.
 *
 * @return bool True if the category has a logo. False otherwise.
 */
function has_category_logo($item = null) : bool
{
    return !empty(get_category_logo($item));
}
