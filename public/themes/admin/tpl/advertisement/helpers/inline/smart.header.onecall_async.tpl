<script type="application/javascript" src="//ced.sascdn.com/tag/{$config['network_id']}/smart.js" async></script>
{if $config['header_bidding']}
  <script type="text/javascript" src="//tagmanager.smartadserver.com/{$config['network_id']}/{$config['site_id']}/smart.prebid.js" async></script>
{/if}
<script type="application/javascript">
  var targetingCode = '{$targetingCode}';

  {$customCode}

  var sas = sas || {};
  sas.cmd = sas.cmd || [];
  sas.cmd.push(function() {
    sas.setup({ networkid: {$config['network_id']}, domain: "{$config['domain']}", async: true{if $config['header_bidding']}, renderMode : 2{/if} });
  });
  sas.cmd.push(function() {
    sas.call("onecall", {
      siteId: {$config['site_id']},
      pageId: {$page_id},
      formats: [
        {foreach $zones as $zone}
          { id: {$zone['format_id']} },
        {/foreach}
      ],
      target: targetingCode
    }{if !$config['header_bidding']}, {
      onNoad: function(data) {
        if (data.formatId) {
          $('#' + data.tagId).parent().remove();
        }
      }
    }{/if});
  });
</script>
