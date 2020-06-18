<script type="application/ld+json">
  [
    {
      "@context": "http://schema.org",
      "@type": "NewsArticle",
      "mainEntityOfPage": {
        "@type": "WebPage",
        "@id": "{$url}"
      },
      "headline": "{$title|escape:'html'}",
      "author": {
        "@type": "Person",
        "name": "{$author|escape:'html'}"
      },
      "datePublished": "{format_date date=$content->created format="y-MM-dd HH:mm:ss" type="custom"}",
      "dateModified": "{format_date date=$content->changed format="y-MM-dd HH:mm:ss" type="custom"}",
      "articleSection": "{$category->title|escape:'html'}",
      "keywords": "{$keywords|escape:'html'}",
      "url": "{$url}",
      "wordCount": {$wordCount},
      "description": "{$description|escape:'html'}",
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
      {if !empty($image)}
        , "image": {
            "@type": "ImageObject",
            "url": "{get_url item=$image absolute=true}",
            "height": {$image->height},
            "width": {$image->width}
          }
        },
        {include file='./structured_image_data.tpl'}
      {else}
        }
      {/if}
  ]
</script>
