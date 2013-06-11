<?php
/**
 * Smarty plugin for rendering a intersticial banner
 *
 * @param array $params
 * @param Template $tpl Template class which extends of Smarty
*/
function smarty_insert_intersticial($params, &$smarty)
{
    // Get required params
    $type     = $params['type'];
    $ads      = $smarty->tpl_vars['advertisements']->value;
    $category = $smarty->tpl_vars['category']->value;

    // Filter advertisements for the insterstitial position
    $ads = array_filter(
        $ads,
        function ($ad) use ($type) {
            if ((($ad->type_advertisement + 50) % 100) == 0) {
                return true;
            }
            return false;
        }
    );

    // Render the advertisement content
    $content = '';
    if (count($ads) > 0) {
        // Pick one random advertisement from those available
        $selectedAd = $ads[array_rand($ads)];

        $adContent = json_encode($selectedAd->render($params));

        $timeout = intval($selectedAd->timeout) * 1000; // convert to ms
        $pk_advertisement = date('YmdHis', strtotime($selectedAd->created)).
                            sprintf('%06d', $selectedAd->pk_advertisement);

        $content = '<script type="text/javascript" language="javascript">
        /* <![CDATA[ */
            var intersticial = new IntersticialBanner({
                publiId: "'.$pk_advertisement.'",
                cookieName: "ib_'.$pk_advertisement.'",
                content: '.$adContent.',
                timeout: '.$timeout.'
            });
        /* ]]> */
        </script>';
    }

    return $content;
}
