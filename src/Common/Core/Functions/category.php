<?php
/**
 * Returns the category for the provided item.
 *
 * @param Content $item The item to get category for. If not provided, the
 *                      function will try to search the item in the template.
 *
 * @return \Common\ORM\Entity\Category The category.
 */
function get_category($item = null) : ?\Common\ORM\Entity\Category
{
    $item = $item ?? getService('core.template.frontend')->getValue('item');

    if (empty($item)) {
        return null;
    }

    if ($item instanceof \Content && !empty($item->pk_fk_content_category)) {
        try {
            return getService('api.service.category')
                ->getItem($item->pk_fk_content_category);
        } catch (\Exception $e) {
            return null;
        }
    }

    return $item instanceof \Common\ORM\Entity\Category
        ? $item
        : null;
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

    return !empty($category) ? $category->pk_content_category : null;
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
