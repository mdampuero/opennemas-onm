<script type="application/ld+json">
  [
    {
      "@context": "http://schema.org",
      "@type": "ImageGallery",
      "description": "{$description|escape:'html'}",
      "keywords": "{$keywords|escape:'html'}",
      "datePublished": "{format_date date=$created format="y-MM-dd HH:mm:ss" type="custom"}",
      "dateModified": "{format_date date=$changed format="y-MM-dd HH:mm:ss" type="custom"}",
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
      {if !empty($photos)}
        , "associatedMedia": [
          {foreach from=$photos item=photo name="photos"}
            {
              "url": "{get_url item=$photo absolute=true}",
              "height": {$photo->height},
              "width": {$photo->width}
            }{if !$smarty.foreach.photos.last},{/if}
          {/foreach}
        ]},
        {foreach from=$photos item=$image name="photos"}
          {include file='./structured_image_data.tpl'}{if !$smarty.foreach.photos.last},{/if}
        {/foreach}
      {else}
        }
      {/if}
  ]
</script>
