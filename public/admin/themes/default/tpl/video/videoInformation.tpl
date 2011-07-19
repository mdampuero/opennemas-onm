
<table style="width:100%;">

    <tr>
        <td valign="top">
            <label for="title">{t}Title:{/t}</label>
            <input type="text" id="title" name="title" title="Título de la noticia"  onChange="javascript:get_metadata(this.value);"
                 {if (!empty($video->title))} value="{$video->title|clearslash|escape:"html"}"
                 {else} value="{$information['title']|clearslash|escape:"html"}" {/if} class="required" />
        </td>
    </tr>
    <tr>
        <td valign="top">
            <label for="metadata">{t}Keywords:{/t}</label>
            <input type="text" id="metadata" name="metadata"title="Metadatos" value="{$video->metadata}" class="required"  />
            <sub>{t}Comma separated{/t}</sub>
        </td>
    </tr>
     <tr>
        <td>
            <label for="title">Descripción:</label>
            <textarea name="description" id="description" class="required" value=""
                    title="{t}Video description{/t}">{$video->description|clearslash}</textarea>
        </td>
    </tr>
    <tr>
       <td valign="top">
            <label for="title">{t}Service:{/t}</label>
                <input type="text" id="author_name" name="author_name" title="author_name"
                    {if (!empty($video->author_name))} value="{$video->author_name|clearslash|escape:"html"}"
                    {else} value="{$information['service']|clearslash|escape:"html"}" {/if} />
       </td>
    </tr>
    {if (!empty($video->uri))}
    <tr>
       <td valign="top">
            <label for="title">Enlace:</label>         
            <a href="{$smarty.const.SITE_URL}{$video->uri}" target="_blank">
                {$smarty.const.SITE_URL}{$video->uri}
            </a> 
       </td>
    </tr>
    {/if}
    <tr>
       <td valign="top" >
            <label for="title">{t}Other Information{/t}:</label>

            <ul>
                <li> <label>{t}Original Title{/t}:</label> {$information['title']}</li>
                <li> <label>{t}FLV{/t}:</label> {$information['FLV']}</li>
                <li> <label>{t}Download Url{/t}:</label> {$information['downloadUrl']}</li>
                <li> <label>{t}Service{/t}:</label> {$information['service']}</li>
                <li> <label>{t}Duration{/t}:</label> {$information['duration']}</li>
                <li> <label>{t}Url Thumbnail{/t}:</label> {$information['thumbnail']}</li>
                <li> <label>{t}Enbed Url{/t}:</label> {$information['embedUrl']}</li>
           </ul>
           {$information['embedHTML']}
           <input type="hidden" value="{json_encode($information)|escape:"html"}" name="information" />


        </td>
    </tr>

</table>