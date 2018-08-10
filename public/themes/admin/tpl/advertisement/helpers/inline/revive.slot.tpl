{if $iframe}
  <iframe src="{$url}" style="width: 800px; max-width:100%; height:600px; overflow: hidden; border:none" scrolling="no"></iframe>
{else}
  <script data-id="{$id}">
    <!--// <![CDATA[
      OA_show('zone_{$id}');
    // ]]> -->
  </script>
{/if}
