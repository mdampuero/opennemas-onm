<?php
/*
 * -------------------------------------------------------------
 * File:        function.get_permalink.php
 * Returns the permalink for a given content
 * -------------------------------------------------------------
 */
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;

function smarty_function_get_permalink($params)
{
    if (!array_key_exists('item', $params)
        || !is_object($params['item'])
        || empty($params['item']->id)
    ) {
       return '';
    }

    if (array_key_exists('absolute', $params) && $params['absolute']) {
        $absolute = UrlGeneratorInterface::ABSOLUTE_URL;
    } else {
        $absolute = UrlGeneratorInterface::ABSOLUTE_PATH;
    }

    $content = $params['item'];

    return getService('router') ->generate(
        'frontend_content_permalink',
        ['content_id' => $content->id ],
        $absolute
    );
}
