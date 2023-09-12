<amp-analytics type="googleanalytics" config="{$configfile}" data-credentials="include">
    <script type="application/json">
        {
            "vars": {
              "GA4_MEASUREMENT_ID": "{$ga4Id}",
              "GA4_ENDPOINT_HOSTNAME": "www.google-analytics.com",
              "DEFAULT_PAGEVIEW_ENABLED": true,
              "GOOGLE_CONSENT_ENABLED": false,
              "WEBVITALS_TRACKING": false,
              "PERFORMANCE_TIMING_TRACKING": false,
              "SEND_DOUBLECLICK_BEACON": false
            },
            "extraUrlParams": {
                "event__str_sponsored": "0",
                "event__str_ads_enabled": "1",
                "event__str_layout": "{$layout}",
                "event__str_categoria": "{$category}",
                "event__str_subcategoria": "sinsc",
                "event__str_terciariacategoria": "sinct",
                "event__str_createdby": "{$publisherId}",
                "event__str_lastmodify": "{$lastAuthorId}",
                "event__str_firma": "{$publisherName}",
                "event__str_fechapublicacion": "{$pubDate}",
                "event__str_fechadeactualizacion": "{$updated}",
                "event__str_keywords": "{$tagNames}",
                "event__str_noticia_id": "{$contentId}",
                "event__str_mediatype": "{$mediaType}",
                "event__str_seotag": "{$tagSlugs}",
                "event__str_platform": "amp",
                "event__str_ga_page_host": "{$mainDomain}",
                "event__str_user_id": "",
                "event__str_user_logged": "0",
                "event__str_gaid": "",
                "event__str_su": ""
            }
        }
    </script>
</amp-analytics>
