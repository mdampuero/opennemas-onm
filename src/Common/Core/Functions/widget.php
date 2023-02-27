<?php

/**
 * Returns true if the widget exists, false otherwise.
 *
 * @param  string $name The identifier name of the widget.
 *
 * @return boolean True if the widget exists, false otherwise.
 */
function widget_exists($name)
{
    return getService('core.helper.widget')->widgetExists($name);
}
