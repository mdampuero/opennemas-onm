<amp-consent id="consent" layout="nodisplay" type="opencmp">
    <script type="application/json">
        {
            "postPromptUI": "opencmp-consent-prompt-ui",
            "sandbox": "allow-top-navigation-by-user-activation allow-popups-to-escape-sandbox",
            "paywallRedirectUrl": CANONICAL_URL,
            "clientConfig": {
                "domain": "{$id}",
                "activationKey": "{$apikey}"
            },
            "uiConfig": { "overlay": true }
        }
    </script>
</amp-consent>
