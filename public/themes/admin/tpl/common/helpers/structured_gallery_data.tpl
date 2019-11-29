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
      {include file='./structured_associated_media_data.tpl'}},
  {/if}
  {foreach from=$photos item=$image name="photos"}
    {include file='./structured_image_data.tpl'}{if !$smarty.foreach.photos.last},{/if}
  {/foreach}
]</script>
