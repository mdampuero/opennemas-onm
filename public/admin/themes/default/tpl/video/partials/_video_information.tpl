
<table>
    <tr>
        <td>
            <label for="title">{t}Title:{/t}</label>
        </td>
        <td valign="top">
            <input  type="text" id="title" name="title" title="Título de la noticia"  style="width:60%"
                    onChange="javascript:get_metadata(this.value);"
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
            <label for="title">Descripción:</label>
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
            <label for="title">Enlace:</label>
        </td>
        <td valign="top">
            <a href="{$smarty.const.SITE_URL}{$video->uri}" target="_blank">
                {$smarty.const.SITE_URL}{$video->uri}
            </a>
        </td>
    </tr>
    {/if}
    <tr>
        <td colspan=2 style="padding:5px; text-align:left;">
            <label>{t}Preview:{/t}</label>
            <div class="video_player" style="width:80%">
                 {$information['embedHTML']}
            </div>

            <input type="hidden" value="{json_encode($information)|escape:"html"}" name="information" />

            <br>
            <label for="title">{t}Other Information{/t}:</label>
            <table style="width:80%; margin:20xp;">
                <tr>
                    <td>{t}Original Title{/t}</td>
                    <td>{$information['title']}</td>
                </tr>
                <tr>
                    <td>{t}FLV{/t}</td>
                    <td>{$information['FLV']}</td>
                </tr>
                <tr>
                    <td>{t}Download Url{/t}</td>
                    <td>{$information['downloadUrl']}</td>
                </tr>
                <tr>
                    <td>{t}Service{/t}</td>
                    <td>{$information['service']}</td>
                </tr>
                <tr>
                    <td>{t}Duration{/t}</td>
                    <td>{$information['duration']}</td>
                </tr>
                <tr>
                    <td>{t}Url Thumbnail{/t}</td>
                    <td>{$information['thumbnail']}</td>
                </tr>
                <tr>
                    <td>{t}Embed Url{/t}</td>
                    <td>{$information['embedUrl']}</td>
                </tr>
            </table>
        </td>
    </tr>

</table>
