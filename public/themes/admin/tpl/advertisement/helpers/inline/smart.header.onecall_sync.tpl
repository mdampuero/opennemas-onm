<script type="application/javascript" src="//ced.sascdn.com/tag/{$config['network_id']}/smart.js"></script>
<script type="application/javascript">
    sas.setup({ networkid: {$config['network_id']}, domain: "{$config['domain']}" });
</script>

<script type="application/javascript">
  var targetingCode = '{$targetingCode}';

  {$customCode}

  sas.call("onecall", {
    siteId: {$config['site_id']},
    pageId: {$page_id},
    formatId: "{foreach $zones as $zone}{if $zone@last}{$zone['format_id']}{else}{$zone['format_id']},{/if}{/foreach}",
    target: targetingCode
  }, {
    onNoad: function(data) {
      if (data.formatId) {
        $('#' + data.tagId).parent().remove();
      }
    }
  });
</script>
