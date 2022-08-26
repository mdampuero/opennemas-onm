<amp-analytics type='gfksensic' data-block-on-consent='_till_responded'>
  <script type='application/json'>
    {
      "requests": {
        "custom_params": "cp_c1={$domain}&cp_c2={$category}"
      },
      "vars": {
        {if !empty({setting name='gfk' field='pre_mode'})}"environment": "-preproduction",{/if}

        "regionID": "{$regionId}",
        "mediaID": "{$mediaId}Amp"
      },
      "triggers": {
        "trackConsent": {
          "on": "visible",
          "request": "impression"
        }
      }
    }
  </script>
</amp-analytics>
