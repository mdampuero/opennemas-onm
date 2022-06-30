<amp-analytics>
<script type="application/json">
{
  "requests": {
    {literal}"pageview": "//${canonicalHost}{url name=frontend_content_stats content_id=$id}&_=RANDOM"{/literal}
  },
  "triggers": {
    "trackPageview": {
      "on": "visible",
      "request": "pageview"
    }
  },
  "transport": {
    "beacon": true
  }
}
</script>
</amp-analytics>
