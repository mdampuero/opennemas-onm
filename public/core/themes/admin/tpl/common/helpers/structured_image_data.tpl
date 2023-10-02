{
  "@context": "http://schema.org",
  "@type": "ImageObject",
  "author": "{$author|replace:'\\':''|escape:'htmlall'}",
  "url": "{get_url item=$image absolute=true}",
  "height": {$image->height},
  "width": {$image->width},
  "caption": "{$image->description|replace:'\\':''|escape:'htmlall'}",
  "name": "{$image->title|replace:'\\':''|escape:'htmlall'}"
}
