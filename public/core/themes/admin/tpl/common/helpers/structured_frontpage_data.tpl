<@script type="application/ld+json">
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
          "name": "{if !empty($title)}{$title|replace:'\\':''|escape:'htmlall'}{elseif !empty($category)}{$category->title|replace:'\\':''|escape:'htmlall'}{elseif $app['extension'] === 'video'}{t}Videos{/t}{elseif $app['extension'] === 'album'}{t}Albums{/t}{elseif $app['extension'] === 'opinion'}{t}Opinions{/t}{elseif $app['extension'] === 'poll'}{t}Polls{/t}{elseif $app['extension'] === 'staticpage'}{$title|replace:'\\':''|escape:'htmlall'}{else}{$app['extension']|replace:'\\':''|escape:'htmlall'}{/if}"
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
      "url": "{$logo}"
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
      "description": "{$category->description|default:$category->title|replace:'\\':''|escape:'htmlall'}",
      "url": "{$url}",
    {else if !empty($tag)}
      "name": "{$tag->name|replace:'\\':''|escape:'htmlall'}",
      {if $tag->description}
      "description": "{$tag->description|replace:'\\':''|escape:'htmlall'}",
      {else}
      "description": "{t domain=base 1=$tag->name|replace:'\\':''|escape:'htmlall' 2=$siteName}All the latest information about %1 in %2. News, events, reports and opinion articles.{/t}",
      {/if}
      "url": "{$url}",
    {else}
      "name": "{$siteName}",
      "description": "{$siteDescription}",
      "url": "{$url}",
    {/if}
    "image": {
      "@type": "ImageObject",
      "url": "{$logo}"
    }
  }]
</script>
