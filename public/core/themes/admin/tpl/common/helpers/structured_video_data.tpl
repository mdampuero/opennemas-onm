<script type="application/ld+json">
  {
    "@context": "http://schema.org/",
    "@type": "VideoObject",
    "author": "{$author|escape:'html'}",
    "name": "{$video->title|escape:'html'}",
    "description": "{$description|escape:'html'}",
    "@id": "{$url}",
    "uploadDate": "{format_date date=$video->created format="y-MM-dd HH:mm:ss" type="custom"}",
    "thumbnailUrl": "{$video->thumb}",
    "contentUrl": "{$url}",
    "keywords": "{$videokeywords|escape:'html'}",
    "publisher": {
      "@type": "Organization",
      "name": "{$sitename|escape:'html'}",
      "logo": {
        "@type": "ImageObject",
        "url": "{$logo['url']}",
        "width": {$logo['width']},
        "height": {$logo['height']}
      },
      "url": "{$siteurl}"
    }
  }
</script>
