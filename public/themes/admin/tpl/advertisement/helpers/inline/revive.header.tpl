<script type="text/javascript">
  <!--// <![CDATA[
    var OA_zones = {
      {foreach $zones as $zone}
        'zone_{$zone['id']}' : {$zone['openXId']},
      {/foreach}
    };
  // ]]> -->
</script>
<script type="text/javascript" src="{$config['url']}/www/delivery/spcjs.php?cat_name={$actual_category}"></script>
