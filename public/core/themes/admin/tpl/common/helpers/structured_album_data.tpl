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
    "@type": "ImageGallery",
    "description": "{$description|replace:'\\':''|escape:'htmlall'}",
    "keywords": "{$keywords|replace:'\\':''|escape:'htmlall'}",
    "datePublished": "{format_date date=$content->starttime format="yyyy-MM-dd'T'HH:mm:ssXXX" type="custom"}",
    "dateModified": "{format_date date=$content->changed format="yyyy-MM-dd'T'HH:mm:ssXXX" type="custom"}",
    "mainEntityOfPage": {
        "@type": "WebPage",
        "@id": "{$url}"
    },
    "headline": "{$title|replace:'\\':''|escape:'htmlall'}",
    "url": "{$url}",
    "author": {
        "@type": "Person",
        "name": "{$author|replace:'\\':''|escape:'htmlall'}"
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
