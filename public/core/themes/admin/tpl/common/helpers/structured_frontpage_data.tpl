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
    {if !empty($category)}
      , {
        "@type": "ListItem",
        "item": {
          "@id": "{$url}",
          "name": "{$category->title}"
        },
        "position": 2
      }]},
    {elseif $app['extension'] === 'video'}
      , {
        "@type": "ListItem",
        "item": {
          "@id": "{$url}",
          "name": "{t}Videos{/t}"
        },
        "position": 2
      }]},
    {elseif $app['extension'] === 'album'}
      , {
        "@type": "ListItem",
        "item": {
          "@id": "{$url}",
          "name": "{t}Albums{/t}"
        },
        "position": 2
      }]},
    {elseif $app['extension'] === 'opinion'}
      , {
        "@type": "ListItem",
        "item": {
          "@id": "{$url}",
          "name": "{t}Opinions{/t}"
        },
        "position": 2
      }]},
    {elseif $app['extension'] === 'staticpage'}
      , {
        "@type": "ListItem",
        "item": {
          "@id": "{$url}",
          "name": "{$title|escape:'html'}"
        },
        "position": 2
      }]},
    {else}
      ]},
    {/if}
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
      "description": "{$category->description|default:$category->title}",
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
