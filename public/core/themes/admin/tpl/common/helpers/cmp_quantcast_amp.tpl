
<amp-consent id="inmobi" layout="nodisplay">
  <script type="application/json">
    {
      "consentInstanceId": "inmobi",
      "checkConsentHref": "https://apis.cmp.inmobi.com/amp/check-consent",
      "consentRequired": "remote",
      "promptUISrc": "https://cmp.inmobi.com/tcfv2/amp.html",
      "postPromptUI": "post-prompt-ui",
      "clientConfig": {
        "coreConfig": {
          "inmobiAccountId": "{$id}",
          "privacyMode": [ "GDPR" ],
          "displayUi": "always",
          "googleEnabled": true,
          "publisherCountryCode": "ES",
          "lang_": "{$smarty.const.CURRENT_LANGUAGE_SHORT}",
          "initScreenRejectButtonShowing": false,
          "initScreenCloseButtonShowing": false,
          "initScreenBodyTextOption": 1,
          "defaultToggleValue": "on",
          "vendorPurposeIds": [1, 2, 3, 4, 5, 6, 7, 8, 9, 10, 11],
          "vendorFeaturesIds": [1, 2, 3],
          "vendorPurposeLegitimateInterestIds": [2, 7, 8, 9, 10, 11],
          "vendorSpecialFeaturesIds": [1, 2],
          "vendorSpecialPurposesIds": [1, 2],
          "vendorListUpdateFreq": 30,
          "gvlVersion": 3
        },
        "tagVersion": "V3"
      }
    }
  </script>
</amp-consent>
