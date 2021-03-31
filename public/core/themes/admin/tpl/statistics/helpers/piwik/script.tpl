<!-- Piwik -->
<script>
  var _paq = _paq || [];

  _paq.push(['trackPageView']);
  _paq.push(['enableLinkTracking']);

  (function() {
    var u = "{$config['server_url']}";
    _paq.push(['setTrackerUrl', u + '/piwik.php']);
    _paq.push(['setSiteId', '{$config["page_id"]}']);
    var d = document, g = d.createElement('script'), s = d.getElementsByTagName('script')[0];
    g.async = true; g.defer = true;
    g.src = u + '/piwik.js'; s.parentNode.insertBefore(g,s);
  })();
</script>
<noscript>
  <img src="{$config['server_url']}/piwik.php?idsite={$config['page_id']}" style="border:0" alt="" />
</noscript>
<!-- End Piwik Tracking Code -->
