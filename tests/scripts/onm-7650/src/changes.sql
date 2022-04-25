SET names 'latin1';
UPDATE menu_items SET 
link_name = REPLACE(link_name,'a:1:{s:5:\"es_ES\";s:10:\"actualidad\";}','actualidad'),
link_name = REPLACE(link_name,'a:1:{s:5:\"es_ES\";s:13:\"quienes-somos\";}','quienes-somos'),
link_name = REPLACE(link_name,'a:1:{s:5:\"es_ES\";s:8:\"economia\";}','economia'),
link_name = REPLACE(link_name,'a:1:{s:5:\"es_ES\";s:8:\"politica\";}','politica'),
link_name = REPLACE(link_name,'a:1:{s:5:\"es_ES\";s:10:\"publicidad\";}','publicidad'),
link_name = REPLACE(link_name,'a:1:{s:5:\"es_ES\";s:8:\"sociedad\";}','sociedad'),
link_name = REPLACE(link_name,'a:1:{s:5:\"es_ES\";s:13:\"internacional\";}','internacional'),
link_name = REPLACE(link_name,'a:1:{s:5:\"es_ES\";s:18:\"condiciones-de-uso\";}','condiciones-de-uso'),
link_name = REPLACE(link_name,'a:1:{s:5:\"es_ES\";s:10:\"tecnologia\";}','tecnologia'),
link_name = REPLACE(link_name,'a:1:{s:5:\"es_ES\";s:22:\"politica-de-privacidad\";}','politica-de-privacidad'),
link_name = REPLACE(link_name,'a:1:{s:5:\"es_ES\";s:7:\"ciencia\";}','ciencia'),
link_name = REPLACE(link_name,'a:1:{s:5:\"es_ES\";s:8:\"opinion/\";}','opinion'),
link_name = REPLACE(link_name,'a:1:{s:5:\"es_ES\";s:7:\"cultura\";}','cultura'),
link_name = REPLACE(link_name,'a:1:{s:5:\"es_ES\";s:8:\"deportes\";}','deportes'),
link_name = REPLACE(link_name,'a:1:{s:5:\"es_ES\";s:6:\"album/\";}','album'),
link_name = REPLACE(link_name,'a:1:{s:5:\"es_ES\";s:8:\"sociedad\";}','sociedad'),
link_name = REPLACE(link_name,'a:1:{s:5:\"es_ES\";s:6:\"video/\";}','video')
WHERE type IN ('blog-category','internal','static');

UPDATE menu_items 
SET title = 'Actualidad'
WHERE type IN ('blog-category','internal','static') AND title =  'a:1:{s:5:\"es_ES\";s:10:\"Actualidad\";}';
UPDATE menu_items 
SET title = 'Publicidad'
WHERE type IN ('blog-category','internal','static') AND title =  'a:1:{s:5:\"es_ES\";s:10:\"Publicidad\";}';
UPDATE menu_items 
SET title = 'Sociedad'
WHERE type IN ('blog-category','internal','static') AND title =  'a:1:{s:5:\"es_ES\";s:8:\"Sociedad\";}';
UPDATE menu_items 
SET title = 'Internacional'
WHERE type IN ('blog-category','internal','static') AND title =  'a:1:{s:5:\"es_ES\";s:13:\"Internacional\";}';
UPDATE menu_items 
SET title = 'Aviso Legal'
WHERE type IN ('blog-category','internal','static') AND title =  'a:1:{s:5:\"es_ES\";s:11:\"Aviso Legal\";}';
UPDATE menu_items 
SET title = 'Ciencia'
WHERE type IN ('blog-category','internal','static') AND title =  'a:1:{s:5:\"es_ES\";s:7:\"Ciencia\";}';
UPDATE menu_items 
SET title = 'Cultura'
WHERE type IN ('blog-category','internal','static') AND title =  'a:1:{s:5:\"es_ES\";s:7:\"Cultura\";}';
UPDATE menu_items 
SET title = 'Deportes'
WHERE type IN ('blog-category','internal','static') AND title =  'a:1:{s:5:\"es_ES\";s:8:\"Deportes\";}';
UPDATE menu_items 
SET title = 'Media'
WHERE type IN ('blog-category','internal','static') AND title =  'a:1:{s:5:\"es_ES\";s:5:\"Media\";}';
UPDATE menu_items 
SET title = 'Quiénes Somos'
WHERE type IN ('blog-category','internal','static') AND title =  'a:1:{s:5:\"es_ES\";s:14:\"Quiénes Somos\";}';
UPDATE menu_items 
SET title =  'Economía'
WHERE type IN ('blog-category','internal','static') AND title = 'a:1:{s:5:"es_ES";s:9:"Economía";}';
UPDATE menu_items 
SET title =  'Política'
WHERE type IN ('blog-category','internal','static') AND title = 'a:1:{s:5:\"es_ES\";s:9:\"Política\";}';
UPDATE menu_items 
SET title = 'Tecnología'
WHERE type IN ('blog-category','internal','static') AND title =  'a:1:{s:5:\"es_ES\";s:11:\"Tecnología\";}';
UPDATE menu_items 
SET title =  'Política de privacidad'
WHERE type IN ('blog-category','internal','static') AND title =  'a:1:{s:5:\"es_ES\";s:23:\"Política de privacidad\";}';
UPDATE menu_items 
SET title =  'Opinión'
WHERE type IN ('blog-category','internal','static') AND title =  'a:1:{s:5:\"es_ES\";s:8:\"Opinión\";}';
UPDATE menu_items 
SET title =  'Galerías'
WHERE type IN ('blog-category','internal','static') AND title =  'a:1:{s:5:\"es_ES\";s:9:\"Galerías\";}';
UPDATE menu_items 
SET title =  'Vídeos'
WHERE type IN ('blog-category','internal','static') AND title =  'a:1:{s:5:\"es_ES\";s:7:\"Vídeos\";}';
