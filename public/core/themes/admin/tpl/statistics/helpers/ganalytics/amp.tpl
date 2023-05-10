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
  {if strpos($account, 'G') === 0}
    <amp-analytics type="googleanalytics" config="https://amp.analytics-debugger.com/ga4.json" data-credentials="include">
    <script type="application/json">
    {
        "vars": {
          "GA4_MEASUREMENT_ID": "{$account}",
          "GA4_ENDPOINT_HOSTNAME": "www.google-analytics.com",
          "DEFAULT_PAGEVIEW_ENABLED": true,
          "GOOGLE_CONSENT_ENABLED": false,
          "WEBVITALS_TRACKING": false,
          "PERFORMANCE_TIMING_TRACKING": false,
          "SEND_DOUBLECLICK_BEACON": false
        }
    }
    </script>
    </amp-analytics>
  {/if}
{/foreach}
