<script type="application/ld+json">
  [
    {
      "@context": "http://schema.org",
      "@type": "ImageGallery",
      "description": "{$description|escape:'html'}",
      "keywords": "{$keywords|escape:'html'}",
      "datePublished": "{format_date date=$content->created format="y-MM-dd HH:mm:ss" type="custom"}",
      "dateModified": "{format_date date=$content->changed format="y-MM-dd HH:mm:ss" type="custom"}",
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
      {if has_album_photos($content)}
        , "associatedMedia": [
          {foreach get_album_photos($content) as $photo}
            {
              "url": "{get_photo_path($photo, null, [], true)}",
              "height": {get_photo_height($photo)},
              "width": {get_photo_width($photo)}
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
