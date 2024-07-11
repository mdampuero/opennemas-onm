<@script type="application/ld+json">
{
  "@context": "http://schema.org/",
  "@type": "LiveBlogPosting",
  "@id": "{$url}",
  "url":"{$url}",
  "headline":"{$title|replace:'\\':''|escape:'htmlall'}",
  "description":"{$description|replace:'\\':''|escape:'htmlall'}",
  "coverageStartTime":"{format_date date=$content->coverage_start_time format="yyyy-MM-dd'T'HH:mm:ssXXX" type="custom"}",
  "coverageEndTime":"{format_date date=$content->coverage_end_time format="yyyy-MM-dd'T'HH:mm:ssXXX" type="custom"}",
  "mainEntityOfPage": {
    "@type": "WebPage",
    "@id": "{$url}"
  },
  "publisher": {
    "@type": "Organization",
    "name": "{$siteName}",
    "logo": {
        "@type": "ImageObject",
        "url": "{$logo['url']}",
        "width": "{$logo['width']}",
        "height": "{$logo['height']}"
    },
    "url": "{$siteUrl}"
  },
  "liveBlogUpdate":[
    {foreach from=$content->live_blog_updates item=update name=update}
    {
      "@type":"BlogPosting",
      "headline":"{$update['title']|replace:'\\':''|escape:'htmlall'}",
      "datePublished":"{format_date date=$update['created'] format="yyyy-MM-dd'T'HH:mm:ssXXX" type="custom"}",
      "dateModified":"{format_date date=$update['modified'] format="yyyy-MM-dd'T'HH:mm:ssXXX" type="custom"}",
      "author": {
        "@type": "Person",
        "name": "{$author|replace:'\\':''|escape:'htmlall'}"
        {if has_author_url($content)}
          , "url": "{$app.instance->getBaseUrl()}{get_author_url($content)}"
        {/if}
      },
      "mainEntityOfPage": {
        "@type": "WebPage",
        "@id": "{$url}"
      },
      "publisher": {
        "@type": "Organization",
        "name": "{$siteName}",
        "logo": {
            "@type": "ImageObject",
            "url": "{$logo['url']}",
            "width": "{$logo['width']}",
            "height": "{$logo['height']}"
        },
        "url": "{$siteUrl}"
      },
      "articleBody": "{get_update_body($update)|replace:'\\':''|escape:'htmlall'}",
      {if has_update_image($update)}
      "image": {
        "@type": "ImageObject",
        "url": "{get_photo_path(get_update_image($update), null, [], true)}",
        "height": {get_photo_height(get_update_image($update))},
        "width": {get_photo_width(get_update_image($update))}
      },
      {else}
      "image":[],
      {/if}
      "url": "{$siteUrl}"
    }{if !$smarty.foreach.update.last},{/if}
    {/foreach}
  ]
}
</script>
