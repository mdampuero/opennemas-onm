{
  "@context": "http://schema.org",
  "@type": "ImageObject",
  "author": "{$author}",
  "url": "{get_url item=$image absolute=true}",
  "height": {$image->height},
  "width": {$image->width},
  "datePublished": {$image->created},
  "caption": "{$image->description|escape:'html'}",
  "name": "{$image->title|escape:'html'}"
}
