<amp-consent id="consent" layout="nodisplay" type="quantcast">
  <script type="application/json">
    {
      "postPromptUI": "post-prompt-ui",
      "consentInstanceId": "quantcast",
      "checkConsentHref": " https://apis.quantcast.mgr.consensu.org/amp/check-consent",
      "promptUISrc": "https://quantcast.mgr.consensu.org/tcfv2/amp.html",
      "clientConfig": {
        "coreConfig": {
          "quantcastAccountId": "{$id}",
          "privacyMode": [ "GDPR" ],
          "googleEnabled": true,
          "lang_": "{$smarty.const.CURRENT_LANGUAGE_SHORT}",
          "initScreenRejectButtonShowing": false,
          "defaultToggleValue": "on",
          "vendorListUpdateFreq": 30
        }
      }
    }

  </script>
</amp-consent>
