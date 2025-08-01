<?php
/**
 * Returns all the theme settings defined on backend.
 *
 * @param array $params the list of parameters
 * @param \Smarty $smarty the smarty instance
 *
 * @return string
 */
function smarty_function_getThemeOptions($params, &$smarty)
{
    $action    = array_key_exists('action', $params) && !empty($params['action']) ? $params['action'] : '';
    $extension = array_key_exists('extension', $params) && !empty($params['extension']) ? $params['extension'] : '';

    if (empty($smarty->getValue('theme_options'))) {
        $smarty->assign(
            $smarty->getContainer()->get('core.helper.theme_settings')->getThemeVariables($action, $extension)
        );
    }
}
