<?xml version="1.0" encoding="UTF-8"?>

{if preg_match('/sitemap\.php/',$smarty.server.SCRIPT_NAME) && ($smarty.request.action eq "web")}
    <urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9" xmlns:news="http://www.google.com/schemas/sitemap-news/0.9">
        <url>
            <loc>{$smarty.const.SITE_URL}</loc>
            <changefreq>daily</changefreq>
        </url>
        {foreach key=k item=v from=$availableCategories}
            {if $v->inmenu ==1 && $v->internal_category ==1}
                <url>
                    <loc>http://{$smarty.const.SITE}/seccion/{$v->name}/</loc>
                    <changefreq>hourly</changefreq>
                </url>
            {/if}
        {/foreach}
        {foreach name=outer item=category from=$articlesByCategory}
            {foreach key=key item=item from=$category}
                <url>
                    <loc>{$smarty.const.SITE_URL}{generate_uri content_type='article' id=$item.pk_content date=$item.created category_name=$item.catName title=$item.title}</loc>
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
    </urlset>
{elseif preg_match('/sitemap\.php/',$smarty.server.SCRIPT_NAME) && ($smarty.request.action eq "news")}
    <urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9"
        xmlns:news="http://www.google.com/schemas/sitemap-news/0.9">
        {section name=c loop=$opinions}
            <url>
                <loc>{$smarty.const.SITE_URL}{generate_uri   content_type="opinion"
                                                id=$opinions[c].id
                                                date=$opinions[c].changed
                                                title=$opinions[c].title
                                                category_name=$opinions[c].author_name_slug}</loc>
                <news:news>
                    <news:publication>
                        <news:name><![CDATA[{$smarty.const.SITE_NAME}]]></news:name>
                        <news:language><![CDATA[es]]></news:language>
                    </news:publication>
                    <news:genres>OpEd</news:genres>
                    <news:publication_date><![CDATA[{$opinions[c].changed|date_format:"%Y-%m-%dT%H:%M:%S+01:00"}]]></news:publication_date>
                    <news:title><![CDATA[{$opinions[c].title|strip_tags|clearslash}]]></news:title>
                    <news:keywords><![CDATA[{$opinions[c].metadata}]]></news:keywords>
                </news:news>
            </url>
        {/section}
        {foreach name=outer item=category from=$articlesByCategory}
            {foreach key=key item=item from=$category}
            <url>
                <loc>{$smarty.const.SITE_URL}{generate_uri content_type='article' id=$item.pk_content date=$item.created category_name=$item.catName title=$item.title}</loc>
                <news:news>
                    <news:publication>
                        <news:name><![CDATA[{$smarty.const.SITE_NAME}]]></news:name>
                        <news:language<![CDATA[es]]></news:language>
                    </news:publication>
                    <news:genres>PressRelease</news:genres>                    
                    <news:publication_date><![CDATA[{$item.changed|date_format:"%Y-%m-%dT%H:%M:%S+01:00"}]]></news:publication_date>
                    <news:title><![CDATA[{$item.title|strip_tags|clearslash}]]></news:title>
                    <news:keywords><![CDATA[{$item.metadata}]]></news:keywords>
                </news:news>
                </url>
            {/foreach}
        {/foreach}
    </urlset>
{else}
    <sitemapindex>
        <sitemap>
            <loc>{$smarty.const.SITE_URL}sitemapnews.xml</loc>
        </sitemap>
        <sitemap>
            <loc>{$smarty.const.SITE_URL}sitemapweb.xml</loc>
        </sitemap>
    </sitemapindex>
{/if}
