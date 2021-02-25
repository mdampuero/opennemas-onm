<script type="application/ld+json">
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
      }]},
    {else}
      ]},
    {/if}
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
      {if !empty($category)}
        "articleSection": "{$category->title|escape:'html'}",
      {/if}
      "keywords": "{$keywords|escape:'html'}",
      "url": "{$url}",
      "wordCount": {$wordCount},
      "description": "{$description|escape:'html'}",
      "publisher": {
        "@type": "Organization",
        "name": "{$siteName}",
        "logo": {
            "@type": "ImageObject",
            "url": "{$logo['url']}",
            "width": {$logo['width']},
            "height": {$logo['height']}
        },
        "url": "{$siteUrl}"
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
      {elseif !empty($video)}
        , "video": {
            "@type": "VideoObject",
            "name": "{$video->title|escape:'html'}",
            "description": "{$video->description|escape:'html'}",
            "uploadDate": "{format_date date=$video->created format="y-MM-dd HH:mm:ss" type="custom"}",
            "thumbnailUrl": "{$video->url}",
            "contentUrl": "{get_url item=$video absolute=true}"
          }
        , "image": {
            "@type": "ImageObject",
            "url": "{$video->url}",
            "height": {get_photo_height(get_video_thumbnail($video))|default:360},
            "width": {get_photo_width(get_video_thumbnail($video))|default:480}
          }
        }
      {else}
        }
      {/if}
  ]
</script>
