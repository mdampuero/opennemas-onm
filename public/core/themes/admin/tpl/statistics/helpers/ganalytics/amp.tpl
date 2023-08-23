{if !empty($accounts)}
  <amp-analytics type="gtag" data-credentials="include" id="analytics">
  <script type="application/json">
  {
    "vars" : {
      "gtag_id": "{$accounts[0]}",
      "config" : {
        {foreach $accounts as $key => $account}
          "{$account}": { "groups": "default"{if $dataLayer}, {$dataLayer}{/if}}{if !$account@last},{/if}

        {/foreach}
      }
    }
  }
  </script>
  </amp-analytics>
{/if}
