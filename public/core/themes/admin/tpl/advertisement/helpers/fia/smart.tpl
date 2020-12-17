<figure class="op-ad{if $default} op-ad-default{/if}">
  <iframe height="{$height}" width="{$width}" style="border:0;margin:0;padding:0;">
    <script type="application/javascript" src="//ced.sascdn.com/tag/{$config['network_id']}/smart.js" async></script>
    <div id="sas_{$format_id}"></div>
    <script type="application/javascript">
      var sas = sas || {};
      sas.cmd = sas.cmd || [];
      sas.cmd.push(
        function () {
          sas.call(
            { siteId: {$config['site_id']}, pageId: {$page_id}, formatId: {$format_id}, tagId: "sas_{$format_id}" },
            { networkId: {$config['network_id']}, domain: "{$config['domain']}" }
          );
        }
      );
    </script>
  </iframe>
</figure>
