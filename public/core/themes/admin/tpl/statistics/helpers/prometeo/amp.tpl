<amp-analytics>
  <script type="application/json">
    {
      "requests": {
        "pageview": {literal}"https://www.prometeo-media-service.com/assets/pixel.gif?RANDOM&pcp_sectiond=${categoria_n1}&pcp_sub_section=${categoria_n2}&pcp_category_3=${categoria_n3}&pcp_category_4=${categoria_n4}&tags=${tags}&pcp_content_type=${layout}&curl=${canonicalUrl}&pcp_ai=${article}&pcp_a=${author}&pdp_mt=${title}&pdp_mkw=${keywords}&pdp_mim=${image}&sd_sId=${media_id}&sd_sid=${media_id}&pcp_pt=${publish_time}&pr_client_id=CLIENT_ID(_prometeo)&pdp_hn=${canonicalHostname}&pdp_cpn=${canonicalPath}&originPetition=amp&event_type=pageview&subs_mode=${subs_mode}&subs_period=${subs_period}&subs_id=${subs_id}&subscriptor=${subscriptor}&product_brand=${product_brand}&product_model=${product_model}&product_version=${product_version}&product_name=${product_name}&product_dc=${product_dc}&product_platform=${product_platform}&pdp_edt=${timestamp}&pdp_d=${canonicalHost}&pdp_hp=https:&pdp_cr=${documentReferrer}&pr_geo_country=${ampGeo(ISOCountry)}"{/literal}
      },
      "triggers": {
        "trackPageview": {
          "on": "visible",
          "request": "pageview",
          "vars": {
            "categoria_n1": "{get_category_slug($content)}",
            "categoria_n2": "",
            "categoria_n3": "",
            "categoria_n4": "",
            "layout": "{$type}",
            "article": "{get_id($content)}",
            "publish_time": "{if !empty($content)}{format_date date=get_publication_date($content) format='yyyy-MM-dd HH:mm:ss' type='custom'}{/if}",
            "author": "{get_author_name($content)}",
            "media_id": {$id},
            "title": "{$content->title|escape}",
            "keywords": "{$seoTags}",
            "image": "{$imagePath}",
            "tags": "{$seoTags}",
            "accesstype": "{$accessType}",
            "subs_mode": "",
            "subs_period": "",
            "subs_id": "",
            "subscriptor": "",
            "product_brand": "",
            "product_model": "",
            "product_version": "",
            "product_name": "",
            "product_dc": "",
            "product_platform": ""
          }
        }
      },
      "transport": {
        "beacon": false,
        "xhrpost": false,
        "image": true
      }
    }
  </script>
</amp-analytics>
