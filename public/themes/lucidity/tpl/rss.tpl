<?xml version="1.0" encoding="utf-8"?>
<rss version="2.0">
	<channel>
		<title> {$smarty.const.SITE_NAME} RSS :: {$title_rss}</title>
		<link>{$RSS_URL}</link>
		<description>{$smarty.const.SITE_NAME} Articles	</description>
		<lastBuildDate>{php} echo date("D, j M Y H:i:s", gmmktime()) . ' GMT'; {/php}</lastBuildDate>
		<generator>{$smarty.const.SITE_NAME} Web</generator>
		<category>{$title_rss}</category>		
		
		<image>
                        <url>{$params.IMAGE_DIR}logo-onm-small.png</url>
			<title>{$smarty.const.SITE_NAME} RSS</title>
			<link>{$SITE_URL}</link>
		</image>
		{if preg_match('/OPINION/',$title_rss)}
			{section name=c loop=$rss}
			<item>
				<title>{$rss[c].title|clearslash}</title>
				<link>{$SITE_URL}{$rss[c].permalink}</link>
				<description><![CDATA[{$rss[c].body|clearslash}]]></description>
				<enclosure url="{$SITE_URL}{$smarty.const.MEDIA_IMG_PATH_WEB}{$rss[c].path_img}" type="image/gif"/>
	       		<guid isPermaLink="true">{$SITE_URL}{$rss[c].permalink}</guid>
	       		<author><![CDATA[{$rss[c].name|clearslash}]]></author>
	       		<pubDate><![CDATA[{$rss[c].created|date_format:"%a, %d %b %Y %H:%M:%S %z"}]]></pubDate>
	    	</item>
	    	
	    	{/section}
	    {else}
			{section name=c loop=$rss}
			<item>
				<title>{$rss[c]->title|clearslash}</title>
				<link>{$SITE_URL}{$rss[c]->permalink}</link>
				<description><![CDATA[{$rss[c]->summary|clearslash}]]></description>
	           {foreach from=$photos key=myId item=i}
	              {if $myId == $rss[c]->id}
					<enclosure url="{$SITE_URL}{$smarty.const.MEDIA_IMG_PATH_WEB}{$i->path_file}{$i->name}" length="{$i->size*1024|string_format:"%d"}" type="image/{$i->type_img}"/>
	              {/if}
	            {/foreach}			
	       		<guid isPermaLink="true">{$SITE_URL}{$rss[c]->permalink}</guid>
	       		<author><![CDATA[{$rss[c]->agency|clearslash}]]></author>
	       		<pubDate><![CDATA[{$rss[c]->created|date_format:"%a, %d %b %Y %H:%M:%S %z"}]]></pubDate>
	    	</item>
	    	
	    	{/section}
	    {/if}
	</channel>
</rss>