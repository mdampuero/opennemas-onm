<script type="application/javascript" src="//ced.sascdn.com/tag/{$config['network_id']}/smart.js" async></script>
<script type="application/javascript">
    var sas = sas || {};
    sas.cmd = sas.cmd || [];
    sas.cmd.push(function() {
        sas.setup({ networkid: {$config['network_id']}, domain: "{$config['domain']}", async: true{if $config['header_bidding']}, renderMode : 2{/if} });
    });
</script>
{if $config['header_bidding']}
  <script type="text/javascript" src="//tagmanager.smartadserver.com/{$config['network_id']}/{$config['site_id']}/smart.prebid.js" async></script>
{/if}
