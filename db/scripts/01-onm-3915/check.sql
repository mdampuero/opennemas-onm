SELECT tags.name, tags.id, tags.slug, tags.language_id, contents_tags.content_id FROM `tags` inner join contents_tags on tags.id = contents_tags.tag_id WHERE 1 = 1
