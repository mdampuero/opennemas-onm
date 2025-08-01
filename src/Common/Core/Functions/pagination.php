<?php

/**
 * Returns the current page number.
 *
 * @param type variable Description
 *
 * @return int The current page.
 */
function get_pagination_current_page() : int
{
    $page = getService('core.template.frontend')->getValue('page');

    return empty($page) ? 1 : $page;
}

/**
 * Returns the URL for the first page.
 *
 * @return string The URL for the first page.
 */
function get_pagination_first_page_url() : string
{
    return get_pagination_page_url(1);
}

/**
 * Returns the URL for the last page.
 *
 * @return string The URL for the last page.
 */
function get_pagination_last_page_url() : ?string
{
    return get_pagination_page_url(get_pagination_total_pages());
}

/**
 * Returns the URL for the next page.
 *
 * @return string The URL for the next page.
 */
function get_pagination_next_page_url() : ?string
{
    $page  = get_pagination_current_page();
    $total = get_pagination_total_pages();

    return $page + 1 <= $total
        ? get_pagination_page_url($page + 1)
        : null;
}

/**
 * Returns the list of next pages until a maximum number of pages.
 *
 * @param int $max The maximum number of pages to return.
 *
 * @return array The list of next pages.
 */
function get_pagination_next_pages(int $max) : array
{
    $current = get_pagination_current_page();
    $total   = get_pagination_total_pages();
    $pages   = [];

    while ($current < $total && $max > 0) {
        $pages[] = ++$current;
        $max--;
    }

    return $pages;
}

/**
 * Returns the URL for the page.
 *
 * @param int $page The page to generate URL for.
 *
 * @return string The URL for the page.
 */
function get_pagination_page_url(int $page) : string
{
    $uri = getService('core.globals')->getRequest()->getRequestUri();
    $uri = preg_replace('/page=[0-9]*&?/', '', $uri);
    $uri = trim(trim($uri, '&'), '?');

    if ($page === 1) {
        return $uri;
    }

    return strpos($uri, '?') === false
        ? $uri . '?page=' . $page
        : preg_replace('/\\?/', '?page=' . $page . '&', $uri);
}

/**
 * Returns the URL for the previous page.
 *
 * @return string The URL for the next page.
 */
function get_pagination_previous_page_url() : ?string
{
    $page = get_pagination_current_page();

    return $page > 1
        ? get_pagination_page_url($page - 1)
        : null;
}

/**
 * Returns the list of previous pages until a maximum number of pages.
 *
 * @param int $max The maximum number of pages to return.
 *
 * @return array The list of previous pages.
 */
function get_pagination_previous_pages(int $max) : array
{
    $current = get_pagination_current_page();
    $pages   = [];

    while ($current > 1 && $max > 0) {
        $pages[] = --$current;
        $max--;
    }

    return array_reverse($pages);
}

/**
 * Returns the number of total pages based on the total number of items to show
 * and the number of items per page.
 *
 * @return int The total number of pages.
 */
function get_pagination_total_pages() : int
{
    $epp   = getService('core.template.frontend')->getValue('epp') ?? 1;
    $total = getService('core.template.frontend')->getValue('total') ?? 1;

    return ceil($total / $epp);
}

/**
 * Checks if the current page has pagination.
 *
 * @return bool True if the current page has pagination. False otherwise.
 */
function has_pagination() : bool
{
    return get_pagination_total_pages() > 1;
}

/**
 * Checks if the next page exists.
 *
 * @return bool True if the next page exists. False otherwise.
 */
function has_pagination_next_page() : bool
{
    return !empty(get_pagination_next_pages(1));
}

/**
 * Checks if the previous page exists.
 *
 * @return bool True if the previous page exists. False otherwise.
 */
function has_pagination_previous_page() : bool
{
    return !empty(get_pagination_previous_pages(1));
}
