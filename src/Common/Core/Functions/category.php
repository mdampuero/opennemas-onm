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
    return getService('core.helper.category')->getCategory($item);
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
    return getService('core.helper.category')->getCategoryColor($item);
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
    return getService('core.helper.category')->getCategoryDescription($item);
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
    return getService('core.helper.category')->getCategoryId($item);
}

/**
 * Returns the path to category logo for the provided item.
 *
 * @param Content $item The item to get logo path for. If not provided, the
 *                      function will try to search the item in the template.
 *
 * @return ?\Common\Model\Entity\Content The photo logo object. Null otherwise.
 */
function get_category_logo($item = null) : ?\Common\Model\Entity\Content
{
    return getService('core.helper.category')->getCategoryLogo($item);
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
    return getService('core.helper.category')->getCategoryName($item);
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
    return getService('core.helper.category')->getCategorySlug($item);
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
    return getService('core.helper.category')->getCategoryUrl($item);
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
    return getService('core.helper.category')->hasCategoryDescription($item);
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
    return getService('core.helper.category')->hasCategoryLogo($item);
}

/**
 * Checks if the category has manual layout.
 *
 * @param Content $item The item to check category logo for. If not provided,
 *                      the function will try to search the item in the
 *                      template.
 *
 * @return bool True if the category has a manual layout. False otherwise.
 */
function is_manual_category($item = null) : bool
{
    return getService('core.helper.category')->isManualCategory($item);
}
