<link href="https://cdntrf.com/css/{$config['domain']}.css" rel="stylesheet">

<script type="text/javascript"
  id="trfAdSetup"
  async
  data-traffectiveConf='{
    "targeting": [
      {ldelim}"key": "zone", "values": null, "value": "{$targeting['zone']}"{rdelim},
      {ldelim}"key": "pagetype", "values": null, "value": "{$targeting['pagetype']}"{rdelim},
      {ldelim}"key": "category", "values": null, "value": "{$targeting['category']}"{rdelim},
      {ldelim}"key": "module", "values": null, "value": "{$targeting['extension']}"{rdelim},
      {ldelim}"key": "programmatic_ads", "values": null, "value": "{if ($config['progAds'])}false{else}true{/if}"{rdelim},
      {ldelim}"key": "ads", "values": null, "value": "{if ($config['ads'])}false{else}true{/if}"{rdelim}
    ],
    "dfpAdUrl": "{$config['dfpUrl']}",
    "clientAlias": "{$config['client_alias']}"
  }'
  src="{$config['srcUrl']}">
</script>

