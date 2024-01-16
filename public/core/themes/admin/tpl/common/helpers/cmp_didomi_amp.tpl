<amp-consent id="didomi" layout="nodisplay" type="didomi">
<script type="application/json">
{
  {if !empty($mrfpassId)}
  "promptUISrc": "https://experiences.mrf.io/passexperience/render?id={$mrfpassId}&type=amp",
  "sandbox": "allow-popups-to-escape-sandbox",
  {/if}
  "uiConfig": { "overlay": false },
  "clientConfig": {
    "gdprAppliesGlobally": true,
    "config": { "notice": { "initialHeight": "75vh" }, "app": { "apiKey": "{$apikey}" } },
    "noticeId": "{$id}"
  }
}
</script>
</amp-consent>
