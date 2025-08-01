<div id="sas_{$id}_{$rand}"></div>
<script type="application/javascript">
  var targetingCode = '{$targeting}';

  {$customCode}

  sas.cmd.push(function() {
    sas.call("std", {
      siteId: {$config['site_id']},
      pageId: {$page_id},
      formatId: {$id},
      tagId: 'sas_{$id}_{$rand}',
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
