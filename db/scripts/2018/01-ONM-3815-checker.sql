SELECT tags.name, tags.tag_id, tags.slug, tags.language_id, contents_tags.content_id FROM `tags` inner join contents_tags on tags.tag_id = contents_tags.tag_id WHERE 1 = 1
