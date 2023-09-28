{include file="common/helpers/structured_organization_data.tpl"}
<@script type="application/ld+json">
  [{
    "@context": "http://schema.org/",
    "@type": "BreadcrumbList",
    "itemListElement": [{
      "@type": "ListItem",
      "item": {
        "@id": "{$siteUrl}",
        "name": "{$siteName|replace:'\\':''|escape:'htmlall'}",
        "@type": "CollectionPage"
      },
      "position": 1
    }
    {if !empty($category)}
      , {
        "@type": "ListItem",
        "item": {
          "@id": "{get_url item=$category absolute=true}",
          "name": "{$category->title|replace:'\\':''|escape:'htmlall'}",
          "@type": "CollectionPage"
        },
        "position": 2
      }, {
        "@type": "ListItem",
        "item": {
          "@id": "{$url}",
          "name": "{$title|replace:'\\':''|escape:'htmlall'}",
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
          "name": "{$title|replace:'\\':''|escape:'htmlall'}",
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
    "headline": "{$title|replace:'\\':''|escape:'htmlall'}",
    "author": {
      "@type": "Person",
      "name": "{$author|replace:'\\':''|escape:'htmlall'}"
      {if has_author_url($content)}
        , "url": "{$app.instance->getBaseUrl()}{get_author_url($content)}"
      {/if}
    },
    "datePublished": "{format_date date=$content->starttime format="yyyy-MM-dd'T'HH:mm:ssXXX" type="custom"}",
    "dateModified": "{format_date date=$content->changed format="yyyy-MM-dd'T'HH:mm:ssXXX" type="custom"}",
    {if !empty($category)}
      "articleSection": "{$category->title|replace:'\\':''|escape:'htmlall'}",
    {elseif $content->content_type_name === 'opinion'}
      "articleSection": "{if is_blog($content)}{t}Blog{/t}{else}{t}Opinion{/t}{/if}",
    {/if}
    "keywords": "{$keywords|replace:'\\':''|escape:'htmlall'}",
    "url": "{$url}",
    "wordCount": {$wordCount},
    "description": "{$description|replace:'\\':''|escape:'htmlall'}",
    "publisher": {
      "@type": "Organization",
      "name": "{$siteName|replace:'\\':''|escape:'htmlall'}",
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
          "name": "{get_title(get_featured_media($content, 'inner'))|replace:'\\':''|escape:'htmlall'}",
          "description": "{get_description(get_featured_media($content, 'inner'))|default:get_title(get_featured_media($content, 'inner'))|replace:'\\':''|escape:'htmlall'}",
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
