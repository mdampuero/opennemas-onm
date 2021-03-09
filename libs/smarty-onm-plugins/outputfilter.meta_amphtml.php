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
    $content             = $smarty->getValue('o_content');
    $allowedContentTypes = [ 'album', 'article', 'opinion', 'poll', 'video' ];
    $tpl                 = '<link rel="amphtml" href="%s"/>';

    if (empty($content)
        || !in_array($content->content_type_name, $allowedContentTypes)
        || !$container->get('core.security')->hasExtension('AMP_MODULE')
        || empty($request)
        || strpos($request->getRequestUri(), 'amp.html') !== false
    ) {
        return $output;
    }
    try {
        $url = $container->get('core.helper.l10n_route')->localizeUrl(
            $container->get('core.helper.url_generator')
                ->generate(
                    $content,
                    ['_format' => 'amp', 'absolute' => true ]
                )
        );

        return str_replace('</head>', sprintf($tpl, $url) . '</head>', $output);
    } catch (\Throwable $th) {
        return $output;
    }
}
