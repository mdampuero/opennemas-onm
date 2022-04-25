SET names 'latin1';
SET @countTitle = (SELECT COUNT(title) FROM menu_items WHERE title in
(
    'a:1:{s:5:\"es_ES\";s:14:\"Quiénes Somos\";}',
    'a:1:{s:5:\"es_ES\";s:9:\"Economía\";}',
    'a:1:{s:5:\"es_ES\";s:9:\"Política\";}',
    'a:1:{s:5:\"es_ES\";s:11:\"Tecnología\";}',
    'a:1:{s:5:\"es_ES\";s:23:\"Política de privacidad\";}',
    'a:1:{s:5:\"es_ES\";s:8:\"Opinión\";}',
    'a:1:{s:5:\"es_ES\";s:9:\"Galerías\";}',
    'a:1:{s:5:\"es_ES\";s:7:\"Vídeos\";}',
    'a:1:{s:5:\"es_ES\";s:10:\"Actualidad\";}',
    'a:1:{s:5:\"es_ES\";s:10:\"Publicidad\";}',
    'a:1:{s:5:\"es_ES\";s:8:\"Sociedad\";}',
    'a:1:{s:5:\"es_ES\";s:13:\"Internacional\";}',
    'a:1:{s:5:\"es_ES\";s:11:\"Aviso Legal\";}',
    'a:1:{s:5:\"es_ES\";s:7:\"Ciencia\";}',
    'a:1:{s:5:\"es_ES\";s:7:\"Cultura\";}',
    'a:1:{s:5:\"es_ES\";s:8:\"Deportes\";}',
    'a:1:{s:5:\"es_ES\";s:5:\"Media\";}'
) AND type IN ('blog-category','internal','static'));
SET @countLink = (SELECT COUNT(link_name) FROM menu_items WHERE link_name in
(
    'a:1:{s:5:\"es_ES\";s:10:\"actualidad\";}',
    'a:1:{s:5:\"es_ES\";s:14:\"quienes-somos\";}',
    'a:1:{s:5:\"es_ES\";s:9:\"economia\";}',
    'a:1:{s:5:\"es_ES\";s:9:\"politica\";}',
    'a:1:{s:5:\"es_ES\";s:10:\"publicidad\";}',
    'a:1:{s:5:\"es_ES\";s:8:\"sociedad\";}',
    'a:1:{s:5:\"es_ES\";s:13:\"internacional\";}',
    'a:1:{s:5:\"es_ES\";s:11:\"condiciones-de-uso\";}',
    'a:1:{s:5:\"es_ES\";s:11:\"tecnologia\";}',
    'a:1:{s:5:\"es_ES\";s:23:\"politica-de-privacidad\";}',
    'a:1:{s:5:\"es_ES\";s:7:\"ciencia\";}',
    'a:1:{s:5:\"es_ES\";s:8:\"opinion\";}',
    'a:1:{s:5:\"es_ES\";s:7:\"cultura\";}',
    'a:1:{s:5:\"es_ES\";s:8:\"deportes\";}',
    'a:1:{s:5:\"es_ES\";s:5:\"album\";}',
    'a:1:{s:5:\"es_ES\";s:7:\"video/\";}'
)
AND type IN ('blog-category','internal','static'));

SELECT IF (@countTitle + @countLink > 0, 'FAIL','OK') as result;
