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
    {/if}
  ]},
{
  "@context": "http://schema.org",
  "@type": "Event",
  "name": "{$content->title|replace:'\\':''|escape:'htmlall'}",
  "startDate": "{format_date date=$content->event_start_date format="yyyy-MM-dd'T'HH:mm:ssXXX" type="custom"}",
  "endDate": "{format_date date=$content->event_end_date format="yyyy-MM-dd'T'HH:mm:ssXXX" type="custom"}",
  "description": "{$content->description|replace:'\\':''|escape:'htmlall'}",
  "eventStatus": "https://schema.org/EventScheduled",
  "eventAttendanceMode": "https://schema.org/OfflineEventAttendanceMode",
  "location": {
    "@type": "Place",
    "name": "{($content->event_place) ? "{$content->event_place|escape:'htmlall'}" : ""}",
    "address": "{($content->event_address) ? "{$content->event_address}" : ""}"
  },
  "organizer": {
    "@type": "Organization",
    "name": "{($content->event_organizer_name) ? "{$content->event_organizer_name|escape:'htmlall'}" : "{$siteName|replace:'\\':''|escape:'htmlall'}"}",
    "url": "{($content->event_organizer_url) ? "{$content->event_organizer_url}" : "{$siteUrl}"}"
  }
  {if has_featured_media($content, 'frontpage')}
    , "image":
      {include file='./structured_image_data.tpl' image=get_featured_media($content, 'frontpage')}
  {/if}
}]
</script>
