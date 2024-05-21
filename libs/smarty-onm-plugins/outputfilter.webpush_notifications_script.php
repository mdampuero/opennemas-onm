<?php
/**
 * Handles the needed js for Web Push notifications.
 *
 * @param string $output
 * @param \Smarty $smarty
 *
 * @return string
 */
function smarty_outputfilter_webpush_notifications_script($output, $smarty)
{
    $request = $smarty->getContainer()->get('request_stack')->getCurrentRequest();

    if (is_null($request)) {
        return $output;
    }

    $uri = $request->getUri();

    if (!preg_match('/newsletter/', $smarty->source->resource)
        && !preg_match('/\/manager/', $uri)
        && !preg_match('/\/managerws/', $uri)
        && !preg_match('/\/sharrre/', $uri)
        && !preg_match('/\/ads\//', $uri)
        && !preg_match('/\/comments\//', $uri)
        && !preg_match('/\/hbbtv/', $uri)
        && !preg_match('/\/rss\/(?!listado$)/', $uri)
        && !preg_match('@\.amp\.html@', $uri)
    ) {
        $ds      = $smarty->getContainer()->get('orm.manager')->getDataSet('Settings', 'instance');
        $service = $ds->get('webpush_service');

        if (empty($service) || !empty($ds->get('webpush_stop_collection'))) {
            return $output;
        }

        try {
            $webpushHelper = $smarty->getContainer()->get(sprintf('core.helper.%s', $service));
            $script        = $webpushHelper->getWebpushCollectionScript();

            $output = preg_replace(
                '@(<body.*>)@',
                '${1}' . "\n" . $script . "\n",
                $output
            );
        } catch (\Exception $e) {
            return $output;
        }
    }

    return $output;
}
