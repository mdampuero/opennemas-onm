<@script type="application/ld+json">
  [{
    "@context": "http://schema.org/",
    "@type": "BreadcrumbList",
    "itemListElement": [{
      "@type": "ListItem",
      "item": {
        "@id": "{$siteUrl}",
        "name": "{$siteName}",
        "@type": "CollectionPage"
      },
      "position": 1
    }
    {if !empty($category)}
      , {
        "@type": "ListItem",
        "item": {
          "@id": "{get_url item=$category absolute=true}",
          "name": "{$category->title}",
          "@type": "CollectionPage"
        },
        "position": 2
      }, {
        "@type": "ListItem",
        "item": {
          "@id": "{$url}",
          "name": "{$title|escape:'html'}",
          "@type": "ItemPage"
        },
        "position": 3
      }
    {elseif $content->content_type_name === 'opinion'}
      , {
        "@type": "ListItem",
        "item": {
          "@id": "{url name=frontend_opinion_frontpage absolute=true}",
          "name": "{t}Opinion{/t}",
          "@type": "CollectionPage"
        },
        "position": 2
      }, {
        "@type": "ListItem",
        "item": {
          "@id": "{$url}",
          "name": "{$title|escape:'html'}",
          "@type": "ItemPage"
        },
        "position": 3
      }
    {/if}
  ]},
  {
    "@context": "http://schema.org",
    "@type": {if $content->content_type_name === 'opinion'}["NewsArticle", "OpinionNewsArticle"]{else}"NewsArticle"{/if},
    "mainEntityOfPage": {
      "@type": "WebPage",
      "@id": "{$url}"
    },
    "headline": "{$title|escape:'html'}",
    "author": {
      "@type": "Person",
      "name": "{$author|escape:'html'}"
      {if has_author_url($content)}
        , "url": "{$app.instance->getBaseUrl()}{get_author_url($content)}"
      {/if}
    },
    "datePublished": "{format_date date=$content->starttime format="yyyy-MM-dd'T'HH:mm:ssXXX" type="custom"}",
    "dateModified": "{format_date date=$content->changed format="yyyy-MM-dd'T'HH:mm:ssXXX" type="custom"}",
    {if !empty($category)}
      "articleSection": "{$category->title|escape:'html'}",
    {/if}
    "keywords": "{$keywords|escape:'html'}",
    "url": "{$url}",
    "wordCount": {$wordCount},
    "description": "{$description|escape:'html'}",
    "publisher": {
      "@type": "Organization",
      "name": "{$siteName}",
      "logo": {
          "@type": "ImageObject",
          "url": "{$logo}"
      },
      "url": "{$siteUrl}"
    }
    {if get_type(get_featured_media($content, 'inner')) === 'photo'}
      , "image": {
          "@type": "ImageObject",
          "url": "{get_photo_path(get_featured_media($content, 'inner'), null, [], true)}",
          "height": {get_photo_height(get_featured_media($content, 'inner'))},
          "width": {get_photo_width(get_featured_media($content, 'inner'))}
        }
      },
      {include file='./structured_image_data.tpl' image=get_featured_media($content, 'inner')}
    {elseif get_type(get_featured_media($content, 'inner')) === 'video'}
      , "video": {
          "@type": "VideoObject",
          "name": "{get_title(get_featured_media($content, 'inner'))|escape:'html'}",
          "description": "{get_description(get_featured_media($content, 'inner'))|default:get_title(get_featured_media($content, 'inner'))|escape:'html'}",
          "uploadDate": "{format_date date=get_publication_date(get_featured_media($content, 'inner')) format="yyyy-MM-dd'T'HH:mm:ssXXX" type="custom"}",
          "thumbnailUrl": "{get_photo_path(get_video_thumbnail(get_featured_media($content, 'inner')), '', [], true)}",
          "contentUrl": "{get_url item=get_featured_media($content, 'inner') absolute=true}"
        }
      , "image": {
          "@type": "ImageObject",
          "url": "{get_photo_path(get_video_thumbnail(get_featured_media($content, 'inner')), '', [], true)}",
          "height": {get_photo_height(get_video_thumbnail(get_featured_media($content, 'inner')))|default:360},
          "width": {get_photo_width(get_video_thumbnail(get_featured_media($content, 'inner')))|default:480}
        }
      }
    {else}
      }
    {/if}
  ]
</script>

<@script type="application/ld+json">
{
  "@context": "http://schema.org/",
  "@type": "LiveBlogPosting",
  "@id": "{$siteUrl}",
  "url":"{$url}",
  "coverageStartTime":"{format_date date=$content->coverage_start_time format="yyyy-MM-dd'T'HH:mm:ssXXX" type="custom"}",
  "coverageEndTime":"{format_date date=$content->coverage_end_time format="yyyy-MM-dd'T'HH:mm:ssXXX" type="custom"}",
  "headline":"{$title|escape:'html'}",
  "description":"{$description|escape:'html'}",
  "articleBody":"{$description|escape:'html'}",
  "mainEntityOfPage": {
    "@type": "WebPage",
    "@id": "{$url}"
  },
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
      },
  {/if}
  "about":{
    "@type":"Event",
    "eventAttendanceMode":"OnlineEventAttendanceMode",
    "startDate":"{format_date date=$content->starttime format="yyyy-MM-dd'T'HH:mm:ssXXX" type="custom"}",
    "name":"{$title|escape:'html'}",
    "description":"{$description|escape:'html'}",
    "endDate":"{format_date date=$content->endtime format="yyyy-MM-dd'T'HH:mm:ssXXX" type="custom"}",
    "location": {
      "@type": "VirtualLocation",
      "url": "{$url}"
      },
    "offers": {
      "@type":"Offer",
      "availability":"https://schema.org/OnlineOnly",
      "price":"0.00",
      "priceCurrency":"EUR",
      "validFrom":"{format_date date=$content->starttime format="yyyy-MM-dd'T'HH:mm:ssXXX" type="custom"}",
      "url":"{$url}"
      }
    ,"organizer":
      {
        "@type":"Organization",
        "name":"{$siteName}",
        "url":"{$app.instance->getBaseUrl()}"
      }
    ,"performer":
      {
        "@type":"Organization",
        "name":"{$siteName}",
        "url":"{$app.instance->getBaseUrl()}"
      }
    {if get_type(get_featured_media($content, 'inner')) === 'photo'}
      ,"image": {include file='./structured_image_data.tpl' image=get_featured_media($content, 'inner')}
    {/if}
    ,"eventStatus":"https:\/\/schema.org\/EventScheduled"
  },

  "liveBlogUpdate":[
    {foreach from=$content->live_blog_updates item=update name=update}
    {
      "@type":"BlogPosting",
      "headline":"{$update['title']|escape:'html'}",
      "datePublished":"{format_date date=$update['created'] format="yyyy-MM-dd'T'HH:mm:ssXXX" type="custom"}",
      "dateModified":"{format_date date=$update['modified'] format="yyyy-MM-dd'T'HH:mm:ssXXX" type="custom"}",
      "mainEntityOfPage": {
        "@type": "WebPage",
        "@id": "{$url}"
      },

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
      "articleBody": "{get_update_body($update)|escape:'html'}",
      {if has_update_image($update)}
      "image": {
        "@type": "ImageObject",
        "url": "{get_photo_path(get_update_image($update), null, [], true)}",
        "height": {get_photo_height(get_update_image($update))},
        "width": {get_photo_width(get_update_image($update))}
      },
      {elseif get_type(get_featured_media($content, 'inner')) === 'photo'}
      "image": {
        "@type": "ImageObject",
        "url": "{get_photo_path(get_featured_media($content, 'inner'), null, [], true)}",
        "height": {get_photo_height(get_featured_media($content, 'inner'))},
        "width": {get_photo_width(get_featured_media($content, 'inner'))}
      },
      {/if}
      "url": "{$siteUrl}"
    }{if !$smarty.foreach.update.last},{/if}
    {/foreach}
  ]
}
</script>
