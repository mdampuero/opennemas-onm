<?php
/**
 * Renders the contents of a sidebar
 *
 * @param array $params the list of parameters
 * @param \Smarty $smarty the smarty instance
 *
 * @return string
 */
function smarty_function_render_sidebar($params, &$smarty)
{
    // Initializing parameters
    $sidebarName = isset($params['name']) ? $params['name'] : null;

    // TODO: Here will be the logic of the sidebar rendering

    // Render its contents
    return '';
}
