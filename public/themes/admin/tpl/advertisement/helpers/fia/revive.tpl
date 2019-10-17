<figure class="op-ad{if $default} op-ad-default{/if}">
  <iframe height="{$height}" width="{$width}" style="border:0;margin:0;padding:0;">
    <script>
      var OA_zones = {
        'zone_{{$id}}': {{$reviveId}}
      };
    </script>
    <script src="{{$url}}/www/delivery/spcjs.php?cat_name={{$category}}"></script>
    <script>
      OA_show('zone_{{$id}}');
    </script>
  </iframe>
</figure>
