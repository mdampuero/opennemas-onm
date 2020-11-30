<html>
  <head>
    <style>
      body {
        display: table;
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
    <div class="content">
      <script type="application/javascript" src="//ced.sascdn.com/tag/{$config['network_id']}/smart.js" async></script>
      <div id="sas_{$format_id}"></div>
      <script type="application/javascript">
        var sas = sas || {};
        sas.cmd = sas.cmd || [];
        sas.cmd.push(
          function () {
            sas.call(
              { siteId: {$config['site_id']}, pageId: {$page_id}, formatId: {$format_id}, tagId: "sas_{$format_id}" },
              { networkId: {$config['network_id']}, domain: "{$config['domain']}" /*, onNoad: function() {} */ }
            );
          }
        );
      </script>
    </div>
  </body>
</html>
