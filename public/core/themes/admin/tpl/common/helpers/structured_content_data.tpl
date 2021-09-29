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
    },
    "datePublished": "{format_date date=$content->created format="yyyy-MM-dd HH:mm:ss" type="custom"}",
    "dateModified": "{format_date date=$content->changed format="yyyy-MM-dd HH:mm:ss" type="custom"}",
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
          "url": "{$logo['url']}",
          "width": {$logo['width']},
          "height": {$logo['height']}
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
          "uploadDate": "{format_date date=get_publication_date(get_featured_media($content, 'inner')) format="yyyy-MM-dd HH:mm:ss" type="custom"}",
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
