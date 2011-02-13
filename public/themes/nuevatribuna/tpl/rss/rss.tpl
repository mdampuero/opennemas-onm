<?xml version="1.0" encoding="utf-8"?>
<rss version="2.0">
	<channel>
		<title>{$smarty.const.SITE_FULLNAME} :: {$title_rss}</title>
		<link>{$RSS_URL}</link>
		<description>Articulos de {$smarty.const.SITE_FULLNAME} en {$title_rss}</description>
		<lastBuildDate>{$buildDate}</lastBuildDate>
		<generator>Opennemas Framework</generator>
		<category>{$title_rss}</category>

		<image>
            <url>{$params.IMAGE_DIR}logos/nuevatribuna-square.png</url>
			<title>{$smarty.const.SITE_NAME} RSS</title>
			<link>{$smarty.const.SITE_URL}</link>
		</image>
		{if preg_match('/OPINION/',$title_rss)}
			{section name=c loop=$rss}
			<item>
				<title>{$rss[c].title|clearslash}</title>
				<link>{$smarty.const.SITE_URL}{$rss[c].permalink}</link>
				<description><![CDATA[{$rss[c].body|clearslash}]]></description>
				<enclosure url="{$smarty.const.SITE_URL}{$smarty.const.MEDIA_IMG_PATH_WEB}{$rss[c].path_img}" type="image/gif"/>
	       		<guid isPermaLink="true">{$smarty.const.SITE_URL}{$rss[c].permalink}</guid>
	       		<author><![CDATA[{$rss[c].name|clearslash}]]></author>
	       		<pubDate><![CDATA[{$rss[c].created|date_format:"%a, %d %b %Y %H:%M:%S %z"}]]></pubDate>
	    	</item>

	    	{/section}
	    {else}
			{section name=c loop=$rss}
			<item>
				<title>{$rss[c]->title|clearslash}</title>
				<link>{$smarty.const.SITE_URL}{$rss[c]->permalink}</link>
				<description><![CDATA[{$rss[c]->summary|clearslash}]]></description>
	           {foreach from=$photos key=myId item=i}
	              {if $myId == $rss[c]->id}
					<enclosure url="{if "@http:@"|preg_match:$smarty.const.MEDIA_IMG_PATH_WEB}{else}{$smarty.const.SITE_URL}{/if}{$smarty.const.MEDIA_IMG_PATH_WEB}{$i->path_file}{$i->name}" length="{$i->size*1024|string_format:"%d"}" type="image/{$i->type_img}"/>
	              {/if}
	            {/foreach}
	       		<guid isPermaLink="true">{$smarty.const.SITE_URL}{$rss[c]->permalink}</guid>
	       		<author><![CDATA[{$rss[c]->agency|clearslash}]]></author>
	       		<pubDate><![CDATA[{$rss[c]->created|date_format:"%a, %d %b %Y %H:%M:%S %z"}]]></pubDate>
	    	</item>

	    	{/section}
	    {/if}
	</channel>
</rss>
