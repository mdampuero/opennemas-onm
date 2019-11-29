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
