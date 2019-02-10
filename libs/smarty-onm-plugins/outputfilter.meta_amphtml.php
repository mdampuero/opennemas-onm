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

    // var_dump($container->get('core.helper.url_generator')->generate($content, [ 'absolute' => true ]));die();

    $url = $container->get('core.helper.l10n_route')->localizeUrl(
        $container->get('router')->generate(
            'frontend_' . $content->content_type_name . '_show_amp',
            [ 'id' => $content->pk_content, ],
            UrlGeneratorInterface::ABSOLUTE_URL
        )
    );

    return str_replace('</head>', sprintf($tpl, $url) . '</head>', $output);
}
