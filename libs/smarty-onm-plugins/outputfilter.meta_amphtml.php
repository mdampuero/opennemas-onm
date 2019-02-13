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
    $container           = $smarty->getContainer();
    $request             = $container->get('request_stack')->getCurrentRequest();
    $allowedContentTypes = ['article', 'opinion'];
    $templateVars        = $smarty->getTemplateVars();
    $content             =
        is_array($templateVars) && array_key_exists('o_content', $templateVars)
        ? $templateVars['o_content']
        : null;
    $tpl                 = '<link rel="amphtml" href="%s"/>';

    if (!$container->get('core.security')->hasExtension('AMP_MODULE')
        || empty($request)
        || strpos($request->getRequestUri(), 'amp.html') !== false
        || !is_object($content)
        || !property_exists($content, 'pk_content')
        || !in_array($content->content_type_name, $allowedContentTypes)
    ) {
        return $output;
    }

    $url = $container->get('core.helper.l10n_route')->localizeUrl(
        $container->get('router')->generate(
            'frontend_' . $content->content_type_name . '_show_amp',
            [ 'id' => $content->pk_content, ],
            UrlGeneratorInterface::ABSOLUTE_URL
        )
    );

    return str_replace('</head>', sprintf($tpl, $url) . '</head>', $output);
}
