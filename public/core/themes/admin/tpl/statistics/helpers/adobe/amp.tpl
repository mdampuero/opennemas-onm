<amp-analytics type="adobeanalytics_nativeConfig">
  <script type="application/json">
      {
          "requests": {
              "base": "{$baseFile}",
              "iframeMessage": "{literal}${base}?sponsored=${sponsored}&ads_enabled=${ads_enabled}&layout=${layout}&categoria=${categoria}&subcategoria=${subcategoria}&terciariacategoria=${terciariacategoria}&createdby=${createdby}&lastmodify=${lastmodify}&firma=${firma}&fechapublicacion=${fechapublicacion}&keywords=${keywords}&noticia_id=${noticia_id}&mediatype=${mediatype}&seotag=${seotag}&platform=${platform}&gaid=${gaid}&su=${su}&fechadeactualizacion=${fechadeactualizacion}&title=${title}&amp_request=${amp_request}&page_url=${page_url}&page_url_qs=${page_url_qs}&page_entry_referrer=${page_entry_referrer}&page_referrer=${page_referrer}&ga_page_host=${ga_page_host}&user_consent_policy=${user_consent_policy}{/literal}"
          },
          "vars": {
                "sponsored": "0",
                "ads_enabled": "1",
                "layout": "{$layout}",
                "categoria": "{get_category_slug($content)}",
                "subcategoria": "sinsc",
                "terciariacategoria": "sinct",
                "createdby": "{get_author_id($content)}",
                "lastmodify": "{$lastAuthorId}",
                "firma": "{get_author_name($content)}",
                "fechapublicacion": "{format_date date=$content->starttime format="YYYYMMdd" type="custom"}",
                "keywords": "{$tagNames}",
                "noticia_id": "{get_id($content)}",
                "mediatype": "{$mediaType}",
                "seotag": "{$tagSlugs}",
                "platform": "amp",
                "gaid": "",
                "su": "",
                "fechadeactualizacion": "{format_date date=$content->changed format="YYYYMMdd" type="custom"}",
                "title": "{get_title($content)}",
                "amp_request": "{literal}${sourceUrl}{/literal}",
                "page_url": "{$canonical}",
                "page_url_qs": "{literal}${sourceUrl}{/literal}",
                "page_entry_referrer": "{literal}${externalReferrer}{/literal}",
                "page_referrer": "{literal}${documentReferrer}{/literal}",
                "ga_page_host": "{literal}${ampdocHostname}{/literal}",
                "user_consent_policy": "{literal}$IF($EQUALS(${consentState}, sufficient), accept)$IF($EQUALS(${consentState}, insufficient), deny)$IF($EQUALS(${consentState}, unknown), no choice)$IF($EQUALS(${consentState}, ), sin datos){/literal}"
          }
      }
  </script>
</amp-analytics>
