
<table>
    <tr>
        <td>
            <label for="title">{t}Title:{/t}</label>
        </td>
        <td valign="top">
            <input  type="text" id="title" name="title" title="Título de la noticia"  style="width:60%"
                {if (!empty($video->title))}
                    value="{$video->title|clearslash|escape:"html"}"
                {else}
                    value="{$information['title']|clearslash|escape:"html"}"
                {/if}
                class="required" />
        </td>
    </tr>
    <tr>
        <td>
            <label for="metadata">{t}Keywords:{/t} <small>{t}Comma separated{/t}</small></label>
        </td>
        <td valign="top">
            <input type="text" id="metadata" name="metadata"title="Metadatos" value="{$video->metadata|default:""}" class="required"  style="width:70%" />

        </td>
    </tr>
     <tr>
        <td>
            <label for="title">{t}Description{/t}</label>
        </td>
        <td>
            <textarea name="description" id="description" class="required" style="width:70%"
                    title="{t}Video description{/t}">{$video->description|clearslash|default:""}</textarea>
        </td>
    </tr>
    <tr>
        <td>
            <label for="title">{t}Service:{/t}</label>
        </td>
        <td valign="top">
            <input type="text" id="author_name" name="author_name" title="author_name" style="width:70%"
                {if (!empty($video->author_name))} value="{$video->author_name|clearslash|escape:"html"|default:""}"
                {else} value="{$information['service']|clearslash|escape:"html"|default:""}" {/if} />
        </td>
    </tr>
    {if (!empty($video->uri))}
    <tr>
        <td>
            <label for="title">{t}Link:{/t}</label>
        </td>
        <td valign="top">
            <a href="{$smarty.const.SITE_URL}{$video->uri}" target="_blank">
                {$smarty.const.SITE_URL}{$video->uri}
            </a>
        </td>
    </tr>
    {/if}
    <tr>
        <td valign="top">
            <label>{t}Preview:{/t}</label>
        </td>
        <td colspan=2 style="padding:5px; text-align:left;">

            <div class="video_player" style="max-width:500px; overflow:hidden;">
                 {$information['embedHTML']}
            </div>

            <input type="hidden" value="{json_encode($information)|escape:"html"}" name="information" />

        </td>
    </tr>
    <tr>
        <td valign="top">
            <label for="title">{t}Other Information{/t}:</label>
        </td>
        <td>
            <table style="width:80%; margin:20xp;">
                <tr>
                    <td width="100px"><strong>{t}Original Title{/t}</strong></td>
                    <td>{$information['title']}</td>
                </tr>
                <tr>
                    <td><strong>{t}FLV{/t}</strong></td>
                    <td>{$information['FLV']}</td>
                </tr>
                <tr>
                    <td><strong>{t}Download Url{/t}</strong></td>
                    <td>{$information['downloadUrl']}</td>
                </tr>
                <tr>
                    <td><strong>{t}Service{/t}</strong></td>
                    <td>{$information['service']}</td>
                </tr>
                <tr>
                    <td><strong>{t}Duration{/t}</strong></td>
                    <td>{$information['duration']}</td>
                </tr>
                <tr>
                    <td><strong>{t}Url Thumbnail{/t}</strong></td>
                    <td>{$information['thumbnail']}</td>
                </tr>
                <tr>
                    <td><strong>{t}Embed Url{/t}</strong></td>
                    <td>{$information['embedUrl']}</td>
                </tr>
            </table>
        </td>
    </tr>

</table>
