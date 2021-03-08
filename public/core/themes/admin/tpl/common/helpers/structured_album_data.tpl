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
    {if !empty($image)}
      , "primaryImageOfPage":
        {include file='./structured_image_data.tpl'}
    {/if}
    {if !empty($content->photos)}
      , "associatedMedia": [
        {foreach $content->photos as $photo}
          {
            "url": "{get_photo_path(get_content($photo.pk_photo, 'Photo'), null, [], true)}",
            "height": {get_photo_height(get_content($photo.pk_photo, 'Photo'))},
            "width": {get_photo_width(get_content($photo.pk_photo, 'Photo'))}
          }{if !$photo@last},{/if}
        {/foreach}
      ]},
      {foreach $content->photos as $photo}
        {include file='./structured_image_data.tpl' image=get_content($photo.pk_photo, 'Photo')}{if !$photo@last},{/if}
      {/foreach}
    {else}
      }
    {/if}
  ]
</script>
