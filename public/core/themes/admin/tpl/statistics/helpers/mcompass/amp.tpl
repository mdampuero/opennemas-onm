{if $cookies === 'cmp'}
<amp-analytics config="https://sdk.newsroom.bi/amp.v2.json" data-credentials="include">
{else}
<amp-analytics config="https://sdk.newsroom.bi/amp.v1.json" data-credentials="include">
{/if}
  <script type="application/json">
    {
      "vars" : {
        "accountId": "{$id}"
      }
    }
  </script>
</amp-analytics>
