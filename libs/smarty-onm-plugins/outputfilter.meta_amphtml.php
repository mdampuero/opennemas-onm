<?php
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;

/**
 * Adds the meta amphtml to the page.
 *
 * @param string $output
 * @param \Smarty $smarty
 *
 * @return string
 */
function smarty_outputfilter_meta_amphtml($output, $smarty)
{
    $request = $smarty->getContainer()->get('request_stack')->getCurrentRequest();

    if (is_null($request)) {
        return $output;
    }

    $uri = $request->getUri();

    $container = $smarty->getContainer();

    if (!$container->get('core.security')->hasExtension('AMP_MODULE')
        || strstr($uri, 'amp.html') !== false
        || strstr($uri, 'blog/section') !== false
        || !array_key_exists('content', $smarty->tpl_vars)
        || !is_object($smarty->tpl_vars['content']->value)
        || $smarty->tpl_vars['content']->value->content_type_name !== 'article'
    ) {
        return $output;
    }

    $content = $smarty->tpl_vars['content']->value;

    $url = $container->get('router')->generate('frontend_article_show_amp', [
        'category_name' => $content->category_name,
        'slug'          => $content->slug,
        'article_id'    => date('YmdHis', strtotime($content->created))
            . sprintf('%06d', $content->pk_content),
    ], UrlGeneratorInterface::ABSOLUTE_URL);

    $url = $container->get('core.helper.l10n_route')
        ->localizeUrl($url);

    $code = '<link rel="amphtml" href="' . $url . '"/>';

    return preg_replace('@(</head>)@', $code . '${1}', $output);
}
