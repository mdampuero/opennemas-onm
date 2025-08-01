<script type='text/javascript'>
  (function() {
    /** CONFIGURATION START **/
    var _sf_async_config = window._sf_async_config = (window._sf_async_config || {});
    _sf_async_config.uid = {$id}; // ACCOUNT NUMBER
    _sf_async_config.domain = "{$domain}"; // DOMAIN
    _sf_async_config.useCanonical = true;
    _sf_async_config.useCanonicalDomain = true;
    _sf_async_config.sections = "{$category}";
    _sf_async_config.authors = "{$author}";
    /** CONFIGURATION END **/
    function loadChartbeat() {
        var e = document.createElement('script');
        var n = document.getElementsByTagName('script')[0];
        e.type = 'text/javascript';
        e.async = true;
        e.src = '//static.chartbeat.com/js/chartbeat.js';
        n.parentNode.insertBefore(e, n);
    }
    loadChartbeat();
  })();
</script>
