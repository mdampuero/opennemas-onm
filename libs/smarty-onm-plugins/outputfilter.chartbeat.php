<?php
/*
 * Smarty plugin
 * -------------------------------------------------------------
 * File:     outputfilter.chartbeat.php
 * Type:     outputfilter
 * Name:     chartbeat
 * Purpose:  Prints chartbeat analytics code
 * -------------------------------------------------------------
 */
function smarty_outputfilter_chartbeat($output, $smarty)
{
    $request = $smarty->getContainer()->get('request_stack')->getCurrentRequest();

    if (is_null($request)) {
        return $output;
    }

    $uri     = $request->getUri();
    $referer = $request->headers->get('referer');

    if (!preg_match('/\/admin\/frontpages/', $referer)
        && !preg_match('/\/manager/', $uri)
        && !preg_match('/\/managerws/', $uri)
        && !preg_match('/\/share-by-email/', $uri)
        && !preg_match('/\/sharrre/', $uri)
        && !preg_match('/\/ads/', $uri)
        && !preg_match('/\/comments/', $uri)
        && !preg_match('/\/fb\/instant-articles/', $uri)
    ) {
        $isAmp = preg_match('@\.amp\.html$@', $uri);
        if ($isAmp) {
            $code = addChartBeatCode($output, $smarty, 'amp');
        } else {
            $code = addChartBeatCode($output, $smarty);
        }

        return $code;
    }

    return $output;
}

function addChartBeatCode($output, $smarty, $type = null)
{
    $config = getService('setting_repository')->get('chartbeat');

    if (!is_array($config)
        || !array_key_exists('id', $config)
        || !array_key_exists('domain', $config)
        || empty(trim($config['id']))
        || empty(trim($config['domain']))
    ) {
        return $output;
    }

    // Get author if exists otherwise get agency
    $author = '';
    if (array_key_exists('content', $smarty->tpl_vars)) {
        $content = $smarty->tpl_vars['content']->value;
        $user    = getService('user_repository')->find($content->fk_author);
        $author  = (!is_null($user->name)) ? $user->name : $content->agency;
        if (empty($author)) {
            $author = getService('setting_repository')->get('site_name');
        }
    }

    $code = '';
    if ($type === 'amp') {
        $code = '
<!-- Chartbeat tracking -->
<amp-analytics type="chartbeat">
    <script type="application/json">
        {
            "vars": {
                "uid": "' . $config['id'] . '",
                "domain": "' . $config['domain'] . '",
                "sections": "' . $smarty->tpl_vars['category_name'] . '",
                "authors": "' . $author . '"
            }
        }
    </script>
</amp-analytics>
<!-- End Chartbeat -->';
    } else {
        $code = '
<script type="text/javascript">
  var _sf_async_config = _sf_async_config || {};
  /** CONFIGURATION START **/
  _sf_async_config.sections = "' . $smarty->tpl_vars['category_name'] . '";
  _sf_async_config.authors = "' . $author . '";



  /** CONFIGURATION END **/
  (function() {
      function loadChartbeat() {
          window._sf_endpt = (new Date()).getTime();
          var e = document.createElement("script");
          e.setAttribute("language", "javascript");
          e.setAttribute("type", "text/javascript");
          e.setAttribute("src", "//static.chartbeat.com/js/chartbeat.js");

          document.body.appendChild(e);
      }
      var oldonload = window.onload;
      window.onload = (typeof window.onload != "function") ?
          loadChartbeat : function() {
              oldonload();
              loadChartbeat();
          };
      })();
</script>';

        $headCode = '
<script type="text/javascript">
    var _sf_async_config = _sf_async_config || {};
    /** CONFIGURATION START **/
    _sf_async_config.uid = ' . $config['id'] . '; // ACCOUNT NUMBER
    _sf_async_config.domain = "' . $config['domain'] . '"; // DOMAIN
    _sf_async_config.flickerControl = false;
    _sf_async_config.useCanonical = true;
    /** CONFIGURATION END **/
    var _sf_startpt = (new Date()).getTime();
</script>
<script async src="//static.chartbeat.com/js/chartbeat_mab.js"></script>';

        $output = str_replace('</head>', $headCode . '</head>', $output);
    }

    return str_replace('</body>', $code . '</body>', $output);
}
