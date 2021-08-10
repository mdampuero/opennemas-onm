<@script type="application/ld+json">
  [{
    "@context": "http://schema.org/",
    "@type": "BreadcrumbList",
    "itemListElement": [{
      "@type": "ListItem",
      "item": {
        "@id": "{$siteUrl}",
        "name": "{$siteName}",
        "@type": "CollectionPage"
      },
      "position": 1
    }
    {if !empty($category)}
      , {
        "@type": "ListItem",
        "item": {
          "@id": "{get_url item=$category absolute=true}",
          "name": "{$category->title}",
          "@type": "CollectionPage"
        },
        "position": 2
      }, {
        "@type": "ListItem",
        "item": {
          "@id": "{$url}",
          "name": "{$title|escape:'html'}",
          "@type": "ItemPage"
        },
        "position": 3
      }
    {/if}
  ]},
  {
    "@context": "http://schema.org/",
    "@type": "VideoObject",
    "author": "{$author|escape:'html'}",
    "name": "{$video->title|escape:'html'}",
    "description": "{$video->description|default:$video->title|escape:'html'}",
    "@id": "{$url}",
    "uploadDate": "{format_date date=$video->created format="yyyy-MM-dd HH:mm:ss" type="custom"}",
    "thumbnailUrl": "{get_photo_path(get_video_thumbnail($video), '', [], true)}",
    "contentUrl": "{$url}",
    "keywords": "{$videoKeywords|escape:'html'}",
    "publisher": {
      "@type": "Organization",
      "name": "{$siteName|escape:'html'}",
      "logo": {
        "@type": "ImageObject",
        "url": "{$logo['url']}",
        "width": {$logo['width']},
        "height": {$logo['height']}
      },
      "url": "{$siteUrl}"
    }
  }]
</script>
