//TODO: Falta imagen
<@script type="application/ld+json">
{
  "@context": "http://schema.org/",
  "@type": "LiveBlogPosting",
  "@id": "{$siteUrl}",
  "coverageStartTime":"{format_date date=$content->coverage_start_time format="yyyy-MM-dd'T'HH:mm:ssXXX" type="custom"}",
  "coverageEndTime":"{format_date date=$content->coverage_end_time format="yyyy-MM-dd'T'HH:mm:ssXXX" type="custom"}",
  "headline":"{$title|escape:'html'}",
  "description":"{$description|escape:'html'}",
  "articleBody":"{$description|escape:'html'}",
  {if !empty($category)}
  "articleSection": "{$category->title|escape:'html'}",
  {/if}
  "datePublished": "{format_date date=$content->starttime format="yyyy-MM-dd'T'HH:mm:ssXXX" type="custom"}",
  "dateModified": "{format_date date=$content->changed format="yyyy-MM-dd'T'HH:mm:ssXXX" type="custom"}",
  "publisher": {
    "@type": "Organization",
    "name": "{$siteName}",
    "logo": {
        "@type": "ImageObject",
        "url": "{$logo}"
    },
    "url": "{$siteUrl}"
  },
  "author": {
    "@type": "Person",
    "name": "{$author|escape:'html'}"
    {if has_author_url($content)}
      , "url": "{$app.instance->getBaseUrl()}{get_author_url($content)}"
    {/if}
  },
  {if get_type(get_featured_media($content, 'inner')) === 'photo'}
    "image": {
        "@type": "ImageObject",
        "url": "{get_photo_path(get_featured_media($content, 'inner'), null, [], true)}",
        "height": {get_photo_height(get_featured_media($content, 'inner'))},
        "width": {get_photo_width(get_featured_media($content, 'inner'))}
      }
    },
    {include file='./structured_image_data.tpl' image=get_featured_media($content, 'inner')}
  {/if}
  "about":{
    "@type":"Event",
    "startDate":"{format_date date=$content->starttime format="yyyy-MM-dd'T'HH:mm:ssXXX" type="custom"}",
    "name":"{$title|escape:'html'}",
    "description":"{$description|escape:'html'}",
    "endDate":"{format_date date=$content->endtime format="yyyy-MM-dd'T'HH:mm:ssXXX" type="custom"}"
  },

  "liveBlogUpdate":[
    {foreach from=$content->live_blog_updates item=update name=update}
    {
      "@type":"BlogPosting",
      "headline":"{$update['title']|escape:'html'}",
      "datePublished":"{format_date date=$update['created'] format="yyyy-MM-dd'T'HH:mm:ssXXX" type="custom"}",
      "dateModified":"{format_date date=$update['modified'] format="yyyy-MM-dd'T'HH:mm:ssXXX" type="custom"}",
      "author": {
        "@type": "Person",
        "name": "{$author|escape:'html'}"
        {if has_author_url($content)}
          , "url": "{$app.instance->getBaseUrl()}{get_author_url($content)}"
        {/if}
      },
      "publisher": {
        "@type": "Organization",
        "name": "{$siteName}",
        "logo": {
            "@type": "ImageObject",
            "url": "{$logo}"
        },
        "url": "{$siteUrl}"
      },
      "articleBody": "{$update['body']|escape:'html'}",
      "url": "{$siteUrl}"
    }{if !$smarty.foreach.update.last},{/if}
    {/foreach}
  ]
}
</script>
