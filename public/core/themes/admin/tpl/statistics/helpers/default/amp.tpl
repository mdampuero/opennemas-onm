<amp-analytics>
<script type="application/json">
{
  "requests": {
    "pageview": "//{literal}${ampdocHost}{/literal}{url name=frontend_content_stats content_id=$id}&_=RANDOM"
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
