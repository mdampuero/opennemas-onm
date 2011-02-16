<?xml version="1.0" encoding="UTF-8"?>
{if preg_match('/sitemap\.php/',$smarty.server.SCRIPT_NAME) && ($smarty.request.action eq "news")}
    <urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9"
            xmlns:n="http://www.google.com/schemas/sitemap-news/0.9">
        {section name=c loop=$opinions}
            <url>
              <loc><![CDATA[http://{$smarty.const.SITE}{$opinions[c].permalink}]]></loc>
              <n:news>
                <n:publication>
                  <n:name><![CDATA[{$smarty.const.SITE_NAME}]]></n:name>
                  <n:language><![CDATA[es]]></n:language>
                </n:publication>
                <n:publication_date><![CDATA[{$opinions[c].changed|date_format:"%Y-%m-%dT%H:%M:%S+01:00"}]]></n:publication_date>
                <n:title><![CDATA[{$opinions[c].title|strip_tags|clearslash}]]></n:title>
                <n:keywords><![CDATA[{$opinions[c].metadata}]]></n:keywords>
              </n:news>
            </url>
        {/section}
        {foreach name=outer item=category from=$categoriesnewsID}
            {foreach key=key item=item from=$category}
            <url>
              <loc><![CDATA[http://{$smarty.const.SITE}{$item.permalink}]]></loc>
              <n:news>
                <n:publication>
                  <n:name><![CDATA[{$smarty.const.SITE_NAME}]]></n:name>
                  <n:language><![CDATA[es]]></n:language>
                </n:publication>
                <n:publication_date><![CDATA[{$item.changed|date_format:"%Y-%m-%dT%H:%M:%S+01:00"}]]></n:publication_date>
                <n:title><![CDATA[{$item.title|strip_tags|clearslash}]]></n:title>
                <n:keywords><![CDATA[{$item.metadata}]]></n:keywords>
              </n:news>
            </url>
            {/foreach}
        {/foreach}
    </urlset>
{elseif preg_match('/sitemap\.php/',$smarty.server.SCRIPT_NAME) && ($smarty.request.action eq "web")}
    <urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9"
            xmlns:news="http://www.google.com/schemas/sitemap-news/0.9">
        <url>
          <loc>http://{$smarty.const.SITE}/</loc>
          <changefreq>daily</changefreq>
        </url>
        {foreach key=k item=v from=$allcategorys}
            {if $v->inmenu ==1 && $v->internal_category ==1}
            <url>
              <loc>http://{$smarty.const.SITE}/seccion/{$v->name}/</loc>
              <changefreq>hourly</changefreq>
            </url>
            {/if}
        {/foreach}
        {foreach name=outer item=category from=$categorieswebID}
            {foreach key=key item=item from=$category}
                <url>
                  <loc>http://{$smarty.const.SITE}{$item.permalink}</loc>
                      <changefreq>hourly</changefreq>
                </url>
            {/foreach}
        {/foreach}
        <url>
          <loc>http://{$smarty.const.SITE}/seccion/opinion/</loc>
          <changefreq>daily</changefreq>
        </url>
        {section name=c loop=$opinions}
            <url>
              <loc>http://{$smarty.const.SITE}{$opinions[c].permalink}</loc>
              <changefreq>hourly</changefreq>
            </url>
        {/section}
        <url>
          <loc>http://{$smarty.const.SITE}/video/</loc>
          <changefreq>daily</changefreq>
        </url>
        {foreach name=outer item=category from=$categoriesVideos}
            {foreach key=key item=item from=$category}
                <url>
                  <loc>http://{$smarty.const.SITE}{$item.permalink}</loc>
                      <changefreq>hourly</changefreq>
                </url>
            {/foreach}
        {/foreach}
        <url>
          <loc>http://{$smarty.const.SITE}/album/</loc>
          <changefreq>daily</changefreq>
        </url>
        {foreach name=outer item=category from=$categoriesGallerys}
            {foreach key=key item=item from=$category}
                <url>
                  <loc>http://{$smarty.const.SITE}{$item.permalink}</loc>
                      <changefreq>hourly</changefreq>
                </url>
            {/foreach}
        {/foreach}
    </urlset>
{else}

    <sitemapindex>
        <sitemap>
            <loc>http://{$smarty.server.SERVER_NAME}/sitemapnews.xml</loc>
        </sitemap>
        <sitemap>
            <loc>http://{$smarty.server.SERVER_NAME}/sitemapweb.xml</loc>
        </sitemap>
    </sitemapindex>
{/if}
