{
  "@context": "http://schema.org",
  "@type": "ImageObject",
  "author": "{$author|escape:'html'}",
  "url": "{get_url item=$image absolute=true}",
  "height": {$image->height},
  "width": {$image->width},
  "datePublished": "{format_date date=$image->starttime format="yyyy-MM-dd HH:mm:ss" type="custom"}",
  "caption": "{$image->description|escape:'html'}",
  "name": "{$image->title|escape:'html'}"
}
