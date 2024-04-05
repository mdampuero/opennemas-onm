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
    if (empty($smarty->getValue('theme_options'))) {
        $smarty->assign($smarty->getContainer()->get('core.helper.theme_settings')->getThemeVariables());
    }
}
