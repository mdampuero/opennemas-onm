<?php

/**
 * Returns the total votes of poll
 *
 * @param mixed $item The item to get poll.
 *
 * @return array The total votes of poll.
 */
function get_total_votes($items = []) : ?array
{
    return getService('core.helper.poll')->getTotalVotes($items);
}

/**
 * Returns whether a survey is closed or not
 *
 * @param mixed $item The item to get poll.
 *
 * @return bool True or False
 */
function is_closed($item)
{
    return getService('core.helper.poll')->isClosed($item);
}
