<@script type="application/ld+json">
  [{
    "@context": "https://schema.org",
    "@type": "NewsMediaOrganization",
    "name": "{$siteName}",
    "url": "{$siteUrl}",
    "logo": "{$logo['url']}",
    "sameAs": {$externalServices},
    "potentialAction": {
      "@type": "ReadAction",
      "target": [
        {
          "@type": "EntryPoint",
          "urlTemplate": "{$siteUrl}",
          "inLanguage": "{$languages}",
          "actionPlatform": [
            "http://schema.org/DesktopWebPlatform",
            "http://schema.org/IOSPlatform",
            "http://schema.org/AndroidPlatform"
          ]
        }
      ]
    }
  }]
</script>
