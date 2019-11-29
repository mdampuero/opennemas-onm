<script type="application/ld+json">[
  {
    "@context" : "http://schema.org",
    "@type" : "NewsArticle",
    "mainEntityOfPage":
    {
      "@type": "WebPage",
      "@id": "{$url}"
    },
    "headline": "{$title}",
    "author" :
    {
      "@type" : "Person",
      "name" : "{$author}"
    },
    "datePublished" : "{$created}",
    "dateModified": "{$changed}",
    "articleSection" : "{$category->title|escape:'html'}",
    "keywords": "{$keywords|escape:'html'}",
    "url": "{$url}",
    "wordCount": "{$wordCount}",
    "description": "{$summary}",
    "publisher" :
    {
      "@type" : "Organization",
      "name" : "{$sitename|escape:'html'}",
      "logo":
      {
          "@type": "ImageObject",
          "url": "{$logo['url']}",
          "width": "{$logo['width']}",
          "height": "{$logo['height']}"
      },
      "url": "{$siteurl}"
    }
    {if !empty($image)}
    ,"image":
        {include file='./structured_image_data.tpl'}
    {/if}
  }
]</script>
