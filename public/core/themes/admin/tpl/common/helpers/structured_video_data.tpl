{include file="common/helpers/structured_organization_data.tpl"}
<@script type="application/ld+json">
  [{
    "@context": "http://schema.org/",
    "@type": "BreadcrumbList",
    "itemListElement": [{
      "@type": "ListItem",
      "item": {
        "@id": "{$siteUrl}",
        "name": "{$siteName|replace:'\\':''|escape:'htmlall'}",
        "@type": "CollectionPage"
      },
      "position": 1
    }
    {if !empty($category)}
      , {
        "@type": "ListItem",
        "item": {
          "@id": "{get_url item=$category absolute=true}",
          "name": "{$category->title|replace:'\\':''|escape:'htmlall'}",
          "@type": "CollectionPage"
        },
        "position": 2
      }, {
        "@type": "ListItem",
        "item": {
          "@id": "{$url}",
          "name": "{$title|replace:'\\':''|escape:'htmlall'}",
          "@type": "ItemPage"
        },
        "position": 3
      }
    {/if}
  ]},
  {
    "@context": "http://schema.org",
    "@type": {if $content->content_type_name === 'opinion'}["NewsArticle", "OpinionNewsArticle"]{else}"NewsArticle"{/if},
    "mainEntityOfPage": {
      "@type": "WebPage",
      "@id": "{$url}"
    },
    "headline": "{$title|replace:'\\':''|escape:'htmlall'}",
    "author": {
      "@type": "Person",
      "name": "{$author|replace:'\\':''|escape:'htmlall'}"
      {if has_author_url($content)}
        , "url": "{$app.instance->getBaseUrl()}{get_author_url($content)}"
      {/if}
    },
    "datePublished": "{format_date date=$content->starttime format="yyyy-MM-dd'T'HH:mm:ssXXX" type="custom"}",
    "dateModified": "{format_date date=$content->changed format="yyyy-MM-dd'T'HH:mm:ssXXX" type="custom"}",
    {if !empty($category)}
      "articleSection": "{$category->title|replace:'\\':''|escape:'htmlall'}",
    {/if}
    "keywords": "{$keywords|replace:'\\':''|escape:'htmlall'}",
    "url": "{$url}",
    "wordCount": {$wordCount},
    "description": "{$description|replace:'\\':''|escape:'htmlall'}",
    "image": {
      "@type": "ImageObject",
      "url": "{get_photo_path(get_video_thumbnail(get_featured_media($content, 'inner')), '', [], true)}",
      "height": {get_photo_height(get_video_thumbnail(get_featured_media($content, 'inner')))|default:360},
      "width": {get_photo_width(get_video_thumbnail(get_featured_media($content, 'inner')))|default:480}
    },
    "publisher": {
      "@type": "Organization",
      "name": "{$siteName|replace:'\\':''|escape:'htmlall'}",
      "logo": {
          "@type": "ImageObject",
          "url": "{$logo['url']}",
          "width": "{$logo['width']}",
          "height": "{$logo['height']}",
      },
      "url": "{$siteUrl}"
    }
  },
  {
    "@context": "http://schema.org/",
    "@type": "VideoObject",
    "author": "{$author|replace:'\\':''|escape:'htmlall'}",
    "name": "{$content->title|replace:'\\':''|escape:'htmlall'}",
    "description": "{$content->description|default:$content->title|replace:'\\':''|escape:'htmlall'}",
    "@id": "{$url}",
    "uploadDate": "{format_date date=$content->starttime format="yyyy-MM-dd'T'HH:mm:ssXXX" type="custom"}",
    "thumbnailUrl": "{get_photo_path(get_video_thumbnail($content), '', [], true)}",
    "contentUrl": "{$url}",
    "keywords": "{$videoKeywords|replace:'\\':''|escape:'htmlall'}",
    "publisher": {
      "@type": "Organization",
      "name": "{$siteName|replace:'\\':''|escape:'htmlall'}",
      "logo": {
        "@type": "ImageObject",
        "url": "{$logo['url']}",
        "width": "{$logo['width']}",
        "height": "{$logo['height']}",
      },
      "url": "{$siteUrl}"
    }
  }]
</script>
