<amp-analytics type='gfksensic' data-block-on-consent='_till_responded'>
  <script type='application/json'>
    {
      "requests": {
        "custom_params": "cp_cp1={$domain}&cp_cp2={$category}"
      },
      "vars": {
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
