SELECT frontpages.id, frontpages.name, frontpage_versions.id, frontpage_versions.name, frontpage_versions.type,
        frontpage_versions.frontpage_id, frontpage_versions.created, frontpage_versions.publish_date,
        frontpage_versions.params FROM `frontpages`
    INNER JOIN frontpage_versions ON frontpages.id = frontpage_versions.frontpage_id
    INNER JOIN content_positions ON frontpage_versions.id = content_positions.frontpage_version_id
    WHERE 1 LIMIT 1
