<script type="application/ld+json">
  [{
    "@context": "http://schema.org/",
    "@type": "BreadcrumbList",
    "itemListElement": [{
      "@type": "ListItem",
      "item": {
        "@id": "{$siteUrl}",
        "name": "{$siteName}"
      },
      "position": 1
    }
    {if !empty($category) || (!empty($app['extension']) && $app['extension'] !== 'frontpages')}
      , {
        "@type": "ListItem",
        "item": {
          "@id": "{$url}",
          "name": "{if !empty($title)}{$title}{elseif !empty($category)}{$category->title}{elseif $app['extension'] === 'video'}{t}Videos{/t}{elseif $app['extension'] === 'album'}{t}Albums{/t}{elseif $app['extension'] === 'opinion'}{t}Opinions{/t}{elseif $app['extension'] === 'poll'}{t}Polls{/t}{elseif $app['extension'] === 'staticpage'}{$title|escape:'html'}{else}{$app['extension']}{/if}"
        },
        "position": 2
      }
    {/if}
  ]},
  {
    "@context": "http://schema.org",
    "@type": "WebSite",
    "name": "{$siteName}",
    "description": "{$siteDescription}",
    "image": {
      "@type": "ImageObject",
      "url": "{$logo['url']}",
      "width": {$logo['width']},
      "height": {$logo['height']}
    },
    "url": "{$siteUrl}",
    "creator": {
      "@type": "Organization"
    }
  }, {
    "@context": "http://schema.org",
    "@type": "WebPage",
    {if !empty($category)}
      "name": "{$category->title}",
      "description": "{$category->description|default:$category->title|escape:'html'}",
      "url": "{$url}",
    {else}
      "name": "{$siteName}",
      "description": "{$siteDescription}",
      "url": "{$url}",
    {/if}
    "image": {
      "@type": "ImageObject",
      "url": "{$logo['url']}",
      "width": {$logo['width']},
      "height": {$logo['height']}
    }
  }]
</script>
