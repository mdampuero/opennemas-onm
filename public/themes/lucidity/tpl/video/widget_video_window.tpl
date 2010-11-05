{*
    OpenNeMas project

    @theme      Lucidity
*}


    {if $video->author_name eq 'youtube'}
                <object width="{$width|default:"290"}" height="{$height|default:"180"}">
                    <param name="movie" value="http://www.youtube.com/v/{$video->videoid}&amp;hl=en_US&amp;fs=1&amp;color1=0x3a3a3a&amp;color2=0x999999"></param>
                    <param name="allowFullScreen" value="true"></param><param name="allowscriptaccess" value="always"></param>
                    <embed src="http://www.youtube.com/v/{$video->videoid}&amp;hl=en_US&amp;fs=1&amp;color1=0x3a3a3a&amp;color2=0x999999" type="application/x-shockwave-flash" allowscriptaccess="always" allowfullscreen="true" width="{$width|default:"290"}" height="{$height|default:"180"}">
                    </embed>
                </object>

    {elseif $video->author_name eq 'vimeo'}

            <object width="{$width|default:"290"}" height="{$height|default:"180"}">
                <param value="transparent" name="wmode" />
                <param name="allowfullscreen" value="true" />
                <param name="allowscriptaccess" value="always" />
                <param name="movie" value="http://vimeo.com/moogaloop.swf?clip_id={$video->videoid}&amp;server=vimeo.com&amp;show_title=0&amp;show_byline=0&amp;show_portrait=0&amp;color=ffffff&amp;fullscreen=1" />
                <embed wmode="transparent" src="http://vimeo.com/moogaloop.swf?clip_id={$video->videoid}&amp;server=vimeo.com&amp;show_title=0&amp;show_byline=0&amp;show_portrait=0&amp;color=ffffff&amp;fullscreen=1" type="application/x-shockwave-flash" allowfullscreen="true" allowscriptaccess="always" width="{$width|default:"290"}" height="{$height|default:"180"}"></embed>
            </object>

    {else}
        <div width="{$width|default:"290"}" height="{$height|default:"180"}" style="overflow:hidden;">
            {$video->htmlcode|clearslash}
        </div>
    {/if}
