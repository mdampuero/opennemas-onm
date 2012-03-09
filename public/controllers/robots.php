<?php
/**
 * Start up and setup the app
*/
require_once('../bootstrap.php');
header("Content-Type: text/plain");
?>
User-Agent: *
Disallow: /admin/
Allow: /

Disallow: /harming/humans
Disallow: /ignoring/human/orders
Disallow: /harm/to/self

Sitemap: <?php echo SITE_URL; ?>sitemapnews.xml.gz
Sitemap: <?php echo SITE_URL; ?>sitemapweb.xml.gz
