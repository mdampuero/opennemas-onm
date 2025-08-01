<script>
  var _PROMETEO_MEDIA_CONFIG = {};
  _PROMETEO_MEDIA_CONFIG = {
    siteId: "{$id}",
    author: "{get_author_name($content)}",
    author_id: "{get_author_id($content)}",
    publish_time: "{if !empty($content)}{format_date date=get_publication_date($content) format='yyyy-MM-dd HH:mm:ss' type='custom'}{/if}",
    article_id: "{get_id($content)}",
    content_type: "{$type}",
    section: "{$section}",
    tags: "{$seoTags}",
    thumbnail_image: "{$imagePath}",
    accesstype: "{$accessType}",
    cuser: "__onm_user"
  }
</script>
