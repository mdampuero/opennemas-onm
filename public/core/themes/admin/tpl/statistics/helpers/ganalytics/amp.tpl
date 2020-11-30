{foreach $accounts as $key => $account}
  <amp-analytics type="gtag" data-credentials="include" id="analytics{$key}">
    <script type="application/json">
    {
      "vars" : {
        "gtag_id": "{$account}",
        "config" : {
          "{$account}": { "groups": "default" }
        }
      }
    }
    </script>
  </amp-analytics>
{/foreach}
<amp-analytics type="gtag" data-credentials="include" id="analytics-onm">
  <script type="application/json">
  {
    "vars" : {
      "gtag_id": "UA-40838799-5",
      "config" : {
        "UA-40838799-5": { "groups": "default" }
      }
    }
  }
  </script>
</amp-analytics>
