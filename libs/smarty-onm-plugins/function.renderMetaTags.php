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

    // Fetch first content of tag|category listing
    if ($content instanceof \Common\Model\Entity\Tag
        || $content instanceof \Common\Model\Entity\Category
    ) {
        $data = $smarty->getValue('contents') ??
            $smarty->getValue('articles') ?? [];

        $content->firstData = array_shift($data);
    }

    $page      = $smarty->getValue('page') ?? null;
    $extension = $smarty->getContainer()->get('core.globals')->getExtension();
    $action    = $smarty->getContainer()->get('core.globals')->getAction();

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
