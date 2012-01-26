<tr>
    <td valign=top style="padding:10px;">
        <table>
            <tr>
                <td valign="top">
                    <label for="title">{t}Title:{/t}</label>
                </td>
                <td valign="top">
                    <input type="text"  value="{$video->title|default:""}" id="title" name="title" title="Título de la noticia" class="required" />
                </td>
            </tr>
            <tr>
                <td valign="top">
                    <label for="metadata">{t}Keywords:{/t} <small>{t}Comma separated{/t}</small></label>
                </td>
                <td valign="top">
                    <input type="text" id="metadata" name="metadata"title="Metadatos" value="{$video->metadata|default:""}" class="required" />
                </td>
            </tr>
             <tr>
                <td valign="top">
                    <label for="title">Descripción:</label>
                </td>
                <td valign="top">
                    <textarea name="description" id="description" class="required"
                            title="{t}Video description{/t}">{$video->description|clearslash|default:""}</textarea>
                </td>
            </tr>

            </tr>
            {if isset($video)}

            <tr>
                <td>
                    <label>
                        {t}Preview:{/t}
                    </label>
                </td>
                <td style="width:100%; padding:10px" colspan="2">
                    <div id="video-information" style="text-align:center; margin:0 auto;">
                        {script_tag src="/media/common_assets/fplayer/flowplayer-3.2.6.min.js" external=1}
                        {render_video video=$video height=$height width="400" height="300" base_url=$smarty.const.INSTANCE_MEDIA}
                    </div>

                </td>
            </tr>

            {else}
            <tr>
                <td>
                    <label for="title">Pick a file to upload:</label>
                </td>
                <td colspan="2">
                    <input type="file" name="video_file">
                </td>
            </tr>
            {/if}

        </table>
        <input type="hidden" value="{$video->video_url}" name="video_url" />
        <input type="hidden" value="{json_encode($information)|escape:"html"}" name="information" />
        <input type="hidden" name="author_name" value="internal"/>
    </td>
</tr>