<script async="async" src="https://www.googletagservices.com/tag/js/gpt.js"></script>
<script>
  var googletag = googletag || {};
  googletag.cmd = googletag.cmd || [];
  googletag.cmd.push(function() {
    {foreach $zones as $zone}
    googletag.defineSlot('{$zone['dfpId']}', {$zone['sizes']}, 'zone_{$zone['id']}').addService(googletag.pubads());
    {/foreach}

    {if !empty($options) && array_key_exists('target', $options) && !empty($options['target'])}googletag.pubads().setTargeting('{$options['target']}', [ '{$category}' ]);
    googletag.pubads().setTargeting('{$options['module']}', [ '{$extension}' ]);
    {/if}
    {$customCode}
    googletag.pubads().enableSingleRequest();
    googletag.pubads().collapseEmptyDivs();
    googletag.enableServices();
  });
</script>
