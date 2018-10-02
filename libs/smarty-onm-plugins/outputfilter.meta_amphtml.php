<?php

use Symfony\Component\Routing\Generator\UrlGeneratorInterface;

function smarty_outputfilter_meta_amphtml($output, $smarty)
{
    $container = $smarty->getContainer();
    $request   = $container->get('request_stack')->getCurrentRequest();

    if (!$container->get('core.security')->hasExtension('AMP_MODULE')
        || empty($request)
        || strpos($request->getRequestUri(), 'amp.html') !== false
        || !array_key_exists('o_content', $smarty->getTemplateVars())
        || $smarty->getTemplateVars()['o_content']->content_type_name !== 'article'
    ) {
        return $output;
    }

    $content = $smarty->getTemplateVars()['o_content'];
    $tpl     = '<link rel="amphtml" href="%s"/>';

    $url = $container->get('core.helper.l10n_route')->localizeUrl(
        $container->get('router')->generate('frontend_article_show_amp', [
            'category_name' => $content->category_name,
            'slug'          => $content->slug,
            'article_id'    => date('YmdHis', strtotime($content->created))
                . sprintf('%06d', $content->pk_content),
            ], UrlGeneratorInterface::ABSOLUTE_URL)
    );

    return str_replace('</head>', sprintf($tpl, $url) . '</head>', $output);
}
