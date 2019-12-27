<script type="application/ld+json">[
{
  "@context": "http://schema.org/",
  "@type": "VideoObject",
  "author": "{$author}",
  "name": "{$video->title|escape:'html'}",
  "description": "{$summary}",
  "@id": "{$url}",
  "uploadDate": "{$video->created}",
  "thumbnailUrl": "{$video->thumb}",
  "keywords": "{$videokeywords|escape:'html'}",
  "publisher" :
  {
      "@type" : "Organization",
      "name" : "{$sitename}",
      "logo":
      {
          "@type": "ImageObject",
          "url": "{$logo['url']}",
          "width": {$logo['width']},
          "height": {$logo['height']}
      },
      "url": "{$siteurl}"
  }
}
]</script>
