{foreach $params as $key => $account}
  <amp-analytics type="googleanalytics" id="analytics{$key}">
    <script type="application/json">
    {
      "vars": {
        "account": "{trim($account['api_key'])}"
      },
      "triggers": {
        "trackPageview": {
          "on": "visible",
          "request": "pageview"
        }
      }
    }
    </script>
  </amp-analytics>
{/foreach}
<amp-analytics type="googleanalytics" id="analytics-onm">
  <script type="application/json">
  {
    "vars": {
      "account": "UA-40838799-5"
    },
    "triggers": {
      "trackPageview": {
        "on": "visible",
        "request": "pageview"
      }
    }
  }
  </script>
</amp-analytics>
