<?php
use \Onm\Settings as s;

function smarty_function_include_ojd_code($params, &$smarty)
{

    $output = "";

    // Get piwik config
    $comscoreConfig = s::get('comscore');

    // Only return anything if the G Analytics is setted in the configuration
    if (is_array($comscoreConfig)
        && array_key_exists('page_id', $comscoreConfig)
        && !empty($comscoreConfig['page_id'])
    ) {

        $output = '
            <!-- BegincomScore  Tag -->
            <script>
               var _comscore = _comscore || [];
               _comscore.push({ c1: "2", c2: "'. $comscoreConfig['page_id'] .'" });
               (function() {
                 var s = document.createElement("script"), '.
                 'el = document.getElementsByTagName("script")[0]; s.async = true;
                 s.src = (document.location.protocol == "https:" ? '.
                    '"https://sb  <https://sb/>" :"http://b  <http://b/>") + '.
                    '".scorecardresearch.com/beacon.js  <http://scorecardresearch.com/beacon.js>";
                 el.parentNode.insertBefore(s, el);
               })();
            </script>
            <noscript>
               <img src="http://b.scorecardresearch.com/p?c1=2&c2='. $comscoreConfig['page_id'] .'&cv=2.0&cj=1" />
            </noscript>
            <!-- EndcomScore  Tag -->';
    }
    return $output;
}
