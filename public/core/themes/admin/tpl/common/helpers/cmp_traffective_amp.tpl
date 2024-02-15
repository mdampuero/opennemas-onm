<amp-consent id="consent" layout="nodisplay" type="opencmp">
    <script type="application/json">
        {
            "postPromptUI": "post-prompt-ui",
            "sandbox": "allow-top-navigation-by-user-activation allow-popups-to-escape-sandbox",
            "clientConfig": {
                "domain": "{$id}",
                "paywallRedirectUrl": "{$canonical}",
                "activationKey": "{$apikey}"
            },
            "uiConfig": { "overlay": true }
        }
    </script>
</amp-consent>
