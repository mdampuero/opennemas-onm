<script>
  var _sf_async_config = _sf_async_config || {};
  /** CONFIGURATION START **/
  _sf_async_config.uid = {$id}; // ACCOUNT NUMBER
  _sf_async_config.domain = "{$domain}"; // DOMAIN
  _sf_async_config.flickerControl = false;
  _sf_async_config.useCanonical = true;
  /** CONFIGURATION END **/
  var _sf_startpt = (new Date()).getTime();
</script>
<script async src="//static.chartbeat.com/js/chartbeat_mab.js"></script>
<script>
  var _sf_async_config = _sf_async_config || {};
  /** CONFIGURATION START **/
  _sf_async_config.sections = "{$category}";
  _sf_async_config.authors = "{$author}";

  /** CONFIGURATION END **/
  (function() {
    function loadChartbeat() {
      window._sf_endpt = (new Date()).getTime();
      var e = document.createElement("script");
      e.setAttribute("language", "javascript");
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
</script>
