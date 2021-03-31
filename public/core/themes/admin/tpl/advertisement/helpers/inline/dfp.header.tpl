<script async="async" src="https://www.googletagservices.com/tag/js/gpt.js"></script>
<script>
  var googletag = googletag || {};
  googletag.cmd = googletag.cmd || [];
</script>
<script>
  googletag.cmd.push(function() {
    {foreach $zones as $zone}
      googletag.defineSlot('{$zone['dfpId']}', {$zone['sizes']}, 'zone_{$zone['id']}').addService(googletag.pubads());
    {/foreach}

    {foreach $targeting as $key => $value}
      googletag.pubads().setTargeting('{$key}', ['{$value}']);
    {/foreach}

    {$customCode}
    googletag.pubads().enableSingleRequest();
    googletag.pubads().collapseEmptyDivs();
    googletag.enableServices();
  });
</script>
