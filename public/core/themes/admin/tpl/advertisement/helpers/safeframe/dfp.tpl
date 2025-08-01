<html>
  <head>
    <style>
      body {
        display: table;
        margin: 0;
        overflow: hidden;
        padding: 0;
        text-align: center;
      }

      img {
        height: auto;
        max-width: 100%;
      }
    </style>
  </head>
  <body>
    <div class="content">
      <script async src="https://securepubads.g.doubleclick.net/tag/js/gpt.js"></script>
      <script>
        var googletag = googletag || {};
        googletag.cmd = googletag.cmd || [];
      </script>
      <script>
        googletag.cmd.push(function() {
          googletag.defineSlot('{$dfpId}', {$sizes}, 'zone_{$id}').addService(googletag.pubads());
          {foreach $targeting as $key => $value}
            googletag.pubads().setTargeting('{$key}', ['{$value}']);
          {/foreach}
          {$customCode}
          googletag.pubads().enableSingleRequest();
          googletag.pubads().collapseEmptyDivs();
          googletag.enableServices();
        });
      </script>
      <div id="zone_{$id}">
        <script>
          googletag.cmd.push(function() { googletag.display('zone_{$id}'); });
        </script>
      </div>
    </div>
  </body>
</html>
