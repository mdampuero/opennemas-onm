<script>
  var _PROMETEO_MEDIA_CONFIG = {};
  _PROMETEO_MEDIA_CONFIG = {
    siteId: {$id},
    author: "{get_author_name($content)}",
    author_id: "{get_author_id($content)}",
    publish_time: "{format_date date=get_publication_date($content) format='yyyy-MM-dd HH:mm:ss' type='custom'}",
    article_id: "{get_id($content)}",
    content_type: "{$type}",
    section: "{get_category_slug($content)}",
    tags: "{$seoTags}",
    thumbnail_image: "{get_photo_path(get_featured_media($content, 'inner'), null, [], true)}",
    cuser: "__onm_user"
  }
</script>
