<script type="application/javascript" src="//ced.sascdn.com/tag/{$config['network_id']}/smart.js" async></script>
<script type="application/javascript">
  var targetingCode = '{$targetingCode}';

  {$customCode}

  var sas = sas || {};
  sas.cmd = sas.cmd || [];
  sas.cmd.push(function() {
    sas.setup({ networkid: {$config['network_id']}, domain: "{$config['domain']}", async: true });
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
    }, {
      onNoad: function(data) {
        if (data.formatId) {
          $('#' + data.tagId).parent().remove();
        }
      }
    });
  });
</script>
