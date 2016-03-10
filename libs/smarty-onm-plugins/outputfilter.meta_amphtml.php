<?php
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;

/*
 * Smarty plugin
 * -------------------------------------------------------------
 * File:     outputfilter.piwik.php
 * Type:     outputfilter
 * Name:     canonical_url
 * Purpose:  Prints Piwik code
 * -------------------------------------------------------------
 */
function smarty_outputfilter_meta_amphtml($output, $smarty)
{
    if (\Onm\Module\ModuleManager::isActivated('AMP_VERSION')
        && array_key_exists('content', $smarty->tpl_vars)
        && is_object($smarty->tpl_vars['content']->value)
        && $smarty->tpl_vars['content']->value->content_type_name == 'article'
    ) {
        $content = $smarty->tpl_vars['content']->value;
        // SHITTY CODE: we need to create a proper way to generate urls from contents.
        $params = [
            'category_name' => $content->category_name,
            'slug'          => $content->slug,
            'article_id'    => date('YmdHis', strtotime($content->created)).sprintf('%06d',$content->pk_content),
        ];

        $url = getService('router')
            ->generate('frontend_article_show_amp', $params, UrlGeneratorInterface::ABSOLUTE_URL);

        $code   = '<link rel="amphtml" href="'.$url.'" />';
        $output = preg_replace('@(</head>)@', $code.'${1}', $output);
    }

    return $output;
}
