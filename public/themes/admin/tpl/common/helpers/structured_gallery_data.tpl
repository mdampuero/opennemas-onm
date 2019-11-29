<script type="application/ld+json">[
{
  "@context":"http://schema.org",
  "@type":"ImageGallery",
  "description": "{$summary}",
  "keywords": "{$keywords|escape:'html'}",
  "datePublished" : "{$created}",
  "dateModified": "{$changed}",
  "mainEntityOfPage":
  {
      "@type": "WebPage",
      "@id": "{$url}"
  },
  "headline": "{$title}",
  "url": "{$url}",
  "author" :
  {
      "@type" : "Person",
      "name" : "{$author}"
  },
  {if !empty($image)}
    "primaryImageOfPage":
      {include file='./structured_image_data.tpl'},
  {/if}
  {if !empty($photos)}
      "associatedMedia":
      [
        {foreach from=$photos item=photo name="photos"}
          {
            "url": "{get_url item=$photo absolute=true}",
            "height": "{$photo->height}",
            "width": "{$photo->width}"
          }{if !$smarty.foreach.photos.last},{/if}
        {/foreach}
      ]
},
  {/if}
  {foreach from=$photos item=$image name="photos"}
    {include file='./structured_image_data.tpl'}{if !$smarty.foreach.photos.last},{/if}
  {/foreach}
]</script>
