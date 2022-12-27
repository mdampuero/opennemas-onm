<?php

use Api\Exception\GetListException;
use Symfony\Component\DependencyInjection\Exception\ServiceNotFoundException;

/**
 * Used to generate links for tags depending on the selected method
 * twitter hashtag, onm internal tags, google search
 */
function smarty_function_renderMetaTags($params, &$smarty)
{
    $content = $smarty->getValue('content') ??
        $smarty->getValue('item') ??
        $smarty->getValue('poll') ??
        $smarty->getValue('author') ??
        $smarty->getValue('tag') ??
        $smarty->getValue('category');

    $page      = $smarty->getValue('page') ?? null;
    $extension = $smarty->getContainer()->get('core.globals')->getExtension();
    $action    = $smarty->getContainer()->get('core.globals')->getAction();
    $exception = '';

    // Code with some weird errors
    try {
        $exception = getService('request_stack')->getCurrentRequest()->attributes->get('exception') ?? '';
    } catch (\Exception $e) {
        $exception = '';
    }

    try {
        $output = $smarty->getContainer()
            ->get(sprintf('core.helper.meta.%s', $extension))->generateMetas($action, $content, $page, $exception);
    } catch (ServiceNotFoundException $e) {
        $output = $smarty->getContainer()
            ->get(sprintf('core.helper.meta'))
            ->generateMetas($action, $content, $page, $exception);
    }

    return $output;
}
