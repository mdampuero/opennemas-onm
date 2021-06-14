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
      }
    {/if}
  ]},
  {
    "@context": "http://schema.org",
    "@type": "ImageGallery",
    "description": "{$description|escape:'html'}",
    "keywords": "{$keywords|escape:'html'}",
    "datePublished": "{format_date date=$content->created format="yyyy-MM-dd HH:mm:ss" type="custom"}",
    "dateModified": "{format_date date=$content->changed format="yyyy-MM-dd HH:mm:ss" type="custom"}",
    "mainEntityOfPage": {
        "@type": "WebPage",
        "@id": "{$url}"
    },
    "headline": "{$title|escape:'html'}",
    "url": "{$url}",
    "author": {
        "@type": "Person",
        "name": "{$author|escape:'html'}"
    }
    {if has_featured_media($content, 'frontpage')}
      , "primaryImageOfPage":
        {include file='./structured_image_data.tpl' image=get_featured_media($content, 'frontpage')}
    {/if}
    {if has_album_photos($content)}
      , "associatedMedia": [
        {foreach get_album_photos($content) as $photo}
          {
            "url": "{get_photo_path(get_content($photo, 'Photo'), null, [], true)}",
            "height": {get_photo_height($photo, 'Photo')},
            "width": {get_photo_width($photo, 'Photo')}
          }{if !$photo@last},{/if}
        {/foreach}
      ]},
      {foreach get_album_photos($content) as $photo}
        {include file='./structured_image_data.tpl' image=get_content($photo, 'Photo')}{if !$photo@last},{/if}
      {/foreach}
    {else}
      }
    {/if}
  ]
</script>
