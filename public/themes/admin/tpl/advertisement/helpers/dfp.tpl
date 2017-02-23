<html>
  <head>
    <style>
      body {
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
    <script async="async" src="https://www.googletagservices.com/tag/js/gpt.js"></script>
    <script>
      var googletag = googletag || {};
      googletag.cmd = googletag.cmd || [];
    </script>
    <script type="text/javascript">
      googletag.cmd.push(function() {
        googletag.defineSlot('{{$dfpId}}', {{$sizes}}, 'zone_{{$id}}').addService(googletag.pubads());
        {{$targetingCode}}
        {{$customCode}}
        googletag.pubads().enableSingleRequest();
        googletag.pubads().collapseEmptyDivs();
        googletag.enableServices();
      });
    </script>
    <div id="zone_{$id}">
      <script type="text/javascript">
        googletag.cmd.push(function() { googletag.display('zone_{$id}'); });
      </script>
    </div>
  </body>
</html>
