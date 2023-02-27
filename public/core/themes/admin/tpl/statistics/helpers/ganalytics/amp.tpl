{foreach $accounts as $key => $account}
  <amp-analytics type="gtag" data-credentials="include" id="analytics{$key}">
    <script type="application/json">
    {
      "vars" : {
        "gtag_id": "{$account}",
        "config" : {
          "{$account}": {
            "groups": "default"
            {if $dataLayer}
              , {$dataLayer}
            {/if}
          }
        }
      }
    }
    </script>
  </amp-analytics>
{/foreach}
