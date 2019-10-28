<figure class="op-ad{if $default} op-ad-default{/if}">
  <iframe height="{$height}" width="{$width}" style="border:0;margin:0;padding:0;">
    <script async="async" src="https://www.googletagservices.com/tag/js/gpt.js"></script>
      <script>
        var googletag = googletag || {};
        googletag.cmd = googletag.cmd || [];
      </script>
      <script>
        googletag.cmd.push(function() {
          googletag.defineSlot('{{$dfpId}}', {{$sizes}}, 'zone_{{$id}}').addService(googletag.pubads());
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
  </iframe>
</figure>
